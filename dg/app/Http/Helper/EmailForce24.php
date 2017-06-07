<?php

/* 
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Http\Helper;

use App\Http\Helper\CommonHelper;
use GuzzleHttp\Client as Client;
use Exception;

class EmailForce24 {
    
    const API_VERSION                           = "V4_1";
    const API_BASE_URL                          = "https://beta-api.data-crypt.com";
    const API_DEV_BASE_URL                      = "https://beta-api.data-crypt.com";
    const VENDOR_REGISTRATION                   = 'vendor_registration';
    const ORDER_CONFIRMATION                    = 'order_confirmation';
    const ORDER_CANCEL                          = 'order_cancel';
    const PASSWORD_RESET                        = 'password_reset';
    
    /**
     * This function call Force24 api and generate token.
     * 
     * @return string
     */
    private static function _generateAPIToken() {
        $response   = array(
            'status'    => false,
            'message'   => trans('messages.common_error'),
            'data'      => array()
        );
        $bodyHeaders    = array(
            'grant_type'    => 'password',
            'username'      => env('F24_USERNAME'),
            'password'      => env('F24_PASSWORD'),
        );
        try {
            $client         = new Client();
            $forceEnv       = env('F24_ENV');
            $apiBaseUrl     = self::API_DEV_BASE_URL;
            if ($forceEnv == 'PROD') {
                $apiBaseUrl     = self::API_BASE_URL;
            }
            $apiEndpoint    = $apiBaseUrl . "/token";
            $res            = $client->request('POST', $apiEndpoint, ['form_params' => $bodyHeaders]);
            if ($res->getStatusCode() == '200') {
                $responseBody           = json_decode($res->getBody(), true);
//                @todo Handle response in another way.
                $response['status']     = true;
                $response['data']       = $responseBody;
                $response['message']    = 'Token fetched Successfully';
            } else {
                CommonHelper::event('cannot find token', CommonHelper::FORCE_EMAIL_LOG_FILE, CommonHelper::DAILY);
            }
        } catch (\GuzzleHttp\Exception\ClientException $cex) {
            CommonHelper::event($cex->getMessage() . "|" . $cex->getFile() . "|" . $cex->getLine(), CommonHelper::FORCE_EMAIL_LOG_FILE, CommonHelper::DAILY);
        } catch (Exception $ex) {
            CommonHelper::event($ex->getMessage() . "|" . $ex->getFile() . "|" . $ex->getLine(), CommonHelper::FORCE_EMAIL_LOG_FILE, CommonHelper::DAILY);
        }
        return $response;
    }

    /**
     * Function sends email to user via force24.
     * 
     * @param array $data
     * @param string $templateName
     * @return array
     */
    public static function sendEmailAPI(array $data, $templateName) {
         $response  = array(
            'status'    => FALSE,
            'message'   => trans('messages.common_error'),
            'data'      => array()
        );
        try {
            $client         = new Client();
            $token          = '';
            $tokenData      = self::_generateAPIToken();
            if ($tokenData['status']) {
                $token      = $tokenData['data']['access_token'];
            } else {
                $response['message']   = 'cannot find token';
                CommonHelper::event('cannot find token', CommonHelper::FORCE_EMAIL_LOG_FILE, CommonHelper::DAILY);
                return $response;
            }
            $endPointUrl    = self::_getAPIUrl($templateName);
            $url            = self::API_BASE_URL . $endPointUrl;
            $aHeaders   = array(
                'Content-Type'  => 'application/json',
                'Authorization' => 'Bearer ' . $token,
            );
            $requestParams  = array(
                'headers'       => $aHeaders,
                'json'          => $data,
            );
            if ($token != '') {
                $res        = $client->request('POST', $url, $requestParams);
                if ($res->getStatusCode() == '200') {
                    $responseBody           = json_decode($res->getBody(), true);
                    $response['status']     = true;
                    $response['data']       = $responseBody;
                    $response['message']    = 'Email sent successfully.';
//                    @todo Handle response in another way.
//                    if (isset($responseBody['status']) && $responseBody['status']) {
//                        if ($responseBody['response']['status']) {
//                            $response['status'] = true;
//                            $response['data']['mapping'] = $responseBody['response']['data']['mapping'];
//                        } else {
//                            $response['message'] = $responseBody['response']['message'];
//                        }
//                    } else {
//                        $response['message'] = $responseBody['message'];
//                    }
                } else {
                    CommonHelper::event('Bad request', CommonHelper::FORCE_EMAIL_LOG_FILE, CommonHelper::DAILY);
                }
            }
        } catch (Exception $ex) {
            CommonHelper::event($ex->getMessage() . "|" . $ex->getFile() . "|" . $ex->getLine(), CommonHelper::FORCE_EMAIL_LOG_FILE, CommonHelper::DAILY);
        }
        return $response;
    }

    /**
     * Function will return endpoint url on the basis of template name provided.
     * 
     * @param string $name
     * @return string
     */
    private static function _getAPIUrl($name) {
        $apiUrl = '';
        switch ($name) {
            case self::VENDOR_REGISTRATION :
                    $apiUrl = '/api/alchemy-wings/vendor-registration';
                break;
            
            case self::ORDER_CONFIRMATION :
                    $apiUrl = '/api/alchemy-wings/order-confirmation';
                break;
            
            case self::ORDER_CANCEL :
                    $apiUrl = '/api/alchemy-wings/order-cancel';
                break;
            
            case self::PASSWORD_RESET :
                    $apiUrl = '/api/alchemy-wings/password-reset';
                break;

            default:
                    $apiUrl = '';
                break;
        }
        return $apiUrl;
    }
}