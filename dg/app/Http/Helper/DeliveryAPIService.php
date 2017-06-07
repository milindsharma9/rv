<?php

namespace App\Http\Helper;

use App\Http\Helper\CommonHelper;
use GuzzleHttp\Client as Client;
use Exception;

class DeliveryAPIService {

    const API_KEY           = "657ee2dc810182d66622007fa1898ae30993067b147aa510bf0c339f271db1b5";
    const TOOKAN_TEAM_ID    = "14067";
    const API_BASE_URL      = "https://api.tookanapp.com/v2/create_task";
    const API_PARTNER_BASE_URL      = "https://api.tookanapp.com/v2/create_task_partner";
    const LEGAL_TEXT                = 'It Is A Legal Requirement To Age Verify The Customer. In Accepting This Order, You Accept Our Courier Terms And Conditions As Set Out On Www.Alchemywings.Co';
    const PICKUP_TEMPLATE_NAME      = 'Pickup_new_test_TC';
    

    /**
     * Method to create pickup ticket
     *
     * @param array $aParam
     * @return array
     */
    public static function createTask($aParam, $taskType) {
        $response = array(
            'status' => FALSE,
            'message' => trans('messages.common_error'),
            'data' => array()
        );
        try {
            $aParam['api_key']  = self::API_KEY;
            $aParam['team_id']  = self::TOOKAN_TEAM_ID;
            $aParam['pickup_custom_field_template']  = self::PICKUP_TEMPLATE_NAME;
            //$aParam['job_description']  = self::LEGAL_TEXT;
            $client             = new Client();
            $apiBaseUrl         = self::API_BASE_URL;
            if ($taskType == \App\PartnerAvailability::TASK_PARTNER) {
                $apiBaseUrl = self::API_PARTNER_BASE_URL;
            }
            $url                = $apiBaseUrl;
            $aHeaders = array(
                'Content-Type' => 'application/json',
            );
            $requestParams = array(
                'headers'   => $aHeaders,
                'json'      => $aParam,
            );
            $res = $client->request('POST', $url, $requestParams);
            if ($res->getStatusCode() == '200') {
                $responseBody = json_decode($res->getBody(), true);
                if (isset($responseBody['status'])
                        && $responseBody['status'] == '200') {
                    $response['status']     = TRUE;
                    $response['data']       = $responseBody;
                    $response['message']    = 'API Success.';
                } else {
                    $response['message'] = isset($responseBody['message']) ? $responseBody['message'] : "API ERROR";
                }
            } else {
                CommonHelper::event('Tookan Bad request| ' . $aParam['order_id'], CommonHelper::LOGISTICS_API_LOG_FILE, CommonHelper::DAILY);
            }
        } catch (Exception $ex) {
            CommonHelper::event($ex->getMessage() . "|" . $ex->getFile() . "|" . $ex->getLine(). "|" . $aParam['order_id'], CommonHelper::LOGISTICS_API_LOG_FILE, CommonHelper::DAILY);
        }
        return $response;
    }

}
