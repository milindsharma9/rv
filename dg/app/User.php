<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Support\Facades\DB;
use App\Product;
use Exception;
use App\Http\Helper\CommonHelper;
use Log;
use App\ValidPostcode;
use App\UserAddress;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Config;

/**
 * User Model
 */
class User extends Authenticatable
{
    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'fullname', 'address','companyname', 'fk_users_role', 'email', 'password','image',
        'token', 'activated', 'mobile', 'api_token'
    ];
    
    /**
     * The attributes that should be hidden for arrays.
     *
     * @var array
     */
    protected $hidden = [
        'password', 'remember_token',
    ];
    /**
     * Get system users (store & customer data).
     * 
     * @package User Model
     * @return Object
     */
    protected function getSystemUsers($paginatorvalue,$params = null) {
       // print_r($params);die;
       
        
        try {
            $response = [];
            $qry = DB::table('users')
                ->select('users.fullname', 'users.address', 'users.email',
                        'users.mobile' ,'users.companyname' , 'users.id', 'users.activated', 'users.created_at')
                ->addSelect('ur.name AS role')
                ->join('users_role AS ur', 'ur.id_users_role', '=', 'users.fk_users_role')
                ->where('users.fk_users_role', '=', \Config::get('appConstants.user_role_id'));
         //               $qry->where('users.email','=',$params['email']); 
             if($params!=null && count($params)>0 && $params['email']!=''){
            $qry->where('users.email','=',$params['email']);
            }
            if($params!=null && count($params)>0 && $params['fullname']!=''){
                        $qry->where('users.fullname','LIKE',"%" .$params['fullname'] . "%");
            }
            if($params!=null && count($params)>0 && $params['companyname']!=''){
                        $qry->where('users.companyname','LIKE',"%" .$params['companyname'] . "%");
            }
            if($params!=null && count($params)>0 && $params['mobile']!=''){
                        $qry->where('users.mobile','=',$params['mobile']);
            }
                       $data =      $qry->orderBy('users.id', 'desc')
                    ->paginate($paginatorvalue);
//                ->where('users.activated', '=', 1)
               /* if(is_array($params)){
                    
                    if($params['fullname']!=''){
                        //$qry->where('users.fullname','=',$params['fullname']);
                    }
                    if($params['email']!=''){
                       // $qry->where('users.email','=',$params['email']);
                    }
                    if($params['mobile']!=''){
                       // $qry->where('mobile','=',$params['mobile']);
                    }
                } */
              //  $data = $qry->orderBy('users.id', 'desc')
               // ->paginate($paginatorvalue);*/
            $response = $data;
           
			
            return $response;
        } catch (Exception $ex) {
            Log::error(__METHOD__ . $ex->getMessage());
        }
    }
    /**
     * One to One association with storeDetails.
     * 
     * @package User Model
     * @return App\StoreDetails
     */
    public function storeDetails()
    {
        try{
            return $this->hasOne('App\StoreDetails', 'fk_users_id');
        } catch (Exception $ex) {
            Log::error(__METHOD__ . $ex->getMessage());
        }
    }
    
    /**
     * Get user Store Detail.
     * 
     * @package User Model
     * @param Int $id
     * @return Object
     */
    public function getStoreDetails($id) {
        try{
            return User::where('id', $id)->with('storeDetails')->first();
        } catch (Exception $ex) {
           Log::error(__METHOD__ . $ex->getMessage());
        }
    }
    
    
    /**
     * One to One association with storeDetails.
     * 
     * @package User Model
     * @return App\StoreDetails
     */
    public function subStoreDetails()
    {
        try{
            return $this->hasOne('App\SubStoreDetails', 'fk_users_id');
        } catch (Exception $ex) {
            Log::error(__METHOD__ . $ex->getMessage());
        }
    }
    
    /**
     * Get user Store Detail.
     * 
     * @package User Model
     * @param Int $id
     * @return Object
     */
    public function getSubStoreDetails($id) {
        try{
            return User::where('id', $id)->with('subStoreDetails')->first();
        } catch (Exception $ex) {
           Log::error(__METHOD__ . $ex->getMessage());
        }
    }
    
    
    /**
     * One to One association with storeDetails.
     * 
     * @package User Model
     * @return App\UserAddress
     */
    public function userAddress()
    {   try{
            return $this->hasOne('App\UserAddress', 'fk_users_id');
        } catch (Exception $ex) {
            Log::error(__METHOD__ . $ex->getMessage());
        }
    }
    
    /**
     * Get User Address Detail.
     * 
     * @package User Model
     * @param Int $id
     * @return Object
     */
    public function getUserAddress($id) {
        try {
            return User::where('id', $id)->with('userAddress')->first();
        } catch (Exception $ex) {
            Log::error(__METHOD__ . $ex->getMessage());
        }
    }
    
    /**
     * One to One association with card_address.
     * 
     * @package User Model
     * @return App\CardAddress
     */
    public function cardAddress() {
        try {
            return $this->hasMany('App\CardAddress', 'fk_users_id');
        } catch (Exception $ex) {
            Log::error(__METHOD__ . $ex->getMessage());
        }
    }
    
    /**
     * Fetch card address.
     * 
     * @package User Model
     * @param Int $id
     * @return Object
     */
    public function getCardAddress($id) {
        try {
            return User::where('id', $id)->with('cardAddress')->first();
        } catch (Exception $ex) {
            Log::error(__METHOD__ . $ex->getMessage());
        }
    }
    
    /**
     * One to one association with user_mangopay
     * 
     * @package User Model
     * @return App\UserMangopay
     */
    public function mangoUser() {
        try{
            return $this->hasOne('App\UserMangopay', 'fk_users_id');
        } catch (Exception $ex) {
            Log::error(__METHOD__ . $ex->getMessage());
        }
    }

    /**
     * One to many association with user_mangopay
     *
     * @return App\KycDetails
     */
    public function mangoUserKyc() {
        try{
            return $this->hasMany('App\KycDetails', 'fk_users_id');
        } catch (Exception $ex) {
            Log::error(__METHOD__ . $ex->getMessage());
        }
    }

    /**
     * Fetch User-mangopay-kyc details.
     *
     * @param Int $id
     * @return Object
     */
    public function getmangoUserKYC($id) {
        try {
            return User::where('id', $id)->with('mangoUserKyc', 'storeDetails', 'subStoreDetails')->first();
        } catch (Exception $ex) {
            Log::error(__METHOD__ . $ex->getMessage());
        }
    }

    /**
     * Fetch User-mangopay-kyc, magopay-userID details.
     * Also store details
     *
     * @param Int $id
     * @return Object
     */
    public function getUserCompleteMangoPayDetails($id) {
        try {
            return User::where('id', $id)->with('mangoUserKyc', 'storeDetails', 'mangoUser', 'subStoreDetails')->first();
        } catch (Exception $ex) {
            Log::error(__METHOD__ . $ex->getMessage());
        }
    }
    
    /**
     * Fetch User-mangopay details.
     * 
     * @package User Model
     * @param Int $id
     * @return Object
     */
    public function getmangoUser($id) {
        try {
            return User::where('id', $id)->with('mangoUser')->first();
        } catch (Exception $ex) {
            Log::error(__METHOD__ . $ex->getMessage());
        }
    }
    
    /**
     * One to one association with mangopay_card_details
     * 
     * @package User Model
     * @return Object
     */
    public function mangoCardUser() {
        try {
            return $this->hasMany('App\MangopayCard', 'fk_users_id');
        } catch (Exception $ex) {
            Log::error(__METHOD__ . $ex->getMessage());
        }
    }
    
    /**
     * Fetch mangopay_card_details.
     * 
     * @package User Model
     * @param Int $id
     * @return Object
     */
    public function getmangoCardUser($id) {
        try {
            return User::where('id', $id)->with('mangoCardUser')->first();
        } catch (Exception $ex) {
            Log::error(__METHOD__ . $ex->getMessage());
        }
    }
    
    /**
     * Get use address on basis of userId;
     * 
     * @package User Model
     * @param Int $userId
     * @return Array
     */
    public function getUserSavedAddress($userId) {
        try {
            $addressData = self::getUserAddress($userId);
            if(NULL != $addressData['userAddress']){
                return ['hasAddress' => TRUE, 'address' => json_encode($addressData['userAddress'])];
            }  else {
                return ['hasAddress' => FALSE , 'address' =>  json_encode([])];
            }
        } catch (Exception $ex) {
            Log::error(__METHOD__ . $ex->getMessage());
        }
    }
    
    /**
     * Get card Id on the basis of userId.
     * 
     * @package User Model
     * @param Int $userId
     * @return Array
     */
    public function getUserCardId($userId) {
        try {
            $cardData = self::getmangoCardUser($userId);
            // @HotFix 321, handle later efficiently.
            //$testCheck = isset($cardData['mangoCardUser']) ? $cardData['mangoCardUser'] : array();
            if (isset($cardData['mangoCardUser'])) {
                $testCheck = $cardData['mangoCardUser'];
                $testCheck = $testCheck->toArray();
            } else {
                $testCheck = array();
            }
            if (!empty($testCheck)){
            //if(NULL != $cardData['mangoCardUser']){
                return ['hasCard' => TRUE, 'cardData' => $cardData['mangoCardUser'], 
                    'cardCount' => count($cardData['mangoCardUser'])];
            }  else {
                return ['hasCard' => FALSE];
            }  
        } catch (Exception $ex) {
            Log::error(__METHOD__ . $ex->getMessage());
        }
    }
    
    /**
     * Get user data's
     * 
     * @package User Model
     * @param Int $userId
     * @return Object
     */
    protected function getUserOrders($userId, $pagination = 0 , $limit = NULL ) {
        try {
            $data = DB::table('sales_order AS so')
                ->select('so.id_sales_order', 'so.order_id AS orderId', 'so.total as totalPrice', 'order_status.name AS orderStatus' , 'ss.store_name AS store')
                ->addSelect(DB::raw('COUNT(soi.id_sales_order_item) AS quantity'))
                ->addSelect(DB::raw('DATE_FORMAT(so.created_at,"%d/%m/%Y") AS date'))
                ->addSelect(DB::raw('DATE_FORMAT(so.created_at,"%h:%i") AS time'))
                ->addSelect(DB::raw('GROUP_CONCAT(DISTINCT(soi.fk_product_id)) AS productId'))
                ->join('sales_order_item AS soi', 'soi.fk_sales_order_id', '=', 'so.id_sales_order')
                ->join('order_status', 'order_status.id_order_status', '=', 'so.fk_order_status_id')
                ->join('sub_store_details AS ss', 'ss.fk_users_id', '=', 'soi.fk_store_id')
                ->where('so.fk_users_id', '=', $userId)
                ->where('order_status.id_order_status', '!=', 4)
                ->orderBy('so.id_sales_order', 'DESC')
                ->groupBy('so.id_sales_order');
            if($pagination == 1) {
                if (NULL != $limit) {
                    $data = $data->take($limit)->get();
                }  else {
                    $data = $data->paginate(5);
                }
            } else {
                $data = $data->get();
            }
            foreach ($data as $key => $value) {
                $productIds = array_slice(explode(',', $value->productId), 0, 7);
                $productDetail['productDetail'] = ($this->getProductDetails($productIds));
                $data[$key] = collect($data[$key])->merge(collect($productDetail));
            }
            return $data;
        } catch (Exception $ex) {
            Log::error(__METHOD__ . $ex->getMessage());
        }
    }
    
    
    
    /**
     * Fetch user details for user.
     * 
     * @param Int $userId
     * @return Object
     */
    protected function getUserOrderDetails($userId) {
        try{
            $orderDetails = DB::table('sales_order AS so')
                ->addSelect(DB::raw('IF(COUNT(so.id_sales_order) = 0, "0", COUNT(so.id_sales_order)) AS total_order'))
                ->addSelect(DB::raw('IF(SUM(so.total) = 0, "0", SUM(so.total)) AS total_value'))
                ->addSelect(DB::raw('IF(AVG(so.total) = 0, "0", AVG(so.total)) AS avg_value'))
                ->where('so.fk_users_id', '=', $userId)
                ->where('so.fk_order_status_id', '!=', 4)
                ->groupBy('so.fk_users_id')
                ->first();
            return $orderDetails;
        } catch (Exception $ex) {
            Log::error(__METHOD__ . $ex->getMessage());
        }
    }
    
    /**
     * Fetch user order product details.
     * 
     * @param Int $userId
     * @return Object
     */
    protected function fetchOrderProducts($userId) {
        try{
            $orderDetails = DB::table('sales_order AS so')
                ->addSelect('so.order_id AS orderId')
                ->addSelect('so.created_at')
                ->addSelect('so.total AS orderTotal')
                ->addSelect(DB::raw('GROUP_CONCAT(DISTINCT(p.name)) AS product_name'))
                ->addSelect(DB::raw('COUNT(p.name) AS quantity'))
                ->join('sales_order_item AS soi', 'soi.fk_sales_order_id', '=', 'so.id_sales_order')
                ->join('products AS p', 'p.id', '=', 'soi.fk_product_id')
                ->where('so.fk_users_id', '=', $userId)
                ->where('so.fk_order_status_id', '!=', 4)
                ->groupBy('soi.fk_sales_order_id')
                ->orderBy('so.id_sales_order', 'DESC')
                ->get();
            return $orderDetails;
        } catch (Exception $ex) {
            Log::error(__METHOD__ . $ex->getMessage());
        }
    }


    /**
     * Get ptoduct details on basis of id 
     * 
     * @package User Model
     * @param type $productId array/ single value.
     * @return Object
     */
    protected function getProductDetails($productId) {
        try {
            $product = new Product();
            return $product->getProductDetails($productId, TRUE, FALSE, TRUE);
        } catch (Exception $ex) {
            Log::error(__METHOD__ . $ex->getMessage());
        }
    }

    /**
     *
     * Method to register user on Alchemy Platform
     *
     * @param array $data User Data Required for registration
     *
     * @return array $aResponse User Details
     *
     */
    public function register(array $data) {
        $aResponse = array(
            'status'        => false,
            'message'       => trans('messages.db_error'),
            'data'          => '',
            'debug_trace'   => '', // For API
        );
        try {
            $phoneNumber = $this->formatPhoneNumberBeforSaving(isset($data['phone']) ? $data['phone'] : "");
            $data['phone'] = $phoneNumber;
            $reponse    = User::create([
                    'first_name'        => isset($data['name']) ? $data['name'] : "First name",
                    'email'             => $data['email'],
                    'password'          => bcrypt($data['password']),
                    'last_name'         => isset($data['last_name']) ? $data['last_name'] : "Last name",
                    'phone'             => isset($data['phone']) ? $data['phone'] : "",
                    'fk_users_role'     => isset($data['fk_users_role']) ? $data['fk_users_role'] : "3",
                    'activated'         => isset($data['activated']) ? $data['activated'] : "1",
            ]);
            $aResponse['data']          = $reponse;
            $aResponse['status']        = true;
            $aResponse['message']       = trans('messages.success');
        } catch (Exception $ex) {
            $debugTrace                 = $ex->getMessage() . "|" . $ex->getFile() . "|" . $ex->getLine();
            $aResponse['message']       = trans('messages.common_error');
            $aResponse['debug_trace']   = $debugTrace;
            CommonHelper::event($debugTrace, CommonHelper::USER_REGISTER_LOG_FILE, CommonHelper::DAILY);
        }
        return $aResponse;
    }
    
    /**
     * Return validation rules for customer profile.
     * 
     * @return array
     */
    public function getCustomerProfileValidationRules() {
        try {
            return array(
                'first_name' => "required|max:64",
                'last_name' => "required|max:64",
                'phone' => 'required|numeric|digits_between:7,15');
        } catch (Exception $ex) {
            Log::error(__METHOD__ . $ex->getMessage());
        }
    }

    /**
     * Update customer profile details in database.
     * 
     * @param array $request
     * @param Integer $userId
     * @return void
     */
    public function updateCustomerProfile($request, $userId) {
        try {
            $user = User::findOrFail($userId);
            $phoneNumber = $this->formatPhoneNumberBeforSaving($request['phone']);
            $request['phone'] = $phoneNumber;
            $user->update(array_only($request, ['first_name', 'last_name', 'phone']));
        } catch (Exception $ex) {
            Log::error(__METHOD__ . $ex->getMessage());
        }
    }
    
    /**
     * Return validation rules for customer address.
     * 
     * @return array
     */
    public function getValidationForAddress($postCode) {
        try {            
            return [
                'address' => 'required',
                'city' => 'required',
                'state' => 'required',
                'pin' => "required|regex:/(^[A-Za-z0-9 ]+$)+/|max:10",
            ];
        } catch (Exception $ex) {
            Log::error(__METHOD__ . $ex->getMessage());
        }
    }
    
    /**
     * Update user address in database.
     * 
     * @param Int $userId
     * @param array $request
     */
    public function updateCustomerAddress($userId, $request) {
        try {
            $exist = UserAddress::where('fk_users_id', '=', $userId)->first();
            if ($exist) {
                UserAddress::where('id_user_addres', $exist->id_user_addres)
                        ->update(array_only($request, ['address', 'city', 'state', 'pin']));
            } else {
                UserAddress::create([
                    'address' => isset($request['address']) ? $request['address'] : '',
                    'city' => isset($request['city']) ? $request['city'] : '',
                    'state' => isset($request['state']) ? $request['state'] : '',
                    'pin' => isset($request['pin']) ? $request['pin'] : '',
                    'fk_users_id' => $userId,]);
            }
        } catch (Exception $ex) {
            Log::error(__METHOD__ . $ex->getMessage());
        }
    }

    /**
     * Function to validate PostCode.
     * 
     * @return array
     */
    public function validationMessages() {
        try {
            return [
                'pin.in' => trans('messages.postcode_error_not_serviceable'),
            ];
        } catch (Exception $ex) {
            Log::error(__METHOD__ . $ex->getMessage());
        }
    }

    /**
     * Function to update user card status.
     * @param int $userId
     * @param int $cardId
     * @param string $status
     */
    public function updateUserCardStatus($userId, $cardId, $status = 'CREATED') {
        try {
            $exist = DB::table('user_card_status')->where('fk_users_id', '=', $userId)->first();
            if ($exist) {
                DB::table('user_card_status')->where('id', $exist->id)
                        ->update(['cardId' => $cardId, 'status' => $status]);
            } else {
                DB::table('user_card_status')->insert([
                    'fk_users_id' => $userId,
                    'cardId' => $cardId,
                    'status' => $status,
                ]);
            }
        } catch (Exception $ex) {
            Log::error(__METHOD__ . $ex->getMessage());
        }
    }
    
    /**
     * Function to fetch user_card_status field on basis of userId.
     * 
     * @param int $userId
     * @param string $field
     * @return object
     */
    public function getUserCardRegistrationField($userId, $field= 'cardId') {
        try {
            $data = DB::table('user_card_status')
                ->select($field)
                ->where('fk_users_id', '=', $userId)
                ->orderBy('id', 'desc')
                ->first();
            return $data;
        } catch (Exception $ex) {
            Log::error(__METHOD__ . $ex->getMessage());
        }
    }
    
    /**
     * Update casrdAddressId for user_card_status.
     * 
     * @param Int $userId
     * @param Int $cardAddressId
     */
    public function updateUserCardAddress($userId, $cardAddressId) {
        try {
            $exist = DB::table('user_card_status')->where('fk_users_id', '=', $userId)->first();
            if ($exist) {
                DB::table('user_card_status')->where('id', $exist->id)
                        ->update(['cardAddressId' => $cardAddressId]);
            }
        } catch (Exception $ex) {
            Log::error(__METHOD__ . $ex->getMessage());
        }
    }

      /**
       * Return validation rules for change password.
       * 
       * @return array
       */
      public function getChangePasswordValidation() {
        try {
          return array(
              'email' => 'required|email',
              'password' => 'required|confirmed|min:6',
          );
        }
        catch (Exception $ex) {
          Log::error(__METHOD__ . $ex->getMessage());
        }
      }

      /**
     * Function to update user profile
     * 
     * @return array
     */
    public function updateUserPassword($userId, $userEmail, $newPassword) {
        try {
            $user = User::where('id', '=', $userId)
                            ->where('email', '=', $userEmail)->first();
            if (!empty($user)) {
                $user->password = bcrypt($newPassword);
                $user->save();
                $response = ['status' => TRUE];
            } else {
                $response = ['status' => FALSE];
            }
            return $response;
        } catch (Exception $ex) {
            Log::error(__METHOD__ . $ex->getMessage());
        }
    }
    
    /**
     * Function to get card address validations.
     * @return array 
     */
    public function getCardAddressValidation() {
        try {
            return array(
                'address' => 'required',
                'city' => 'required',
                'state' => 'required',
                'pin' => 'required|max:10|regex:/(^[A-Za-z0-9 ]+$)+/',
                'id' => 'required|exists:users,id'
            );
        } catch (Exception $ex) {
            Log::error(__METHOD__ . $ex->getMessage());
        }
    }

    /**
     * Function to Save user card addess.
     * 
     * @param Request $request
     * @return json
     */
    public function saveUserCardAddress(Request $request) {
        try {
            $userId = $request['id'];
            $update = FALSE;
            if (!empty($request->get('mango_users_card_id'))) {
                $exist = MangopayCard::where('fk_users_id', '=', $userId)
                        ->where('mango_users_card_id', '=', $request->get('mango_users_card_id'))
                        ->first();
                if($exist){
                    $cardAddressId = $exist['fk_card_addres_id'];
                    $update        = TRUE;
                }
            }
            if ($update) {
                CardAddress::where(['id_card_addres' => $cardAddressId])
                        ->update($request
                                ->only('address', 'city', 'state', 'pin'));
                $this->updateUserCardAddress($userId, $cardAddressId);
            } else {
                $updatedData = CardAddress::create([
                            'address' => isset($request['address']) ? $request['address'] : '',
                            'city' => isset($request['city']) ? $request['city'] : '',
                            'state' => isset($request['state']) ? $request['state'] : '',
                            'pin' => isset($request['pin']) ? $request['pin'] : '',
                            'fk_users_id' => $userId,]);
                $this->updateUserCardAddress($userId, $updatedData['id']);
            }
            return response()->json(['status' => 'success']);
        } catch (Exception $ex) {
            Log::error(__METHOD__ . $ex->getMessage());
        }
    }

    /**
     * Function to save user mangopay details.
     * 
     * @param Int $userId
     * @param Request $request
     */
    public function saveMangopayUserDetails($userId, Request $request, $cardId = NULL) {
        try {
            $exist = FALSE;
            if ($cardId != NULL) {
                $exist = MangopayCard::where('fk_users_id', '=', $userId)
                        ->where('mango_users_card_id', '=', $cardId)
                        ->first();
            }
            if ($exist) {
                MangopayCard::where(['id_mangopay_card_details' => $exist['id_mangopay_card_details']])
                        ->update($request
                                ->only(['mango_users_card_id', 'fk_card_addres_id']));
            } else {
                $count = MangopayCard::where('fk_users_id', '=', $userId)
                        ->first();
                $default = '1';
                if($count['id_mangopay_card_details']){
                    $default = '0';
                }
                MangopayCard::create([
                    'mango_users_card_id' => isset($request['mango_users_card_id']) ? $request['mango_users_card_id'] : '',
                    'fk_card_addres_id' => isset($request['fk_card_addres_id']) ? $request['fk_card_addres_id'] : '',
                    'fk_users_id' => $userId, 'is_default' => $default]);
            }
        } catch (Exception $ex) {
            Log::error(__METHOD__ . $ex->getMessage());
        }
    }
    
    /**
     * Method to update default card.
     * 
     * @param Int $userId
     * @param Int $cardId
     * @param Request $request
     */
    public function updateCardDefault($userId, $cardId) {
        try {
            $exist = MangopayCard::where('fk_users_id', '=', $userId)
                    ->where('mango_users_card_id', '=', $cardId)
                    ->first();
            if ($exist) {
                MangopayCard::where(['fk_users_id' => $userId])
                        ->update(['is_default' => 0]);
                MangopayCard::where(['id_mangopay_card_details' => $exist['id_mangopay_card_details']])
                        ->update(['is_default' => 1]);
            }
        } catch (Exception $ex) {
            Log::error(__METHOD__ . $ex->getMessage());
        }
    }

    /**
     * One to One association with storeLegalAddress.
     * 
     * @package User Model
     * @return App\StoreLegalAddress
     */
    public function storeLegalAddress()
    {   try{
            return $this->hasOne('App\StoreLegalAddress', 'fk_users_id');
        } catch (Exception $ex) {
            Log::error(__METHOD__ . $ex->getMessage());
        }
    }
    
    /**
     * Get User Address Detail.
     * 
     * @package User Model
     * @param Int $id
     * @return Object
     */
    public function getStoreLegalAddress($id) {
        try {
            return User::where('id', $id)->with('storeLegalAddress')->first();
        } catch (Exception $ex) {
            Log::error(__METHOD__ . $ex->getMessage());
        }
    }
    
    /**
     * Get system vendors.
     * 
     * @param Int $userId
     * @package User Model
     * @return Object
     */
    protected function getSystemVendors($userId = NULL) {
        try {
            $response = [];
            $data = DB::table('users')
                    ->select('users.first_name', 'users.last_name', 'users.email', 'users.phone', 'users.id', 'users.activated', 'users.created_at')
                    ->addSelect('ur.name AS role')
                    ->addSelect(DB::raw('IF(sd.store_status = 1, "ON", "OFF") AS store_status'))
                    ->addSelect(DB::raw('IF(users.activated = 1, "ACTIVATED", "PENDING VERIFICATION") AS activte_status'))
                    ->addSelect(DB::raw('IF(sa.address = "", "", sa.address) AS address'))
                    ->addSelect(DB::raw('IF(sa.city = "", "", sa.city) AS town'))
                    ->addSelect(DB::raw('IF(sa.state = "", "", sa.state) AS country'))
                    ->addSelect(DB::raw('IF(sa.pin = "", "", sa.pin) AS post_code'))
                    ->addSelect(DB::raw('IF(ssd.store_name = "", "", ssd.store_name) AS store_name'))
                    ->addSelect(DB::raw('COUNT(ps.fk_product_id) AS product_listed'))
                    ->join('users_role AS ur', 'ur.id_users_role', '=', 'users.fk_users_role')
                    ->join('store_details AS sd', 'sd.fk_users_id', '=', 'users.id')
                    ->leftjoin('sub_store_details AS ssd', 'ssd.fk_users_id', '=', 'users.id')
                    ->join('store_address AS sa', 'sa.fk_users_id', '=', 'users.id')
                    ->leftjoin('products_store AS ps', 'ps.fk_user_id', '=', 'users.id')
                    ->where('users.fk_users_role', '=', \Config::get('appConstants.vendor_role_id'))
//                    ->where('users.activated', '=', 1)
                    ->orderBy('users.id', 'desc')
                    ->groupBy('users.id');
            if($userId){
                $data = $data->where('users.id', $userId);
            }
            $data = $data->get();
            $response = $data;
            foreach ($data as $key => $value) {
                $salesData = DB::table('sales_order_item AS soi')
                        ->addSelect('soi.fk_sales_order_id AS orderId')
                        ->addSelect(DB::raw('IF(COUNT(soi.fk_sales_order_id) = 0, "0", COUNT(soi.fk_sales_order_id)) AS total_order'))
                        ->addSelect(DB::raw('SUM(soi.store_price) AS total_value'))
                        ->addSelect(DB::raw('AVG(soi.store_price) AS avg_order'))
                        ->where('soi.fk_store_id', '=', $value->id)
                        ->first();
                $response[$key]->salesData = $salesData;
            }
            return $response;
        } catch (Exception $ex) {
            Log::error(__METHOD__ . $ex->getMessage());
        }
    }

    /**
     * Get system vendors.
     * 
     * @param Int $userId
     * @package User Model
     * @return Object
     */
    protected function getSystemVendorsForEdit($userId = NULL) {
        try {
            $response = [];
            $data = DB::table('users')
                    ->select('users.first_name', 'users.last_name', 'users.email', 'users.phone', 'users.id', 'users.activated', 'users.created_at')
                    ->addSelect('ur.name AS role')
                    ->addSelect(DB::raw('IF(ssd.store_status = 1, "ON", "OFF") AS store_status'))
                    ->addSelect(DB::raw('IF(users.activated = 1, "ACTIVATED", "PENDING VERIFICATION") AS activte_status'))
                    ->addSelect(DB::raw('IF(sa.address = "", "", sa.address) AS address'))
                    ->addSelect(DB::raw('IF(sa.city = "", "", sa.city) AS town'))
                    ->addSelect(DB::raw('IF(sa.state = "", "", sa.state) AS country'))
                    ->addSelect(DB::raw('IF(sa.pin = "", "", sa.pin) AS post_code'))
                    ->addSelect(DB::raw('IF(ssd.store_name = "", "", ssd.store_name) AS store_name'))
                    ->addSelect(DB::raw('COUNT(ps.fk_product_id) AS product_listed'))
                    ->join('users_role AS ur', 'ur.id_users_role', '=', 'users.fk_users_role')
                    //->join('store_details AS sd', 'sd.fk_users_id', '=', 'users.id')
                    ->leftjoin('sub_store_details AS ssd', 'ssd.fk_users_id', '=', 'users.id')
                    ->join('store_address AS sa', 'sa.fk_users_id', '=', 'users.id')
                    ->leftjoin('products_store AS ps', 'ps.fk_user_id', '=', 'users.id')
                    ->where('users.fk_users_role', '=', \Config::get('appConstants.vendor_role_id'))
//                    ->where('users.activated', '=', 1)
                    ->orderBy('users.id', 'desc')
                    ->groupBy('users.id');
            if($userId){
                $data = $data->where('users.id', $userId);
            }
            $data = $data->get();
            $response = $data;
            foreach ($data as $key => $value) {
                $salesData = DB::table('sales_order_item AS soi')
                        ->addSelect('soi.fk_sales_order_id AS orderId')
                        ->addSelect(DB::raw('IF(COUNT(soi.fk_sales_order_id) = 0, "0", COUNT(soi.fk_sales_order_id)) AS total_order'))
                        ->addSelect(DB::raw('SUM(soi.store_price) AS total_value'))
                        ->addSelect(DB::raw('AVG(soi.store_price) AS avg_order'))
                        ->where('soi.fk_store_id', '=', $value->id)
                        ->first();
                $response[$key]->salesData = $salesData;
            }
            return $response;
        } catch (Exception $ex) {
            Log::error(__METHOD__ . $ex->getMessage());
        }
    }
    

    /**
     * Get system vendors.
     * 
     * @param Int $userId
     * @package User Model
     * @return Object
     */
    protected function getVendorStoresEdit($userId) {
        try {
            $response = [];
            $data = DB::table('users')
                    ->select('users.first_name', 'users.last_name', 'users.email', 'users.phone', 'users.id', 'users.activated', 'users.created_at')
                    ->addSelect('ur.name AS role')
                    ->addSelect(DB::raw('IF(ssd.store_status = 1, "ON", "OFF") AS store_status'))
                    ->addSelect(DB::raw('IF(users.activated = 1, "ACTIVATED", "PENDING VERIFICATION") AS activte_status'))
                    ->addSelect(DB::raw('IF(sa.address = "", "", sa.address) AS address'))
                    ->addSelect(DB::raw('IF(sa.city = "", "", sa.city) AS town'))
                    ->addSelect(DB::raw('IF(sa.state = "", "", sa.state) AS country'))
                    ->addSelect(DB::raw('IF(sa.pin = "", "", sa.pin) AS post_code'))
                    ->addSelect(DB::raw('IF(ssd.store_name = "", "", ssd.store_name) AS store_name'))
                    ->addSelect(DB::raw('COUNT(ps.fk_product_id) AS product_listed'))
                    ->join('users_role AS ur', 'ur.id_users_role', '=', 'users.fk_users_role')
                    //->join('store_details AS sd', 'sd.fk_users_id', '=', 'users.id')
                    ->leftjoin('sub_store_details AS ssd', 'ssd.fk_users_id', '=', 'users.id')
                    ->join('store_address AS sa', 'sa.fk_users_id', '=', 'users.id')
                    ->leftjoin('products_store AS ps', 'ps.fk_user_id', '=', 'users.id')
                    ->where('users.fk_users_role', '=', \Config::get('appConstants.vendor_role_id'))
//                    ->where('users.activated', '=', 1)
                    ->orderBy('users.id', 'desc')
                    ->groupBy('users.id');
            
            $data = $data->where('ssd.fk_parent_id', $userId)
                    ->orWhere('ssd.fk_users_id', $userId);;
            
            $data = $data->get();
            $response = $data;
            foreach ($data as $key => $value) {
                $salesData = DB::table('sales_order_item AS soi')
                        ->addSelect('soi.fk_sales_order_id AS orderId')
                        ->addSelect(DB::raw('IF(COUNT(soi.fk_sales_order_id) = 0, "0", COUNT(DISTINCT(soi.fk_sales_order_id))) AS total_order'))
                        ->addSelect(DB::raw('ROUND(SUM(soi.store_price),2) AS total_value'))
                        ->addSelect(DB::raw('IF(SUM(soi.store_price)/COUNT(DISTINCT(soi.fk_sales_order_id)) = 0, "0", ROUND(SUM(soi.store_price)/COUNT(DISTINCT(soi.fk_sales_order_id)),2)) AS avg_order'))
                        ->where('soi.fk_store_id', '=', $value->id)
                        ->get();
                $response[$key]->salesData = isset($salesData['0'])? $salesData['0'] : '';
            }
            return $response;
        } catch (Exception $ex) {
            Log::error(__METHOD__ . $ex->getMessage());
        }
    }
    
    /**
     * Method to return user subscription data.
     * 
     * @param int $userId
     * @return int
     */
    protected function getUserSubscription($userId) {
        try {
           $response = 0;
           $userDetails = DB::table('user_subscription')
                        ->addSelect('is_subscribed')
                        ->where('fk_users_id', '=',$userId)
                        ->first();
           if($userDetails){
               $response = $userDetails->is_subscribed;
           }else{
               $response = 0;
           }
        } catch (Exception $ex) {
            Log::error(__METHOD__ . $ex->getMessage());
        }
        return $response;
    }
    
    /**
     * Method to update subscription of a user.
     * 
     * @param Int $userId
     * @param Int $status
     * @return Array
     */
    protected function updateSubscriptionStatus($userId, $status) {
        $response = ['status' => 'error', 'message'=> 'Some Error Occured'];
        try {
            $userDetails = DB::table('user_subscription')
                        ->where('fk_users_id', '=',$userId)->first();
           if($userDetails){
               $updateData = array('is_subscribed' => $status,'updated_at' => CommonHelper::getCurrentDateTime());
                DB::table('user_subscription')->where('fk_users_id', $userId)
                    ->update($updateData);
           }else{
               DB::table('user_subscription')->insert([
                    'fk_users_id' => $userId,
                    'is_subscribed' => $status,'updated_at' => CommonHelper::getCurrentDateTime(),'created_at' => CommonHelper::getCurrentDateTime(),
                ]);
           }
           $response = [ 'status' => 'success','message'=> 'Details Updated successfully.'];
        } catch (Exception $ex) {
            Log::error(__METHOD__ . $ex->getMessage());
        }
        return $response;
        
    }
    
    /**
     * One to One association with storeLegalAddress.
     * 
     * @package User Model
     * @return App\StoreRegisteredAddress
     */
    public function storeRegisteredAddress()
    {   try{
            return $this->hasOne('App\StoreRegisteredAddress', 'fk_users_id');
        } catch (Exception $ex) {
            Log::error(__METHOD__ . $ex->getMessage());
        }
    }
    
    /**
     * Get User Address Detail.
     * 
     * @package User Model
     * @param Int $id
     * @return Object
     */
    public function getStoreRegisteredAddress($id) {
        try {
            return User::where('id', $id)->with('storeRegisteredAddress')->first();
        } catch (Exception $ex) {
            Log::error(__METHOD__ . $ex->getMessage());
        }
    }
    
    /**
     * Function to create a new vendor in user table.
     * 
     * @param Array $data
     * @return Int
     */
    public function createVendorUser($data) {
        try {
            return User::create([
                        'email' => $data['email'],
                        'fk_users_role' => isset($data['fk_users_role']) ? $data['fk_users_role'] : Config::get('appConstants.user_role_id'),
                        'token' => isset($data['token']) ? $data['token'] : "",
                        'activated' => isset($data['activated']) ? $data['activated'] : 0,
            ]);
        } catch (Exception $ex) {
            Log::error(__METHOD__ . $ex->getMessage());
        }
    }

    /**
     * Method to register Vendor on Alchemy Platform
     * @param array $data Data required for Registration
     * @return array $aResponse
     *
     */
    public function saveDriverRegistrationData(array $data) {
        $aResponse = array(
            'status' => false,
            'message' => trans('messages.common_error'),
        );
        try {
            DB::beginTransaction();
            $data['fk_users_role'] = config('appConstants.driver_role_id');
            //$data['password'] = '';
            $userData = $this->register($data);
            if ($userData['status']) {
                $data['fk_users_id']        = $userData['data']->id;
                $driverDetailResponse       = $this->_saveUpdateDriverDetails($data); // save drivers details.
                $driverAvailabilityResponse = $this->_saveDriverAvailability($data['availability'], $data['fk_users_id']); //save drivers availability.
                $driverQuestionResponse     = $this->_saveDriverQuestions($data, $data['fk_users_id']); // save drivers questions.
                if ($driverDetailResponse['status']
                        && $driverAvailabilityResponse['status']
                        && $driverQuestionResponse['status']
                ) {
                    DB::commit();
                    $aResponse['message'] = trans('messages.vendor_apply_success');
                    $aResponse['status']  = true;
                } else {
                    //$aResponse['message'] = trans('messages.store_register_success');
                    // @todo show wich message;
                }
            } else {
                $aResponse['message'] = $userData['message'];
            }
        } catch (Exception $ex) {
            DB::rollBack();
            CommonHelper::event($ex->getMessage() . "|" . $ex->getFile() . "|" . $ex->getLine(), CommonHelper::USER_REGISTER_LOG_FILE, CommonHelper:: DAILY);
        }
        return $aResponse;
    }

    /**
     *
     * Method to save driver details
     *
     * @param array $data Driver Data Required for registration
     *
     * @return array $aResponse Driver Data Details
     *
     */
    protected function _saveUpdateDriverDetails(array $data, $driverId = null) {
        $aResponse = array(
            'status'        => false,
            'message'       => trans('messages.db_error'),
            'data'          => '',
            'debug_trace'   => '', // For API
        );
        try {
            $deliveryArea           = $data['delivery_area'];
            $deliveryArea           = array_flip($deliveryArea);
            $data['east_london']    = (isset($deliveryArea['east_london'])) ? 1 : 0;
            $data['central_london'] = (isset($deliveryArea['central_london'])) ? 1 : 0;
            $data['south_london']   = (isset($deliveryArea['south_london'])) ? 1 : 0;
            $data['west_london']    = (isset($deliveryArea['west_london'])) ? 1 : 0;
            $insertData = array_only($data, ['fk_users_id', 'vehicle', 'address_line_1', 'address_line_2',
                'city', 'region', 'pin', 'country', 'nationality', 'fk_occupation_id', 'is_right_to_work',
                'east_london', 'central_london', 'south_london', 'west_london']);
            if (!empty($driverId)) {
                $reponse = DB::table('driver_details')->where('fk_users_id', '=', $driverId)
                ->update($insertData);
            } else {
                $reponse = DB::table('driver_details')->insert($insertData);
            }
            $aResponse['data']          = $reponse;
            $aResponse['status']        = true;
            $aResponse['message']       = trans('messages.success');
        } catch (Exception $ex) {
            $debugTrace                 = $ex->getMessage() . "|" . $ex->getFile() . "|" . $ex->getLine();
            $aResponse['message']       = trans('messages.common_error');
            $aResponse['debug_trace']   = $debugTrace;
            CommonHelper::event($debugTrace, CommonHelper::USER_REGISTER_LOG_FILE, CommonHelper::DAILY);
        }
        return $aResponse;
    }

    /**
     *
     * Method to save driver details
     *
     * @param array $data Driver Data Required for registration
     *
     * @return array $aResponse Driver Data Details
     *
     */
    protected function _saveDriverAvailability(array $availabilityData, $driverId) {
        $aResponse = array(
            'status'        => false,
            'message'       => trans('messages.db_error'),
            'data'          => '',
            'debug_trace'   => '', // For API
        );
        try {
            $insertData = array();
                foreach ($availabilityData as $dayAvailability) {
                    $aDayAvailability = explode("_", $dayAvailability);
                    $day        = $aDayAvailability[0];
                    $time       = $aDayAvailability[1];
                    $aData      = array(
                        'fk_users_id'   => $driverId,
                        'day'           => $day,
                        'fk_time_id'    => $time,
                    );
                    array_push($insertData, $aData);
                }
            $reponse =    DB::table('driver_availability')->insert($insertData);
            $aResponse['data']          = $reponse;
            $aResponse['status']        = true;
            $aResponse['message']       = trans('messages.success');
        } catch (Exception $ex) {
            $debugTrace                 = $ex->getMessage() . "|" . $ex->getFile() . "|" . $ex->getLine();
            $aResponse['message']       = trans('messages.common_error');
            $aResponse['debug_trace']   = $debugTrace;
            CommonHelper::event($debugTrace, CommonHelper::USER_REGISTER_LOG_FILE, CommonHelper::DAILY);
        }
        return $aResponse;
    }

    /**
     *
     * Method to save driver details
     *
     * @param array $data Driver Data Required for registration
     *
     * @return array $aResponse Driver Data Details
     *
     */
    protected function _saveDriverQuestions(array $data, $driverId) {
        $aResponse = array(
            'status'        => false,
            'message'       => trans('messages.db_error'),
            'data'          => '',
            'debug_trace'   => '', // For API
        );
        try {
            $driverVehicle          = $data['vehicle'];
            $aQuestion              = config('rider_configurations.'.$driverVehicle."_question");
            //$driverId               = $data['fk_users_id'];
            $insertData             = array();
            foreach ($aQuestion as $questionId => $question) {
                if (isset($data['question_'.$questionId])) {
                    $answer = $data['question_'.$questionId];
                    $aData  = array(
                        'fk_users_id'       => $driverId,
                        'fk_question_id'    => $questionId,
                        'response'          => $answer,
                    );
                    array_push($insertData, $aData);
                }
            }
            if (!empty($insertData)) {
                $reponse = DB::table('driver_question_response')->insert($insertData);
            }
            $aResponse['data']          = $reponse;
            $aResponse['status']        = true;
            $aResponse['message']       = trans('messages.success');
        } catch (Exception $ex) {
            $debugTrace                 = $ex->getMessage() . "|" . $ex->getFile() . "|" . $ex->getLine();
            $aResponse['message']       = trans('messages.common_error');
            $aResponse['debug_trace']   = $debugTrace;
            CommonHelper::event($debugTrace, CommonHelper::USER_REGISTER_LOG_FILE, CommonHelper::DAILY);
        }
        return $aResponse;
    }

    /**
     * 
     * @return array
     */
    public function getDriverValidationRules() {
        $rules = array(
            'city'              => 'required',
            'vehicle'           => 'required',
            'name'        => 'required',
            'last_name'         => 'required',
            'phone'             => 'required|numeric|digits_between:7,15',
            'email'             => 'required|email|max:255|unique:users',
            'nationality'       => 'required',
            'address_line_1'    => 'required',
            'region'            => 'required',
            'country'           => 'required',
            'pin'               => 'required',
            'fk_occupation_id'  => 'required',
            'delivery_area'     => 'required',
            'availability'      => 'required',
            'is_right_to_work'      => 'required',
        );
        return $rules;
    }

    /**
     * 
     * @return array
     */
    public function getDriverValidationMessages() {
        $messages = array(
            'delivery_area.required'    => 'Please select atleast one delivery Area.',
            'availability.required'     => 'Please select atleast one day as available.',
            'name.required'       => 'The First Name field is required.',
            'is_right_to_work.required' => 'You must have right to work in UK.',
        );
        return $messages;
    }   
        
    /**
     * 
     * @param int $phoneNumber
     * @return int
     */
    public function formatPhoneNumberBeforSaving($phoneNumber) {
        $phonePrefix    = substr($phoneNumber, 0, 2);
        if ($phonePrefix == '44') {
            $phoneNumber = substr($phoneNumber , 2);
        }
        $phonePrefix    = substr($phoneNumber, 0, 1);
        if ($phonePrefix == '0') {
            $phoneNumber = substr($phoneNumber , 1);
        }
        return $phoneNumber;
    }

    /**
     * 
     * @param int $driverId
     * @return array
     */
    public function getDriverDetialsEdit($driverId) {
        $aDriverDetails = User::addSelect('driver_details.*', 'users.*')
                //->addSelect(DB::raw('group_concat(driver_availability.day) as day, group_concat(driver_availability.fk_time_id) as time_id'))
                ->addSelect(DB::raw('group_concat(driver_question_response.fk_question_id) as fk_question_id, group_concat(driver_question_response.response) as question_response'))
                ->join('driver_details', 'driver_details.fk_users_id', '=', 'users.id')
                //->join('driver_availability', 'driver_availability.fk_users_id', '=', 'users.id')
                ->join('driver_question_response', 'driver_question_response.fk_users_id', '=', 'users.id')
                ->where('users.id', '=', $driverId)
                ->first();
        return $aDriverDetails;
    }

    /**
     * 
     * @param type $driverId
     */
    public function getDriverAvalability($driverId) {
        $aDriverAvailability = User::addSelect(DB::raw('group_concat(driver_availability.day) as day, group_concat(driver_availability.fk_time_id) as time_id'))
            ->join('driver_availability', 'driver_availability.fk_users_id', '=', 'users.id')
            ->where('users.id', '=', $driverId)
            ->first();
        if (!empty($aDriverAvailability)) {
            $aDriverAvailability = $aDriverAvailability->toArray();
        }
        return $aDriverAvailability;
    }

    /**
     * 
     * @param array $data
     * @param int $driverId
     * @return array
     */
    public function updateDriverDetails($data, $driverId) {
        $aResponse = array(
            'status' => false,
            'message' => trans('messages.common_error'),
        );
        try {
            DB::beginTransaction();
            $user = User::findOrFail($driverId);
            // Update password or not.
            if (empty($data['password'])) {
                $data['first_name'] = $data['name'];
                $user->update(array_only($data, ['first_name', 'last_name', 'phone', 'activated']));
            } else {
                $data['first_name'] = $data['name'];
                $data['password'] = bcrypt($data['password']);
                $user->update(array_only($data, ['first_name', 'last_name', 'phone', 'password', 'activated']));
            }
            //
            DB::table('driver_question_response')->where('fk_users_id', '=', $driverId)->delete();
            DB::table('driver_availability')->where('fk_users_id', '=', $driverId)->delete();
            $driverDetailResponse       = $this->_saveUpdateDriverDetails($data, $driverId); // save drivers details.
            $driverAvailabilityResponse = $this->_saveDriverAvailability($data['availability'], $driverId); //save drivers availability.
            $driverQuestionResponse     = $this->_saveDriverQuestions($data, $driverId); // save drivers questions.
            if ($driverDetailResponse['status']
                    && $driverAvailabilityResponse['status']
                    && $driverQuestionResponse['status']
            ) {
                DB::commit();
                $aResponse['message'] = trans('messages.vendor_apply_success');
                $aResponse['status']  = true;
            } else {
                //$aResponse['message'] = trans('messages.store_register_success');
                // @todo show wich message;
            }
        } catch (Exception $ex) {
            DB::rollBack();
            CommonHelper::event($ex->getMessage() . "|" . $ex->getFile() . "|" . $ex->getLine(), CommonHelper::USER_REGISTER_LOG_FILE, CommonHelper:: DAILY);
        }
        return $aResponse;
    }

}
