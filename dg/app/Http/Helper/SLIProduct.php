<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

namespace App\Http\Helper;

use App\Http\Helper\CommonHelper;
use GuzzleHttp\Client as Client;
use Illuminate\Http\Request;
use Exception;
use Auth;

class SLIProduct {

    const API_VERSION           = "V4_1";
    const API_BASE_URL          = "https://alchemywings.resultspage.com/search?";
    const API_DEV_BASE_URL      = "https://alchemywings.resultsdemo.com/search?";
    const PASSWORD_RESET        = 'password_reset';
    const STANDARD_QUERY        = 'standard-query';
    const SUGGESTION_QUERY      = 'suggestion-query';
    const RELATED_SEARCH_QUERY  = 'related-search-query';
    const LOGGING_REQUEST       = 'logging-request';
    const RESULT_COUNT          = '20';

    /**
     * Return Url parameter to be used for search.
     * 
     * @param string $queryType
     * @return string
     */
    private static function _getQueryType($queryType = self::STANDARD_QUERY) {
        $urlParam = '';
        switch ($queryType) {
            case self::STANDARD_QUERY:
                $urlParam = 'p=Q';

                break;
            case self::SUGGESTION_QUERY:
                $urlParam = 'p=KK';

                break;
            case self::RELATED_SEARCH_QUERY:
                $urlParam = 'p=UK';

                break;
            case self::LOGGING_REQUEST:
                $urlParam = 'p=LG';

                break;
            default:
                $urlParam = 'p=Q';
                break;
        }
        return $urlParam;
    }
    
    /**
     * Return search query parameters.
     * 
     * @param Request $request
     * @param string $searchKeyWord
     * @param string $queryType
     * @return string
     */
    private static function _getSearchParamsUrl(Request $request, $searchKeyWord, $queryType, $aParam) {
        $userId         = isset(Auth::user()['id']) ? 'uid=' . Auth::user()['id'] : '';
        $userIp         = 'cip=' . $request->ip();
        $userAgent      = 'ua=' . urlencode($request->header('User-Agent'));
        $referenceUrl   = 'ref=' . urlencode(url()->full());
        $searchWord     = 'w='. urlencode($searchKeyWord);
        //$count          = 'cnt='.  self::RESULT_COUNT; 
        $count          = 'cnt='.  (!empty($aParam) && isset ($aParam['result_count'])? $aParam['result_count']: self::RESULT_COUNT);
        $offset         = 'srt='. (!empty($aParam) && isset ($aParam['offset'])? $aParam['offset']: 0);
        $urlParams = [$searchWord, self::_getQueryType($queryType), 'ts=json-full', $userId, $userIp, $referenceUrl, $userAgent, $count, $offset];
        foreach ($urlParams as $key => $value) {
            if ($value != '') {
                unset($key);
            }
        }
        $urlParams = implode('&', $urlParams);
        return $urlParams;
    }

    /**
     * Metthod which returns APi Results.
     * 
     * @param Request $request
     * @param string $searchKeyWord
     * @param string $queryType
     * @return array
     */
    public static function searchSLIAPI(Request $request, $searchKeyWord, $queryType, $aParam) {
        $response = array(
            'status' => FALSE,
            'message' => trans('messages.common_error'),
            'data' => array()
        );
        try {
            $client = new Client();
            $token = 'YWxjaGVteXdpbmdzOnJvbWluZXNj';
            $forceEnv = env('SLI_ENV');
            $apiBaseUrl = self::API_DEV_BASE_URL;
            if ($forceEnv == 'PROD') {
                $apiBaseUrl = self::API_BASE_URL;
            }
            $urlParams = self::_getSearchParamsUrl($request, $searchKeyWord, $queryType, $aParam);
            $url = $apiBaseUrl . $urlParams;
            $url = $url.'&af=pagetype:products';
            //CommonHelper::event('url : '. $url, CommonHelper::SLI_API_LOG_FILE, CommonHelper::DAILY);
            $aHeaders = array(
                'Content-Type' => 'application/json',
                'Authorization' => 'Basic ' . $token,
                'auth' => [env('SLI_USERNAME'), env('SLI_PASSWORD')],
            );
            $requestParams = array(
                'headers' => $aHeaders,
            );
            if ($token != '') {
                $res = $client->request('GET', $url, $requestParams);
                if ($res->getStatusCode() == '200') {
                    $responseBody = json_decode($res->getBody(), true);
                    $response['status'] = TRUE;
                    $response['data'] = $responseBody;
                    $response['message'] = 'Data fetched successfully.';
                } else {
                    CommonHelper::event('Bad request', CommonHelper::SLI_API_LOG_FILE, CommonHelper::DAILY);
                }
            }else {
                    CommonHelper::event('token missing', CommonHelper::SLI_API_LOG_FILE, CommonHelper::DAILY);
            }
        } catch (Exception $ex) {
            CommonHelper::event($ex->getMessage() . "|" . $ex->getFile() . "|" . $ex->getLine(), CommonHelper::SLI_API_LOG_FILE, CommonHelper::DAILY);
        }
        return $response;
    }
    
    /**
     * Method used to search the products.
     * 
     * @param Request $request
     * @param string $searchKeyWord
     * @param string $queryType
     * @return Array
     */
    public static function getSLIProducts(Request $request, $searchKeyWord, $queryType = self::STANDARD_QUERY, $aParam = []) {
        $response = array(
            'status' => FALSE,
            'message' => trans('messages.common_error'),
            'data' => array(
                'results' => array()
            )
        );
        try {
            $apiResponse = self::searchSLIAPI($request, $searchKeyWord, $queryType, $aParam);
            if ($apiResponse['status'] == TRUE) {
                $response['status']                 = TRUE;
                $response['message']                = 'Data Fetched Successfully';
                $response['data']['suggestions']    = isset($apiResponse['data']['suggestions']) ? $apiResponse['data']['suggestions'] : array();
                $response['data']['results']        = isset($apiResponse['data']['results']) ? $apiResponse['data']['results'] : array();
                $response['data']['totalResult']    = $apiResponse['data']['result_meta'];
                $response['data']['paginationData'] = $apiResponse['data']['pages'];
            }
        } catch (Exception $ex) {
            CommonHelper::event($ex->getMessage() . "|" . $ex->getFile() . "|" . $ex->getLine(), CommonHelper::SLI_API_LOG_FILE, CommonHelper::DAILY);
        }
        return $response;
    }
    
    /**
     * Metthod which returns APi Results.
     * 
     * @param Request $request
     * @param string $searchKeyWord
     * @param string $queryType
     * @return array
     */
    public static function _searchSLIBlogAPI(Request $request, $searchKeyWord, $queryType, $aParam = []) {
        $response = array(
            'status' => FALSE,
            'message' => trans('messages.common_error'),
            'data' => array()
        );
        try {
            $client = new Client();
            $token = 'YWxjaGVteXdpbmdzOnJvbWluZXNj';
            $forceEnv = env('SLI_ENV');
            $apiBaseUrl = self::API_DEV_BASE_URL;
            if ($forceEnv == 'PROD') {
                $apiBaseUrl = self::API_BASE_URL;
            }
            $urlParams = self::_getSearchParamsUrl($request, $searchKeyWord, $queryType, $aParam);
            $url = $apiBaseUrl . $urlParams;
            $url = $url.'&af=pagetype:'.$aParam['pagetype'];
            //CommonHelper::event('urlblog : '. $url, CommonHelper::SLI_API_LOG_FILE, CommonHelper::DAILY);
            $aHeaders = array(
                'Content-Type' => 'application/json',
                'Authorization' => 'Basic ' . $token,
                'auth' => [env('SLI_USERNAME'), env('SLI_PASSWORD')],
            );
            $requestParams = array(
                'headers' => $aHeaders,
            );
            if ($token != '') {
                $res = $client->request('GET', $url, $requestParams);
                if ($res->getStatusCode() == '200') {
                    $responseBody = json_decode($res->getBody(), true);
                    $response['status'] = TRUE;
                    $response['data'] = $responseBody;
                    $response['message'] = 'Data fetched successfully.';
                } else {
                    CommonHelper::event('Bad request', CommonHelper::SLI_API_LOG_FILE, CommonHelper::DAILY);
                }
            }else {
                    CommonHelper::event('token missing', CommonHelper::SLI_API_LOG_FILE, CommonHelper::DAILY);
            }
        } catch (Exception $ex) {
            CommonHelper::event($ex->getMessage() . "|" . $ex->getFile() . "|" . $ex->getLine(), CommonHelper::SLI_API_LOG_FILE, CommonHelper::DAILY);
        }
        return $response;
    }
    
    public static function searchSLIBlogAPI(Request $request, $searchKeyWord, $queryType, $aParam = []) {
        $response = array(
            'blog' => array(),
            'place' => array(),
            'event' => array(),
        );
        $apiResponse = self::_searchSLIBlogAPI($request, $searchKeyWord, $queryType, $aParam = ['pagetype' => 'blog', 'result_count' => 2]);
        //CommonHelper::event($apiResponse, CommonHelper::SLI_API_LOG_FILE, CommonHelper::DAILY);
        if ($apiResponse['status'] == TRUE) {
            $response['blog']['suggestions']    = isset($apiResponse['data']['suggestions']) ? $apiResponse['data']['suggestions'] : array();
            $response['blog']['results']        = isset($apiResponse['data']['results']) ? $apiResponse['data']['results'] : array();
            $response['blog']['totalResult']    = $apiResponse['data']['result_meta'];
            $response['blog']['paginationData'] = $apiResponse['data']['pages'];
        }
        $apiResponse = self::_searchSLIBlogAPI($request, $searchKeyWord, $queryType, $aParam = ['pagetype' => 'place', 'result_count' => 2]);
        //CommonHelper::event($apiResponse, CommonHelper::SLI_API_LOG_FILE, CommonHelper::DAILY);
        if ($apiResponse['status'] == TRUE) {
            $response['place']['suggestions']    = isset($apiResponse['data']['suggestions']) ? $apiResponse['data']['suggestions'] : array();
            $response['place']['results']        = isset($apiResponse['data']['results']) ? $apiResponse['data']['results'] : array();
            $response['place']['totalResult']    = $apiResponse['data']['result_meta'];
            $response['place']['paginationData'] = $apiResponse['data']['pages'];
        }
        $apiResponse = self::_searchSLIBlogAPI($request, $searchKeyWord, $queryType, $aParam = ['pagetype' => 'event', 'result_count' => 2]);
        //CommonHelper::event($apiResponse, CommonHelper::SLI_API_LOG_FILE, CommonHelper::DAILY);
        if ($apiResponse['status'] == TRUE) {
            $response['event']['suggestions']    = isset($apiResponse['data']['suggestions']) ? $apiResponse['data']['suggestions'] : array();
            $response['event']['results']        = isset($apiResponse['data']['results']) ? $apiResponse['data']['results'] : array();
            $response['event']['totalResult']    = $apiResponse['data']['result_meta'];
            $response['event']['paginationData'] = $apiResponse['data']['pages'];
        }
        //CommonHelper::event($response, CommonHelper::SLI_API_LOG_FILE, CommonHelper::DAILY);
        return $response;
    }

}
