<?php

namespace App\Http\Helper;


use DateTime;
use Illuminate\Support\Facades\Config;
use GuzzleHttp\Client as Client;
use Exception;
use App\StoreAddress;
use App\StoreModel;
use Illuminate\Support\Facades\Auth;
use App\Configurations;
use Illuminate\Support\Facades\Cache;

class CommonHelper
{    
    const DAILY                 = 'd';
    const HOURLY                = 'h';
    const MONTHLY               = 'm';
    const SINGLE                = 's';
    const ORDER_LOG_FILE                = 'order';
    const PAYMENT_LOG_FILE              = 'payment';
    const PAYMENT_BANK_LOG_FILE         = 'bank_account';
    const PAYMENT_PAYOUT_LOG_FILE       = 'payout';
    const USER_REGISTER_LOG_FILE        = 'register';
    const POSTCODEAPI_LOG_FILE          = 'postcode_api';
    const DELIVERY_POSTCODE_LOG_FILE    = 'delivery_postcode';
    const PAYMENT_REGISTER_LOG_FILE     = 'payment_users';

    const PAYMENT_KYC_LOG_FILE          = 'kyc_users';
    const VALIDATE_PHONE_LOG_FILE       = 'validate_phone';
    const EMAIL_FAILURE_LOG_FILE        = 'order_email';
    const COUPON_LOG_FILE               = 'coupon';
    const PAYMENT_REFUND_LOG_FILE       = 'payment_refund';
    const ORDER_STATUS_LOG_FILE         = 'order_status';
    const FULLFILLMENTAPI_LOG_FILE      = 'fullfillment_api';
    const CHECKOUT_LOG_FILE             = 'checkout_api';
    const FORCE_EMAIL_LOG_FILE          = 'force_email_api';
    const SLI_API_LOG_FILE              = 'sli_api';
    const LOGISTICS_API_LOG_FILE        = 'logistics_tookan';
    
    public static function formatPrice($price) {
        $price = number_format((float)$price, 2, '.', '');
        return $price;
    }
    
    public static function getCurrentDateTime() {
        return new DateTime;
    }

    /**
     * Get GDA/RDA value.
     * @param type $type
     * @param type $value
     * @return type
     */
    public static function getMacroDetails($type, $value) {
        $multiplier = 0;
        switch ($type) {
            case 'energy':
                    $multiplier = Config::get('appConstants.energy');

                break;
            case 'fat':
                    $multiplier = Config::get('appConstants.fat');

                break;
            case 'sat_fat':
                    $multiplier = Config::get('appConstants.sat_fat');

                break;
            case 'sugar':
                    $multiplier = Config::get('appConstants.sugar');

                break;
            case 'salt':
                    $multiplier = Config::get('appConstants.salt');

                break;

            default:
                break;
        }
        
        if($multiplier){
            return number_format(($value*100)/$multiplier,1).'%';
        }
        return ('0.0%');
        
    }

    public static function event($message, $fileName = 'event', $type) {
        switch ($type) {
            case self::DAILY:
                $fileName = $fileName . "_" . date('Y-m-d');
                break;
            
            case self::HOURLY:
                $fileName = $fileName . "_" . date('Y-m-d_h');
                break;
            
            case self::MONTHLY:
                $fileName = $fileName . "_" . date('Y-m');
                break;

            default:
                 $fileName = $fileName;
                break;
        }
        $filePath   = storage_path();
        $fileName   = $filePath."/logs/".$fileName .".log";
        $mode       = "a";
        $fh         = fopen($fileName, $mode);
        $message    = self::_prepare($message);
        $message    = date('Y-m-d h:i:s') . "--" . $message . PHP_EOL;
        fwrite($fh, $message);
        fclose($fh);
    }
    
    /**
     * Convert incoming log object to string
     * @param mixed $obj
     * @return string
     */
    protected static function _prepare($obj)
    {
        if (false === is_scalar($obj)) {
            $string = self::_dump($obj);
        } else if (is_numeric($obj)) {
            $string = (string) $obj;
        } else {
            $string = $obj;
        }
        return $string;
    }
    
    protected static function _dump($var)
    {
        // var_export the variable into a buffer and keep the output
        $output = var_export($var, true);
        // neaten the newlines and indents
        $output = preg_replace("/\]\=\>\n(\s+)/m", "] => ", $output);
        return $output;
    }

    /**
     * Method to return trimmed description as required on listing pages
     *
     * @param string $description Product Description
     * @return string $formattedDescription
     */
    public static function formatProductDescription($description) {
        $defaultLength = \Config::get('appConstants.product_desc_default_length');
        if (strlen($description) > $defaultLength) {
            $formattedDescription = substr($description, 0, $defaultLength);
            $formattedDescription = $formattedDescription . "...";
        } else {
            $formattedDescription = $description;
        }
        return $formattedDescription;
    }

    /**
     * Method to get Product Image Path
     *
     * @param int $id Product Barcode
     * @param boolean $isThumb Tells whether thumb is required or main image
     * @param boolean $checkDefault Check for deafult or not
     * @return string Product Image Path
     *
     */
    /*public static function getProductImage($id, $isThumb = false, $checkDefault = true) {
        $imageBasePath              =  url('alchemy/images/product-images');
        $productImageServerPath     = public_path()."/alchemy/images/product-images";
        if ($isThumb) {
            $imageBasePath = $imageBasePath ."/thumb";
            $productImageServerPath = $productImageServerPath ."/thumb";
        }
        $productImage       = $imageBasePath . "/".$id.".png";
        $productImageServer = $productImageServerPath . "/".$id.".png";
        if (!file_exists($productImageServer) && $checkDefault) {
            $productImagePath = $imageBasePath . "/product_default.png";
        } else {
            $productImagePath = $productImage;
        }
        return $productImagePath;
    }*/

    public static function getProductImage($id, $isThumb = false, $isMultiple = false) {
        $imageName          = 'product_default.png';
        $image              = self::getProductImageAbsolutePath($imageName, $isThumb);
        if ($isMultiple) {
            $image = array(
                array(
                    'image' => $image
                )
            );
        }
        $productImageData   = Cache::get('product_images_cache');
        if ($isMultiple) {
            $image =  array(
                array(
                    'image' => $imageName,
                )
            );
        }
        if (isset($productImageData[$id])) {
            $aProductImages = $productImageData[$id];
            if (!empty($aProductImages)) {
                if ($isMultiple) {
                    $onlyProductImages = [];
                    foreach ($aProductImages as $key => $value) {
                        if($value['thumb'] != '1'){
                            $onlyProductImages[] = $value;
                        }
                    }
                    return $onlyProductImages;
                } else {
                    $imageName  = $aProductImages[0]['image'];
                    foreach ($aProductImages as $key => $value) {
                        if($value['thumb'] == '1'){
                            $imageName  = $aProductImages[$key]['image'];
                            $isThumb    = FALSE;
                            break;
                        }
                    }
                    $image      = self::getProductImageAbsolutePath($imageName, $isThumb);
                }
            } else {
                if ($isMultiple) {
                    return array(
                        array(
                            'image' => $imageName,
                        ),
                    );
                }
            }
        }
        return $image;
    }

    public static function getProductImageAbsolutePath($imageName, $isThumb = false) {
        $imageBasePath              =  url('uploads/product_images/');
        $productImageServerPath     = public_path()."/uploads/product_images/";
        if ($isThumb) {
            $imageBasePath          = $imageBasePath ."/thumb";
            $productImageServerPath = $productImageServerPath ."/thumb";
        }
        $productImage       = $imageBasePath . "/". $imageName;
        $productImageServer = $productImageServerPath . "/". $imageName;
        if (!file_exists($productImageServer)) {
            $productImagePath = $imageBasePath . "/product_default.png";
        } else {
            $productImagePath = $productImage;
        }
        return $productImagePath;
    }

    /**
     * Method to get Bundle Image Path
     *
     * @param string $image Image name
     * @param boolean $isThumb Tells whether thumb is required or main image
     * @return string Bundle Image Path
     *
     */
    public static function getBundleImage($image, $isThumb = false) {
        $imageBasePath              =  url('uploads/bundles');
        $bundleImageServerPath      = public_path()."/uploads/bundles";
        if ($isThumb) {
            $imageBasePath          = $imageBasePath;
            $bundleImageServerPath  = $bundleImageServerPath;
        }
        $bundleImage       = $imageBasePath . "/".$image;
        $bundleImageServer = $bundleImageServerPath . "/".$image;
        if (!empty($image) && file_exists($bundleImageServer)) {
            $bundleImagePath = $bundleImage;
        } else {
            if ($isThumb) {
                $imageBasePath          = $imageBasePath ."/thumb";
                $bundleImageServerPath  = $bundleImageServerPath ."/thumb";
            }
            $bundleImage       = $imageBasePath . "/".$image;
            $bundleImageServer = $bundleImageServerPath . "/".$image;
            if (!empty($image) && file_exists($bundleImageServer)) {
                $bundleImagePath = $bundleImage;
            } else{
                $bundleImagePath = $imageBasePath . "/bundle_default.png";
            }
        }
        return $bundleImagePath;
    }

    /**
     * Method to return Unique Id for not logged in users while adding data in CART
     */
    public static function generateUniqueIdForCart() {
        return uniqid("Cart-", true);
    }

    /**
     * Method to get LatLng from External API
     *
     * @param string $postCode Description
     * @return array response Array containg Lat Long
     *
     */
    public function getLatLngFromPostCode($postCode) {
        $response = array(
            'status'    => false,
            'message'   => '',
            'data'      => array(
                'lat' => '',
                'lng' => ''
            )
        );
        try {
            $client = new Client();
            $res    = $client->request('GET', 'http://api.postcodes.io/postcodes/'.$postCode);
            if($res->getStatusCode() == '200') {
                $responseBody = json_decode($res->getBody(), true);
                if (isset($responseBody['result'])
                        && isset($responseBody['result']['latitude'])) {
                    $response['data']['lat'] = $responseBody['result']['latitude'];
                    $response['data']['lng'] = $responseBody['result']['longitude'];
                    $response['status'] = true;
                }
            } else {
                $response['message'] = $responseBody['error'];
            }
        } catch (Exception $ex) {
            $response['message'] = $ex->getMessage();
            self::event($ex->getMessage() . "|" . $ex->getLine(), self::POSTCODEAPI_LOG_FILE, self::DAILY);
        }
        return $response;
    }

    /**
     * Wrapper Helper method to get Near Stores
     *
     * @return array $stores Id's of Stores in given radius
     */
    public static function getNearByStores() {
        $stores = array();
        try {
            $addressModel   = new StoreAddress();
            $userDeliveryDetails = self::getUserDeliveryPostcodeData();
            if ($userDeliveryDetails['status']) {
                $postCodeData   = $userDeliveryDetails['data'];
                $lat            = $postCodeData['lat'];
                $lng            = $postCodeData['lng'];
                $postCode       = $postCodeData['postcode'];
                $stores         = $addressModel->getNearbyStores($lat, $lng, $postCode);
            }
        } catch (Exception $ex) {
            self::event($ex->getMessage() . "|" . $ex->getLine(), self::DELIVERY_POSTCODE_LOG_FILE, self::DAILY);
        }
        return $stores;
    }

    /**
     * Helper method to get User Delivery Postcode data (set in session)
     *
     * @return array $response Current user's delivery postcode & Lat Lng Details
     */
    public static function getUserDeliveryPostcodeData() {
        $response = array(
            'status'    => false,
            'message'   => '',
            'data'      => array(
                'postcode' => '',
                'lat' => '',
                'lng' => '',
            )
        );
        // Obselete Now as per new changes @todo Remove after proper testing
        $cartDeliveryPostcodeSessionKey = \Config::get('appConstants.user_delivery_postcode_session_key');
        $cartDeliveryLatSessionKey      = \Config::get('appConstants.user_delivery_lat_session_key');
        $cartDeliveryLngSessionKey      = \Config::get('appConstants.user_delivery_lng_session_key');
        $postCode                       = session()->get($cartDeliveryPostcodeSessionKey, NULL);
        $deliveryLat                    = session()->get($cartDeliveryLatSessionKey, NULL);
        $deliveryLng                    = session()->get($cartDeliveryLngSessionKey, NULL);
        if (!empty($postCode)
            && !empty($deliveryLat)
            && !empty($deliveryLng)
        ) {
            $response['status']                 = true;
            $response['data']['postcode']       = $postCode;
            $response['data']['lat']   = $deliveryLat;
            $response['data']['lng']   = $deliveryLng;
        } else {
            $userLandingPostcodeSessionKey = \Config::get('appConstants.user_landing_postcode_session_key');
            $userLandingLatSessionKey      = \Config::get('appConstants.user_landing_lat_session_key');
            $userLandingLngSessionKey      = \Config::get('appConstants.user_landing_lng_session_key');
            $landingPostCode   = session()->get($userLandingPostcodeSessionKey, NULL);
            $landingLat        = session()->get($userLandingLatSessionKey, NULL);
            $landingLng        = session()->get($userLandingLngSessionKey, NULL);
            if (!empty($landingPostCode)
                && !empty($landingLat)
                && !empty($landingLng)
            ) {
                $response['status']                 = true;
                $response['data']['postcode']       = $landingPostCode;
                $response['data']['lat']            = $landingLat;
                $response['data']['lng']            = $landingLng;
            }
        }

        // API case, PostCode & lat Lng sent in headers.
        $request    = app('request');
        $postCode   = $request->header('user-pincode', '');
        $lat        = $request->header('user-lat', '');
        $lng        = $request->header('user-lng', '');
        if (!empty($postCode)
            && !empty($lat)
            && !empty($lng)
        ) {
            $response['status']                 = true;
            $response['data']['postcode']       = $postCode;
            $response['data']['lat']            = $lat;
            $response['data']['lng']            = $lng;
        }
        if (!$response['status']) {
            $response['message'] = trans('messages.delivery_postcode_set_error');
            self::event($response['message'], self::DELIVERY_POSTCODE_LOG_FILE, self::DAILY);
        }
        return $response;
    }

    /**
     * Wrapper Helper method to get Near Stores for particular Order
     * Used for Admin Interface
     * Used in landing page for validating postcode
     * Used in API Call, Also returns store distance in certain condition
     *
     * @param string $postCode
     * @param decimal $lat
     * @param decimal $lng
     * @return array $stores Id's of Stores in given radius
     *
     */
    public static function getNearByStoresForOrder($postCode, $lat, $lng, $storeDetails = false) {
        $stores = array();
        try {
            $addressModel   = new StoreAddress();
            if (!empty($postCode)
                && !empty($lat)
                && !empty($lng)
            ) {
                $stores         = $addressModel->getNearbyStoresWithDistance($lat, $lng, $postCode);
                // Exitsing functions expects only Store Id, where as in some case store distance is also needed.
                if (!$storeDetails && !empty($stores)) {
                    $stores = array_keys($stores);
                }
            }
        } catch (Exception $ex) {
            self::event($ex->getMessage() . "|" . $ex->getLine(), self::DELIVERY_POSTCODE_LOG_FILE, self::DAILY);
        }
        return $stores;
    }

    /**
     * Method to check whether near By check will be applicable while showing product listing.
     * Checks two different session set on landing page / Cart Page
     *
     * @return boolean
     *
     */
    public static function checkForNearProducts() {
        $cartDeliveryPostcodeSessionKey = \Config::get('appConstants.user_delivery_postcode_session_key');
        $landingPagePostcodeSessionKey  = \Config::get('appConstants.user_landing_postcode_session_key');
        $landingPostcode    = session()->get($landingPagePostcodeSessionKey, '');
        $cartPostcode       = session()->get($cartDeliveryPostcodeSessionKey, '');
        if (!empty($landingPostcode)
            || !empty($cartPostcode)
        ) {
            return true;
        }
        return false;
    }
    
    /**
     * Wrapper Helper method to get Site's opening time
     * Used for Admin Interface
     *    
     * @return boolean
     *
     */
    public static function checkForSiteTimings() {
        $addressModel = new StoreAddress();
        $open_close = $addressModel->checkSiteTimings();
        if (!empty($open_close)) {
            return false;
        }
        return true;
    }

    /**
     * Method to validate Phone from External API
     *
     * @param string $phoneNumber Description
     * @return array response Array containg Phone validation status
     *
     */
    public static function validatePhoneNumber($phoneNumber) {
        $response = array(
            'status'    => false,
            'message'   => '',
        );
        try {
            $phonePrefix    = substr($phoneNumber, 0, 2);
            if ($phonePrefix == '44') {
                $phoneNumber = substr($phoneNumber , 2);
            }
            $phonePrefix    = substr($phoneNumber, 0, 1);
            if ($phonePrefix != '0') {
                $phoneNumber = '0'. $phoneNumber;
            }
            $clientId       = env('PHONE_VALIDATE_API_KEY', '');
            $countryCode    = config('appConstants.phone_validate_default_country');
            $client = new Client();
            $res    = $client->request('GET', 'http://apilayer.net/api/validate'
                    . '?access_key='.$clientId
                    . '&country_code='.$countryCode
                    . '&format=1'
                    . '&number='.$phoneNumber);
            if($res->getStatusCode() == '200') {
                $responseBody = json_decode($res->getBody(), true);
                if (isset($responseBody['valid'])
                        && $responseBody['valid']) {
                    $response['status'] = true;
                } else {
                    if (isset($responseBody['error'])) {
                        self::event($responseBody['error'], self::VALIDATE_PHONE_LOG_FILE, self::DAILY);
                    }
                }
            } else {
                $response['message'] = trans('messages.validate_phone_api_error');
            }
        } catch (Exception $ex) {
            $response['message'] = $ex->getMessage();
            self::event($ex->getMessage() . "|" . $ex->getLine(), self::VALIDATE_PHONE_LOG_FILE, self::DAILY);
        }
        return $response;
    }

    /**
     * Method to check whether near By check will be applicable while showing product listing.
     * Checks Header Sent From API
     *
     * @return boolean
     *
     */
    public static function checkForNearProductsAPI() {
        $request    = app('request');
        $postCode   = $request->header('user-pincode', '');
        $lat        = $request->header('user-lat', '');
        $lng        = $request->header('user-lng', '');
        if (!empty($postCode)
            && !empty($lat)
            && !empty($lng)
        ) {
            return true;
        }
        return false;
    }

    public static function getUserCartDeliveryPostcodeSessionKey() {
        // there were two different postcode session. BUt now only 1.
        //return config('appConstants.user_delivery_postcode_session_key');
        return config('appConstants.user_landing_postcode_session_key');
    }

    public static function getUserCartDeliveryPostcodeDetails() {
        $response = array(
            'postcode' => '',
            'lat' => '',
            'lng' => '',
        );
        $cartDeliveryPostcodeSessionKey = self::getUserCartDeliveryPostcodeSessionKey();
        $userCartDeliveryPostcode       = session()->get($cartDeliveryPostcodeSessionKey, '');
        $cartDeliveryLatSessionKey      = config::get('appConstants.user_landing_lat_session_key');
        $cartDeliveryLngSessionKey      = config::get('appConstants.user_landing_lng_session_key');
        $userCartDeliveryLat            = session()->get($cartDeliveryLatSessionKey, '');
        $userCartDeliveryLng            = session()->get($cartDeliveryLngSessionKey, '');
        $response['postcode']           = $userCartDeliveryPostcode;
        $response['lat']                = $userCartDeliveryLat;
        $response['lng']                = $userCartDeliveryLng;
        return $response;
    }

    public static function getUserCartDeliveryPostcode() {
        $deliveryPostcodeDetails = self::getUserCartDeliveryPostcodeDetails();
        return $deliveryPostcodeDetails['postcode'];
    }
    
    /**
     * Method to return online message on site
     *
     * @return string
     *
     */
     public static function getOnlineMessage() {
        $configModel = new \App\Configurations();
        echo $configModel->get(\Config::get('configurations.online_msg_key'));
    }
    
    /**
     * Method to return offline message on site
     *
     * @return string
     *
     */
    public static function getOfflineMessage() {
        $configModel = new \App\Configurations();
            echo $configModel->get(\Config::get('configurations.open_time_msg_key'));
    }

    public static function isCartEmpty() {
        $cartCount = session()->get("cart_custom_total", '');
        if (empty($cartCount)) {
            return true;
        }
        return false;
    }

    /**
     * Function to set cookie is not already set.
     */
    public static function updateVisitorCookie() {
        setcookie('alchemy_visited', 'yes', time() + 86400 * 365 * 2);
        return ;
    }
    
    /**
     * Method to check is site is already visited or not.
     * @return boolean
     */
    public static function checkVisitor() {
        $request    = app('request');
        $showMessage        = TRUE;
        if (isset($_COOKIE['alchemy_visited']) &&  $_COOKIE['alchemy_visited'] != null)
        {
            $showMessage = FALSE;
        } else {
            self::updateVisitorCookie();
        }
        return $showMessage;
        
    }
   
    /**
     * Method to return if vendor kyc is filled or not filled.
     * 
     * @return boolean
     */
    public static function checkVendorKycStatus() {
        $storeModel         = new \App\StoreModel();
        $storeId            = Auth::user()->id;
        $allowProductUpload = $storeModel->getStoreKYCStatus($storeId);
        return $allowProductUpload;
    }
    
    /**
     * Method to return formated catname to be used in URl.
     * 
     * @param string $catname
     * @return string
     */
    public static function formatCatName($catname) {
        $catname = str_replace(' ', '-', $catname); // Replaces all spaces with hyphens.
        $catname =  preg_replace('/[^A-Za-z0-9\-]/', '', $catname); // Removes special chars.  
        return strtolower($catname);
    }
    

    /**
     * Method to return if vendor kyc is filled or not filled.
     * 
     * @return boolean
     */
    public static function getVendorSubStores() {
        $subStoreModel      = new \App\SubStoreDetails();
        $storeId            = Auth::user()->id;
        $storeSubStores     = $subStoreModel->getStoreSubStores($storeId);
        return $storeSubStores;
    }

    /**
     * Method to get SubStoreId in session
     */
    public static function getSelectedSubStoreId() {
        $storeId            = Auth::user()->id;
        return session()->get('selected_sub_store_id', $storeId);
    }

    /**
     * Method to set SubStoreId in session
     */
    public static function setSelectedSubStoreId($subStoreId) {
        session()->set('selected_sub_store_id', $subStoreId);
    }

    /**
     * Method to check whether Parent store is selected or substores
     */
    public static function isParentStoreSelected() {
        $storeId            = self::getSelectedSubStoreId();
        $subStoreModel      = new \App\SubStoreDetails();
        $storeSubStores     = $subStoreModel->getStoreSubStores($storeId, FALSE);
        if (!empty($storeSubStores)) {
            return true;
        }
        return false;
    }

    /**
     * Method to check whether Parent store is logged in
     */
    public static function isParentStoreLoggedIn() {
        $storeId            = Auth::user()->id;
        $subStoreModel      = new \App\SubStoreDetails();
        $storeSubStores     = $subStoreModel->getStoreSubStores($storeId);
        if (!empty($storeSubStores)) {
            return true;
        }
        return false;
    }

    /**
     * Method to get the ip address of the user, no matter webserver has some proxy.
     * @return string
     */
    public static function get_ip() {
            //Just get the headers if we can or else use the SERVER global
            if ( function_exists( 'apache_request_headers' ) ) {
                    $headers = apache_request_headers();
            } else {
                    $headers = $_SERVER;
            }
            //Get the forwarded IP if it exists
            if ( array_key_exists( 'X-Forwarded-For', $headers ) && filter_var( $headers['X-Forwarded-For'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 ) ) {
                    $the_ip = $headers['X-Forwarded-For'];
            } elseif ( array_key_exists( 'HTTP_X_FORWARDED_FOR', $headers ) && filter_var( $headers['HTTP_X_FORWARDED_FOR'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 )
            ) {
                    $the_ip = $headers['HTTP_X_FORWARDED_FOR'];
            } else {

                    $the_ip = filter_var( $_SERVER['REMOTE_ADDR'], FILTER_VALIDATE_IP, FILTER_FLAG_IPV4 );
            }
            return $the_ip;
    }

    /**
     * Method to get Store Product Mappping From external Fullfillment API
     *
     * @param string $postCode Description
     * @return array response Array containg Lat Long
     *
     */
    public function getStoreFullFillment($customerId, $aBasketProducts, $aStoreDetails) {
        $response = array(
            'status'    => false,
            'message'   => trans('messages.common_error'),
            'data'      => array()
        );
        $requestData = array(
            'customerId'    => $customerId,
            'products'      => $aBasketProducts,
            'stores'        => $aStoreDetails,
        );
        $aHeaders = array(
            'Content-Type'              => 'application/json',
            'alchemy-transaction-id'    => rand(),
            'alchemy-request-id'        => rand(),
            'alchemy-device-type'       => self::getUserDeviceType(),
            'alchemy-auth-token'        => '12345',
        );
        $requestParams = array(
            'headers'   => $aHeaders,
            'json'      => $requestData,
        );
        try {
            $client = new Client();
            $apiBaseUrl    = env('FULLFILLMENT_API_URL', '');
            $apiEndpoint   = $apiBaseUrl."/api/mapProductStore";
            $res           = $client->request('POST', $apiEndpoint, $requestParams);
            if ($res->getStatusCode() == '200') {
                $responseBody = json_decode($res->getBody(), true);
                if (isset($responseBody['status'])
                        && $responseBody['status']) {
                    if ($responseBody['response']['status']) {
                        $response['status'] = true;
                        $response['data']['mapping'] = $responseBody['response']['data']['mapping'];
                    } else {
                        $response['message'] = $responseBody['response']['message'];
                    }
                } else {
                    $response['message'] = $responseBody['message'];
                }
            } else {
                self::event(trans('messages.fullfillment_api_failure'), self::FULLFILLMENTAPI_LOG_FILE, self::DAILY);
            }
        } catch (Exception $ex) {
            self::event($ex->getMessage() . "|" . $ex->getFile(). "|" . $ex->getLine(), self::FULLFILLMENTAPI_LOG_FILE, self::DAILY);
        }
        return $response;
    }

    /**
     * 
     * @return string Device Type
     */
    public static function getUserDeviceType() {
        $request        = app('request');
        $aDeviceType    = config('device_type.device_type');
        $deviceType     = $request->header('alchemy-device-type', $aDeviceType["web"]);
        return $deviceType;
    }
    
    /**
     * 
     * @param string $disountType
     * @return string
     */
    public static function getDiscountSymbol($disountType, $amount) {
        if($disountType == 'P'){
            return '- '. $amount.'%';
        }elseif ($disountType == 'D' && $amount == '0.00') {
            return '(Free Delivery)';
        }else{
            return '- '. config('appConstants.currency_sign'). $amount;
        }
        
    }

    /**
     * Method to return footer CMS Links
     * 
     * @return array $cmsData
     */
    public static function getFooterCmsLinks() {
        $cmsModel   = new \App\Cms();
        $cmsData    = $cmsModel->getCmsPageForFooter();
        return $cmsData;
    }
    
    /**
     * Method to return gpsc value.
     * 
     * @return value;
     */
    public static function getGlobalGPSC() {
        $configModel = new Configurations();
        return $configModel->get(\Config::get('configurations.gpsc_key'));
    }
    
    /**
     * Method to return gvpc value.
     * 
     * @return value;
     */
    public static function getGlobalGVPC() {
        $configModel = new Configurations();
        return $configModel->get(\Config::get('configurations.gvpc_key'));
    }

    /**
     * Method to get category tree.
     * 
     * @return array
     */
    public static function getCategoryTree() {
        $catModel = new \App\Category();
        $catTree     = $catModel->getCategoryTree();
        return $catTree;
    }

    /**
     * Method to get banner image.
     * 
     * @return string
     */
    public static function getBannerImage($type, $isMobile = 0) {
        $bannerModel    = new \App\Banners();
        $bannerImage     = $bannerModel->getBannerData($type, $isMobile);
        return $bannerImage;
    }

    /**
     * Method used to return product count in current cart.
     * 
     * @param int $id Product Id or Bundle Id
     * @param boolean $isBundle
     * @return mixed $itemCount Empty String or Int Value 
     */
    public static function getProductCartCount($id, $isBundle) {
        $itemCount = '';
        $cartData  = session()->get('user_cart_data', array());
        if (!empty($cartData)) {
            if ($isBundle) {
                foreach ($cartData as $cartItem) {
                    if (isset($cartItem['bundleId']) && $cartItem['bundleId']  == $id) {
                        $itemCount = $cartItem['qty'] / $cartItem['bundleDefaultProductQuantity'];
                        break;
                    }
                }
            } else {
                foreach ($cartData as $cartItem) {
                    if ($cartItem['id'] == $id && $cartItem['options'][0]['bundleId'] == 0) {
                        $itemCount = $cartItem['qty'];
                        break;
                    }
                }
            }
        }
        return $itemCount;
    }

    /**
     * Method to return MangoPay Secure Mode value depending on cart amount value.
     * 
     * @param int $cartAmount
     * @return string $secureMode
     */
    public static function getSecureModeValue($cartAmount = 1) {
        $configModel    = new Configurations();
        $thresholdValue =  $configModel->get(config('configurations.mangopay_3dsecure_key'));
        $secureMode     = 'DEFAULT';
        if ($cartAmount >= $thresholdValue) {
            $secureMode      = 'FORCE';
        }
        return $secureMode;
    }
    
    /**
     * Returns alphanumeric token of 40 characters.
     * 
     * @return string
     */
    public static function getToken() {
        try {
            return substr(hash_hmac('sha256', str_random(40), \Config::get('app.key')), 0, 40);
        } catch (Exception $ex) {
            self::event($ex->getMessage() . "|" . $ex->getLine(), self::FORCE_EMAIL_LOG_FILE, self::DAILY);
        }
    }
    
    /**
     * 
     * @return type
     */
    public static function getLastMonths() {
        $allMonth = config('content_month_mapping.website_map');
        $currDate =  date('Y-m-d');
        $aCurrDate      = explode("-", $currDate);
        $currentMonth   = $aCurrDate[1];
        $aLastMonth     = array();
        foreach($allMonth as $monthId => $monthName) {
            if ($currentMonth > $monthId) {
                $aLastMonth[$monthId] = $monthName;
            }
            if (count($aLastMonth) >= 3) {
                break;
            }
        }
        return $aLastMonth;
    }
    
    /**
     * Method returns all the archive years.
     * 
     * @param Int $year
     * @return Array
     */
    public static function getArchiveYears($year) {
        $allMonth = config('content_month_mapping.website_map');
        $currDate = date('Y-m-d');
        $aCurrDate = explode("-", $currDate);
        $currentMonth = $aCurrDate[1];
        $currentYear = $aCurrDate[0];
        if ($currentYear == $year) {
            $aLastMonth = array();
            foreach ($allMonth as $monthId => $monthName) {
                if ($currentMonth >= $monthId) {
                    $aLastMonth[$monthId] = $monthName;
                }
            }
        } else {
            $aLastMonth = $allMonth;
        }

        return $aLastMonth;
    }

    
    
    /**
     * Method will return future & past months with year.
     * 
     * @return array
     */
    public static function getMonthYear() {
        $monthYear      = [];
        $allMonth       = config('content_month_mapping.website_map');
        $currDate       =  date('Y-m-d');
        $aCurrDate      = explode("-", $currDate);
        $monthYear['future'][$aCurrDate[0].'|'.$aCurrDate[1]] = $allMonth[$aCurrDate[1]];
        for ($i = 1; $i < config('content_month_mapping.future_months'); $i++) {
            $futureDate = date("Y-m-d", strtotime(date('Y-m-01') . " +$i months"));
            $date = explode("-", $futureDate);
            $monthYear['future'][$date[0].'|'.$date[1]] = $allMonth[$date[1]];
        }
        for ($i = 1; $i <= config('content_month_mapping.past_months'); $i++) {
            $pastdate = date("Y-m-d", strtotime(date('Y-m-d') . " -$i months"));
            $date = explode("-", $pastdate);
            $monthYear['past'][$date[0].'|'.$date[1]] = $allMonth[$date[1]];
        }
        return $monthYear;
    }
    
    
    public static function getKeywordsForListing() {
        $aKeywords = \App\Keyword::getKeywordsForListing();
        return $aKeywords;
    }

    public static function getPreviousYears($count = 2) {
        $aYear      = array();
        $prevYear   = date('Y');
        $aYear[$prevYear] = $prevYear;
        for ($i = 1; $i < $count; $i++) {
            $prevYear = date('Y', strtotime('-'.$i.' year'));
            $aYear[$prevYear] = $prevYear;
        }
        return $aYear;
    }

}