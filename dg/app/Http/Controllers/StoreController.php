<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\User;
use Log;
use App\StoreModel;
use App\StoreDetails;
use App\SessionModel;
use App\Category;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Hash;
use App\Product;
use MangoPay\MangoPayApi;
use App\PaymentModel;
use Exception;
use App\Http\Helper\FileUpload;
use App\Cms;
use Illuminate\Support\Facades\DB;
use App\Http\Helper\CommonHelper;
//use DB;
use App\SubStoreDetails;
use App\EventLog;
use Illuminate\Support\Facades\Cache;

/**
 * StoreController 
 * 
 * PHP version 5.6
 * 
 * @category  Laravel 5.2
 * @package   StoreController
 * @copyright 2016 
 * @license   http://52.50.219.163/
 * @link      http://52.50.219.163/
 * 
 */
class StoreController extends Controller {

    /**
     * Constant for store.
     */
    const FILE_SUB_DIR = 'store';

    /**
     *
     * @var App\StoreModel 
     */
    private $storeModel = NULL;

    /**
     * @var \MangoPay\MangoPayApi
     */
    private $mangopay;

    /**
     *
     * @var App\PaymentModel
     */
    private $paymentModel = NULL;
    
    /**
     *
     * @var App\Category 
     */
    private $_categoryModel = NULL;

    /**
     *
     * @var App\Product 
     */
    private $_productModel = NULL;
    
    /**
     *
     * @var App\User 
     */
    private $_userModel = NULL;

    /**
     * @var FileUploader Object
     */
    private $fileUploader       = null;

    /**
     * 
     * @param \MangoPay\MangoPayApi $mangopay
     */
    public function __construct(MangoPayApi $mangopay) {
        $this->mangopay = $mangopay;
        $this->fileUploader = new FileUpload();
    }

    /**
     * Function to Instatntiate Store Model.
     * 
     * @package StoreController
     * @return object App\PaymentModel Model
     */
    private function getPaymentModel() {
        if ($this->paymentModel == NULL) {
            $this->paymentModel = new PaymentModel($this->mangopay);
        }
        return $this->paymentModel;
    }
    
    /**
     * Function to Instatntiate Store Model.
     * 
     * @package StoreController
     * @return object App\StoreModel Model
     */
    private function getStoreModel() {
        if ($this->storeModel == NULL) {
            $this->storeModel = new StoreModel();
        }
        return $this->storeModel;
    }
    
    /**
     * Function to Instatntiate Category Model.
     * 
     * @package StoreController
     * @return object App\Category Model
     */
    private function getCategoryModel() {
        if ($this->_categoryModel == NULL) {
            $this->_categoryModel = new Category();
        }
        return $this->_categoryModel;
    }

    /**
     * Function to Instatntiate Product Model.
     * 
     * @package StoreController
     * @return object App\Product Model
     */
    private function getProductModel() {
        if ($this->_productModel == NULL) {
            $this->_productModel = new Product();
        }
        return $this->_productModel;
    }
    
    /**
     * Function to Instatntiate User Model.
     * 
     * @package StoreController
     * @return object App\User Model
     */
    private function getUserModel() {
        if ($this->_userModel == NULL) {
            $this->_userModel = new User();
        }
        return $this->_userModel;
    }

    /**
     * Method to save Store's product
     * 
     * @package StoreController
     * @param Request $request
     * @return Array
     */
    public function saveProduct(Request $request) {
      DB::enableQueryLog();
        try{
            $prodId         = $request->get('prodId');
            $isAdd          = $request->get('add');
            $storeModel     = $this->getStoreModel();
            $storeId        = CommonHelper::getSelectedSubStoreId();
            $saveResponse   = $storeModel->saveStoreProducts($prodId, $isAdd, $storeId);
            $storeProducts  = $this->getStoreModel()->getStoreProducts($storeId);
            if (empty($storeProducts)) {
                // Clears cache as near by store as are set In cache day Wise & postcode. Check StoreAddress->getNearbyStores
                // And if stores has no products it should not come.
                Cache::flush();
            }
            
            /* Insert data to the log table for prodcut add/remove from store */
            //dd(DB::getQueryLog());
            $logData = array(
                'users_id'          => $storeId,
                'operation_type'    => EventLog::EVENT_STORE_PRODUCT_CHANGE,
                'al_event'          => serialize(DB::getQueryLog()),
            );
            EventLog::logEvent($logData);
            /* End */
            
            $saveResponse['products']   = $storeProducts;
            return $saveResponse;
        } catch (Exception $ex) {
            Log::error('error in StoreController/saveProduct' . $ex->getMessage());
            return view('errors.500');
        }
    }

    /**
     * Method to Get Product to be listed on Store Product page
     * 
     * @package StoreController
     * @param Int $selectedId
     * @param Int $selectedSubCatId
     * @return resources/views/store mixed
     */
    public function getProducts($selectedId = NULL, $selectedSubCatId = NULL) {
        try{
            $parentStoreId      = Auth::user()->id;
            $storeId            = CommonHelper::getSelectedSubStoreId();
            $allowProductUpload = $this->getStoreModel()->getStoreKYCStatus($parentStoreId);
            $aCatTree           = $this->getCategoryModel()->getCategoryTree();
            $storeProducts      = $this->getStoreModel()->getStoreProducts($storeId);
            return view('store.my_products', compact('aCatTree', 'storeProducts'
                ,'selectedId', 'selectedSubCatId', 'allowProductUpload'));
        } catch (Exception $ex) {
            Log::error(__METHOD__ . $ex->getMessage());
            return view('errors.500');
        }
    }

    /**
     * Method to Get Product Sub Categories.
     * 
     * @package StoreController
     * @param Int $pCatId
     * @return array
     */
    public function getSubcatTree($pCatId) {
        try{
            return $this->_getSubSubcatTree($pCatId);
        } catch (Exception $ex) {
            Log::error('error in StoreController/getSubcatTree' . $ex->getMessage());
            return view('errors.500');
        }
    }

    /**
     * Method to get all sub categories.
     * 
     * @package StoreController
     * @param Int $subCatId
     * @return Array
     */
    public function getSubSubcatTree($subCatId) {
        try{
            return $this->_getSubSubcatTree(null, $subCatId);
        } catch (Exception $ex) {
            Log::error(__METHOD__ . $ex->getMessage());
            return view('errors.500');
        }
    }

    protected function _getSubSubcatTree($pCatId, $subCatId = null) {
        $aSubCatTree        = $aSubCats = $response = array();
        $aCatTree           = $this->getCategoryModel()->getCategoryTree();
        $subCatName = '';
        if (empty($subCatId)) {
            if (isset($aCatTree['categories'][$pCatId]['subCategory'])) {
                $firstSubCat    = reset($aCatTree['categories'][$pCatId]['subCategory']);
                $aSubCatTree    = $firstSubCat['subSubCat'];
                $aSubCats       = $aCatTree['categories'][$pCatId]['subCategory'];
                $subCatId       = $firstSubCat['id'];
                $subCatName     = $firstSubCat['name'];
            }
        } else {
            foreach ($aCatTree['categories'] as $pCatId => $aPCatDetails) {
                if (isset($aPCatDetails['subCategory'][$subCatId])) {
                    $aSubCatTree = $aPCatDetails['subCategory'][$subCatId]['subSubCat'];
                    $aSubCats    = $aPCatDetails['subCategory'];
                    $subCatName  = $aPCatDetails['subCategory'][$subCatId]['name'];
                    break;
                }
            }
        }
        $nearCheck          = false;
        $getProducts        = $this->getProductModel()->getSubCatProducts($aSubCatTree, $subCatId, $nearCheck);
        $prodObj = array();
        if ($getProducts['haSubCat']) {
            if (!empty($getProducts['products'])) {
                foreach ($getProducts['products'][0] as $key => $keyDetails) {
                    $aSubCatDetail['name']        = $key;
                    $aSubCatDetail['products']    = $keyDetails;
                    array_push($prodObj, $aSubCatDetail);
                }
                $getProducts['products'] = $prodObj;
            }
        }
        $storeId            = CommonHelper::getSelectedSubStoreId();
        $storeProducts      = $this->getStoreModel()->getStoreProducts($storeId);
        $view = view('store.partials.product_category', 
            [
                'aSubCats' => $aSubCats,
                'subcatDetails' => $getProducts,
                'selsubCatId' => $subCatId,
                'subCatName' => $subCatName,
                'storeProducts' => $storeProducts,
            ]
        );
        $view               = $view->render();
        $response['html_content'] = (string) $view;
        return $response;
    }

    /**
     * Method to edit Store Profile.
     * 
     * @package StoreController
     * @return resources/views/store mixed
     */
    public function editProfile() {
        try {
            $userId = CommonHelper::getSelectedSubStoreId();
            $userData = $this->getUserModel()->getSubStoreDetails($userId);
            $fileSubDir = self::FILE_SUB_DIR;
            return view('store.editProfile', compact('userData', 'fileSubDir'));
        } catch (Exception $ex) {
            Log::error('error in StoreController/editProfile' . $ex->getMessage());
            return view('errors.500');
        }
    }

    /**
     * Method to save profile of store.
     * 
     * @package StoreController
     * @param Request $request
     * @return resources/views/store mixed
     */
    public function saveProfile(Request $request) {
        try {
            $this->validate($request, [
                'store_name' => 'required',
                'phone' => 'numeric|digits_between:8,25'
            ]);
            SubStoreDetails::where('fk_users_id', $request->get('id'))
                        ->update($request->only('store_name'));
            User::where('id', $request->get('id'))
                        ->update($request->only('first_name', 'last_name', 'phone'));
            return redirect()->route('store.profile');
        } catch (Exception $ex) {
            Log::error('error in StoreController/saveProfile' . $ex->getMessage());
            return view('errors.500');
        }
    }

    /**
     * Method to return store profile.
     * 
     * @package StoreController
     * @return resources/views/store mixed
     */
    public function getProfile() {
        try{
            $storeId                = CommonHelper::getSelectedSubStoreId();
            $storeuserData          = $this->getUserModel()->getSubStoreDetails($storeId);
            $cmsUserType            = config('cms.user_type');
            $userTypeToExclude      = $cmsUserType['User'];
            $cmsModel               = new Cms();
            $userLegaldata          = $cmsModel->getUserCmsData($userTypeToExclude);
            return view('store.profile', compact('storeuserData', 'userLegaldata'));
        } catch (Exception $ex) {
            Log::error('error in StoreController/getProfile' . $ex->getMessage());
            return view('errors.500');
        }
    }

    /**
     * Controller method to get Sales Page data
     * 
     * @package StoreController
     * @return resources/views/store mixed
     */
    public function getSalesPageData() {
        try{
            $storeId                = CommonHelper::getSelectedSubStoreId();
            $storeSalesData         = $this->getStoreModel()->getStoreSalesData($storeId);
            $storeProducts          = $this->getStoreModel()->getStoreProducts($storeId);
            $bestSellerProducts     = $this->getStoreModel()->getBestSellerProducts();
            $storeBestSellerProducts = $this->getProductModel()->getStoreBestSellerProducts($storeId);
            $chartData = array();
            foreach ($storeSalesData['last_week_sales']['week_detail'] as $weekDay => $dayRevenue) {
                array_push($chartData, (float) $dayRevenue);
            }
            return view('store.sales', compact('storeSalesData', 'storeProducts', 'bestSellerProducts', 'chartData', 'storeBestSellerProducts'));
        } catch (Exception $ex) {
            Log::error('error in StoreController/getSalesPageData' . $ex->getMessage());
            return view('errors.500');
        }
    }

    /**
     * Methiod to dispaly dashboard.
     * 
     * @package StoreController
     * @return resources/views/store mixed
     */
    public function getDashboard() {
        try{
            $storeId                = CommonHelper::getSelectedSubStoreId();
            $storeSalesData         = $this->getStoreModel()->getStoreSalesData($storeId);
            $catTree                = $this->getCategoryModel()->getCategoryTree();
            $storeuserData          = $this->getUserModel()->getSubStoreDetails($storeId);
            return view('store.dashboard', compact('storeSalesData',
                'storeuserData', 'catTree'));
        } catch (Exception $ex) {
            Log::error('error in StoreController/getDashboard' . $ex->getMessage());
            return view('errors.500');
        }
    }

    /**
     * Get order Details on search.
     * 
     * @package StoreController
     * @param Request $request
     * @return resources/views/store mixed
     */
    public function getorderSearch(Request $request) {
        try {
            $storeId                = CommonHelper::getSelectedSubStoreId();
            if ($request->isMethod('post') && isset($request['order_id']) && $request['order_id'] != '') {
                $orderItem = [];
                $totalPrice = 0.00;
                $orderNumber = $request['order_id'];
                $orderItem = $this->getStoreModel()->updateVendorSession($orderNumber, $storeId);
                return view('store.order-edit-listing', compact('orderItem', 'orderNumber', 'totalPrice'));
            } else {
                $history = $this->getStoreModel()->getStoreHistory($storeId,NULL, 3);
                return view('store.order-search-history', compact('history'));
            }
        } catch (Exception $ex) {
            Log::error('error in StoreController/getOrderSearch' . $ex->getMessage());
            return view('errors.500');
        }
    }

    /**
     * Update Session on every +, - click.
     * 
     * @package StoreController
     * @param Request $request
     * @return Int
     */
    public function updateSession(Request $request) {
        try {
            if (isset($request['itemId']) && isset($request['newQty']) && isset($request['action'])) {
                $sessionData = SessionModel::getSessionVariable('vendor.orderData');
                $total = 0;
                foreach ($sessionData as $key => $value) {
                    if ($key !== 'orderId' && $key !== 'total') {
                        if ($value['id_sales_order_item'] == $request['itemId']) {
                            SessionModel::putSessionVariable('vendor.orderData.' . $key . '.newQty', $request['newQty']);
                            $total += $value['store_price'];
                        }
                    }
                }
                $sessionTotal = SessionModel::getSessionVariable('vendor.orderData.total');
                if ($request['action'] == 'add') {
                    $finalSum = $sessionTotal + $total;
                } else {
                    $finalSum = $sessionTotal - $total;
                }
                SessionModel::putSessionVariable('vendor.orderData.total', $finalSum);
                return $finalSum;
            }
        } catch (Exception $ex) {
            Log::error('error in StoreController/updateSession' . $ex->getMessage());
            return view('errors.500');
        }
    }

    /**
     * Display Driver Page.
     * 
     * @package StoreController
     * @param Request $request
     * @return resources/views/store mixed
     */
    public function vendorVerification(Request $request) {
        try {
            SessionModel::forgetSessionVariable('vendor.riderId');
            $orderNumber = SessionModel::getSessionVariable('vendor.orderData.orderId');
            return view('store.rider-confirmation', compact('orderNumber'));
        } catch (Exception $ex) {
            Log::error('error in StoreController/vendorVerification' . $ex->getMessage());
            return view('errors.500');
        }
    }

    /**
     * Verify driver on basis of userName & Password.
     * 
     * @package StoreController
     * @param Request $request
     * @return resources/views/store mixed
     */
    public function verifyVendor(Request $request) {
        $errorMessage = 'Some Thing went Wrong.';
        try {
            if ($request->isMethod('post')) {
                $this->validate($request, [
                    'riderEmail' => 'required',
                    'riderPassword' => 'required',
                ]);
                SessionModel::forgetSessionVariable('vendor.riderId');
                $riderExist = User::where('email', '=', $request['riderEmail'])
                        ->where('fk_users_role', '=', \Config::get('appConstants.driver_role_id'))
                        ->where('activated', '=', 1)
                        ->first();
                if (Hash::check($request['riderPassword'], $riderExist['password'])) {
                    SessionModel::putSessionVariable('vendor.riderId', $riderExist['id']);
                    return redirect()->route('store.riderConfirmation');
                } else {
                    $errorMessage = 'Invalid Credentials.';
                }
            }
        } catch (Exception $ex) {
            Log::error('error in StoreController/verifyVendor' . $ex->getMessage());
            return view('errors.500');
        }
        return view('store.rider-confirmation')->withErrors($errorMessage);
    }

    /**
     * On succesful rider verification.
     * 
     * @package StoreController
     * @return resources/views/store mixed
     */
    public function riderConfirmation() {
        try {
            if (SessionModel::checkInSession('vendor.orderData')) {
                $orderNumber = SessionModel::getSessionVariable('vendor.orderData.orderId');
                $orderItem = $this->getStoreModel()->getOrderArray();
                $totalPrice = $this->getStoreModel()->getTotalPrice($orderItem);
                return view('store.order-accept-edit', compact('orderItem', 'orderNumber', 'totalPrice'));
            }
            \Session::flash('message', 'Something went wrong!!');
            return view('store.rider-confirmation')->withErrors('error', 'Invalid Credentials.');
        } catch (Exception $ex) {
            Log::error('error in StoreController/riderConfirmation' . $ex->getMessage());
            return view('errors.500');
        }
    }

    /**
     * To dispaly order details to rider.
     * 
     * @package StoreController
     * @return resources/views/store mixed
     */
    public function orderData() {
        try {
            if (SessionModel::checkInSession('vendor.orderData')) {
                $orderNumber = SessionModel::getSessionVariable('vendor.orderData.orderId');
                $orderItem = $this->getStoreModel()->getOrderArray();
                $totalPrice = $this->getStoreModel()->getTotalPrice($orderItem);
                return view('store.order-edit-listing', compact('orderItem', 'orderNumber', 'totalPrice'));
            }
        } catch (Exception $ex) {
            Log::error('error in StoreController/orderData' . $ex->getMessage());
            return view('errors.500');
        }
    }

    /**
     * Transaction Complate.
     * 
     * @package StoreController
     * @return resources/views/store mixed
     */
    public function orderComplete() {
        $status             = FALSE;
        $message            = 'Something went Wrong';
        try {
            if (SessionModel::checkInSession('vendor.orderData')) {
                $orderNumber    = SessionModel::getSessionVariable('vendor.orderData.orderId');
                $riderId        = SessionModel::getSessionVariable('vendor.riderId');
                if ($riderId) {
                    $storeId            = CommonHelper::getSelectedSubStoreId();
                    $updateItemResult   = $this->getStoreModel()->updateOrderItem($storeId, $riderId);
                    $status             = $updateItemResult['status'];
                    $message            = $updateItemResult['message'];
                    return view('store.transaction-complete', compact('orderNumber', 'status', 'message'));
                } else {
                    Log::error('OrderComplete|Rider Id not Present');
                }
            } else {
                Log::error('OrderComplete|orderData not Present in session.');
            }
        } catch (Exception $ex) {
            Log::error('error in StoreController/orderComplete' . $ex->getMessage());
            return view('errors.500');
        }
        return view('store.transaction-complete', compact('orderNumber', 'status', 'message'));
    }

    /**
     * Get detailed history of order on basis od sales_order_id.
     * 
     * @package StoreController
     * @param Int $salesOrderId
     * @return resources/views/store mixed
     */
    public function getDetailedHistory($salesOrderId) {
        try {
            $storeId            = CommonHelper::getSelectedSubStoreId();
            $history            = $this->getStoreModel()->getStoreHistory($storeId, $salesOrderId);
            return view('store.order-history', compact('history'));
        } catch (Exception $ex) {
            Log::error('error in StoreController/getDetailedHistory' . $ex->getMessage());
            return view('errors.500');
        }
    }
    
    /**
     * Fetches product detail on the basis of Product Id.
     * 
     * @package StoreController
     * @param Int $productId
     * @return resources/views/store mixed
     */
    public function getProductDetail($productId) {
        try {
            $storeId            = CommonHelper::getSelectedSubStoreId();
            $product            = $this->getProductModel()->getProductDetails($productId);
            $productInstock     = $this->getStoreModel()->isProductInStock($productId, $storeId);
            $parentStoreId      = Auth::user()->id;
            $allowProductUpload = $this->getStoreModel()->getStoreKYCStatus($parentStoreId);
            if ($product) {
                return view('store.product-details', compact('product', 'productInstock', 'allowProductUpload'));
            } else {
                return view('errors.404');
            }
        } catch (Exception $ex) {
            Log::error('error in StoreController/getProductDetail' . $ex->getMessage());
            return view('errors.500');
        }
    }

    /**
     * Function to upload store profile image & update it in database.
     * 
     * @package StoreController
     * @param Request $request
     * @return resources/views/store
     */
    public function uploadImage(Request $request) {
        try {
            $rules = array(
                'image' => 'required|mimes:jpeg,jpg|max:10000'
            );
            $validateFile       = $this->validateFile($request);
            if ($validateFile['status']) {
                $validator      = Validator::make($request->all(), $rules);
                if ($validator->fails()) {
                    return Redirect::to('/store/editProfile')->withInput()->withErrors($validator);
                }
                $userId         = CommonHelper::getSelectedSubStoreId();
                $user           = User::findOrFail($userId);
                $request        = $this->saveFiles($request, self::FILE_SUB_DIR, '95', '75');
                $user->update(['image' => $request['image']]);
                return redirect()->route('store.editProfile');
            } else {
                return Redirect::to('/store/editProfile')->withInput()->withErrors($validateFile['error']);
            }
        } catch (Exception $ex) {
            Log::error('error in StoreController/uploadImage' . $ex->getMessage());
            return view('errors.500');
        }
    }

    /**
     * To get order history data with pagination.
     * 
     * @package StoreController
     * @return resources/views/store mixed
     */
    public function getHistoryData() {
        try {
            $storeId    = CommonHelper::getSelectedSubStoreId();
            $history    = $this->getStoreModel()->getStoreHistory($storeId, NULL, 5);
            return view('store.order-history-all', compact('history'));
        } catch (Exception $ex) {
            Log::error('error in StoreController/getHistoryData' . $ex->getMessage());
            return view('errors.500');
        }
    }
    
    /**
     * To get order details for store.
     * 
     * @package StoreController
     * @param type $orderNumber
     * @return type
     */
    public function trackorder($orderNumber) {
        try {
            $storeId    = CommonHelper::getSelectedSubStoreId();
            $orderId    = $this->getStoreModel()->getOrderId($orderNumber);
            $orderData  = $this->getStoreModel()->getStoreHistory($storeId, $orderId[0]);
            if (!empty($orderData)) {
                return view('store.order-status', compact('orderData', 'orderNumber'));
            } else {
                return view('errors.404');
            }
        } catch (Exception $ex) {
            Log::error('error in StoreController/trackorder' . $ex->getMessage());
            return view('errors.500');
        }
    }

    /**
     * Method to edit Store Address.
     * 
     * @package StoreController
     * @return resources/views/store mixed
     */
    public function editAddress() {
        try {
            $userId         = CommonHelper::getSelectedSubStoreId();
            $storeAddress   = $this->getStoreModel()->getStoreAddressByStoreId($userId);
            return view('store.editAddress', compact('storeAddress'));
        } catch (Exception $ex) {
            Log::error('error in StoreController/editProfile' . $ex->getMessage());
            return view('errors.500');
        }
    }

    /**
     * Method to save address of store.
     * 
     * @param Request $request
     * @return resources/views/store mixed
     */
    public function saveAddress(Request $request) {
        try {
            $storeModel         = $this->getStoreModel();
            $validationRules    = $storeModel->getValidationRulesForStoreAddress();
            $data               = $request->all();
            $validation         = Validator::make($data, $validationRules, $storeModel->validationMessagesForAddress());
            if ($validation->fails()) {
                return redirect()->back()->withInput()->withErrors($validation);
            } else {
                $storeId    = $request->get('id');
                $userId         = CommonHelper::getSelectedSubStoreId();
                if ($userId != $storeId) {
                    throw new Exception('Invalid User');
                }
                $response   = $storeModel->saveStoreAddress($data, $storeId);
                return redirect()->back()->with('status', $response['message']);
            }
        } catch (Exception $ex) {
            return redirect()->route('store.editAddress')->with('warning', trans('messages.common_error'));
        }
    }
    
    
    /**
     * Method to get store available timings.
     * 
     * @return resources/views/store mixed
     */
    public function getStoreTime() {
        try {
            $storeId    = CommonHelper::getSelectedSubStoreId();
            $timings    = array();
            $time       = config('appConstants.site_timings');
            $days       = config('appConstants.store_days');
            $time       = config('appConstants.store_timings');
            return view('store.store-availability', compact('timings', 'time', 'days', 'storeId'));
        } catch (Exception $ex) {
            return redirect()->route('store.store-availability')->with('warning', trans('messages.common_error'));
        }
    }
    
    /**
     * Method to update store time in database.
     * 
     * @param Request $request
     * @return resources/views/store mixed
     */
    public function updateStoreTime(Request $request){
        try {
            $storeId        = CommonHelper::getSelectedSubStoreId();
            $this->getStoreModel()->updateSiteTimeEntries($storeId, $request->all());
            $message = trans('messages.store_timings_updated');
            \Session::flash('message', $message);
            return redirect()->route('store.profile')->with('status', $message);
        } catch (Exception $ex) {
            return redirect()->route('store.editAddress')->with('warning', trans('messages.common_error'));
        }
    }

    /**
     *
     * @param Request $request
     *
     * @return View Object
     */
    public function registerForKyc(Request $request) {
        $storeId            = isset(Auth::user()['id']) ? Auth::user()['id'] : FALSE;
        $mangoUserData      = $this->getUserModel()->getUserCompleteMangoPayDetails($storeId);
        $userKycDetails     = $mangoUserData['mangoUserKyc'];
        $userKycDetails     = $userKycDetails->toArray();
        $userStoreDetails   = $mangoUserData['storeDetails']->toArray();
        $userStoreDetails['store_name'] = $mangoUserData['subStoreDetails']->store_name;
        $userMangoDetails               = $mangoUserData['mangoUser'];
        $userStoreDetails               = $this->getStoreModel()->getVendorDetailsForAccountCreation($storeId);
        if (!empty($userMangoDetails)) {
            return view('store.legalUser.kyc_status', compact('userKycDetails', 'userStoreDetails'));
        } else {
            // Error case Account is not created yet.
            $businessType = '';	 
            $legalAddress = '';
            $registeredAddress = '';
            return view('store.legalUser.create_form', compact('businessType', 'userStoreDetails', 'legalAddress', 'registeredAddress'));
        }
    }

    
    /**
     * Method to get latest status of document from Mangopay
     *
     * @param Request $request
     * @return array
     */
    public function getKYCDocumentStatus(Request $request) {
        $response = array(
            'status'    => false,
            'message'   => '',
            'data'      => '',
        );
        try {
            $storeId        = isset(Auth::user()['id']) ? Auth::user()['id'] : FALSE;
            $aRequestData   = $request->all();
            $documentId     = $aRequestData['docId'];
            $mangoPayUser   = $this->getPaymentModel()->getMangoUserLegal($storeId);
            if ($mangoPayUser['status']) {
                $userMangoPayId         = $mangoPayUser['data']['mangoUser']['mango_users_id'];
                $kycDocumentResponse    = $this->getPaymentModel()->getKycDetails($userMangoPayId, $documentId);
                if ($kycDocumentResponse['status']) {
                    $response['status']     = true;
                    $response['data']       = $kycDocumentResponse['data'];
                }
            } else {
                $response['message'] = $mangoPayUser['message'];
            }
        } catch (Exception $ex) {
            $response['message'] = $ex->getMessage();
        }
        return $response;
    }

    /**
     * Upload KYC Document for user.
     *
     * @param Request $request
     * @return mixed
     */
    public function uploadKycDocument(Request $request) {
        $validateFile = $this->fileUploader->validateFiles();
        if ($validateFile['status']) {
            $request            = $this->fileUploader->saveFiles($request, 'kyc');
            $aRequestData       = $request->all();
            $storeId            = isset(Auth::user()['id']) ? Auth::user()['id'] : FALSE;
            $mangoPayUser       = $this->getPaymentModel()->getMangoUserLegal($storeId);
            if ($mangoPayUser['status']) {
                $userMangoPayId         = $mangoPayUser['data']['mangoUser']['mango_users_id'];
                $requiredImage          = $aRequestData['document_type'];
                $aImages                = config('mangopay.kyc_users_image');
                $requiredImagesForUser  = isset($aImages[$aRequestData['business_type']]) ? $aImages[$aRequestData['business_type']] : array() ;
                $imageName              = isset($aRequestData[$requiredImage]) ? $aRequestData[$requiredImage] : "" ;
                $requiredImageDetails   = isset($requiredImagesForUser[$requiredImage]) ? $requiredImagesForUser[$requiredImage] : "" ;
                $documentResult = array(
                    'status'    => false,
                    'message'   => 'Invalid Image',
                );
                if (!empty($imageName) && (!empty($requiredImageDetails))) {
                    $documentResult         =  $this->getPaymentModel()->uploadUserKycDocument($storeId, $userMangoPayId, $imageName, $requiredImageDetails);
                }
                if ($documentResult['status']) {
                    return redirect()
                        ->back();
                } else {
                    return redirect()
                    ->back()
                    ->withInput($request->all())->withErrors($documentResult['message']);
                }
            } else {
                return redirect()
                    ->back()
                    ->withInput($request->all())->withErrors($mangoPayUser['message']);
            }
        } else {
            return redirect()
                    ->back()
                    ->withInput($request->all())->withErrors($validateFile['error']);
        }
    }

    /**
     * Fetches Best Seller Products for store & overall
     *
     * @return resources/views/store mixed
     */
    public function getBestSeller() {
        try {
            $storeId                    = CommonHelper::getSelectedSubStoreId();
            $storeProducts              = $this->getStoreModel()->getStoreProducts($storeId);
            $bestSellerProducts         = $this->getStoreModel()->getBestSellerProducts();
            $storeBestSellerProducts    = $this->getProductModel()->getStoreBestSellerProducts($storeId);
            return view('store.best-seller', compact('storeProducts', 'bestSellerProducts', 'storeBestSellerProducts'));
        } catch (Exception $ex) {
            Log::error(__METHOD__ . $ex->getMessage());
            return view('errors.500');
        }
    }

    /**
     * Method to render Seller Agreement Page for Vendor
     *
     * @return view Object
     */
    public function renderSellerAgreement() {
        $type       = config('cms.user_type.Store');
        $title      = config('cms.page_seller_aggrement');
        $cmsModel   = new Cms();
        $cmsData    = $cmsModel->getCmsPageContent($type, $title);
        $companyName = 'test';
        $storeDetails = array(
            'company_name' => '',
            'company_number' => '',
            'company_address' => '',
            'store_name' => '',
            'store_address' => '',
        );
        $userId         = isset(Auth::user()['id']) ? Auth::user()['id'] : FALSE;
        $userData       = $this->getUserModel()->getStoreDetails($userId);
        $storeName      = $this->getUserModel()->getSubStoreDetails($userId);
        if ($userData['storeDetails']->business_type == 'SOLETRADER') {
            $storeAddress   = $this->getStoreModel()->getStoreAddressByStoreId($userId);
            $storeDetails['store_name'] = $storeName['subStoreDetails']->store_name;
            $storeDetails['store_address'] = $storeAddress->address . ", " . $storeAddress->city. ", " . $storeAddress->state
                    . ", " . $storeAddress->pin;
        } else {
            $storeDetails['company_name'] = $userData['storeDetails']->cname;
            $storeDetails['company_number'] = $userData['storeDetails']->company_number;
            $storeAddress       = $this->getUserModel()->getStoreLegalAddress($userId);
            $storeDetails['company_address'] = '';
            if (!empty($storeAddress['storeLegalAddress'])) {
                $storeDetails['company_address'] = $storeAddress['storeLegalAddress']->address_line_1 . ", " . $storeAddress['storeLegalAddress']->city. ", " . $storeAddress['storeLegalAddress']->region
                    . ", " . $storeAddress['storeLegalAddress']->pin;
            }
        }
        return view('store.legal.seller_agreement', compact('cmsData', 'storeDetails'));
    }

    /**
     * Method to render Product List Page for Vendor
     *
     * @return view Object
     */
    public function renderProductList() {
         return view('store.legal.product_list');
    }

    /**
     * Method to render Courier Agreement Page for Vendor
     *
     * @return view Object
     */
    public function renderCourierAgreement() {
        $type       = config('cms.user_type.Store');
        $title      = config('cms.page_courier_agreement');
        $cmsModel   = new Cms();
        $cmsData    = $cmsModel->getCmsPageContent($type, $title);
        return view('store.legal.courier_agreement', compact('cmsData'));
    }
    
    /**
     * Fetches Best Seller Products for store & overall
     *
     * @return resources/views/store mixed
     */
    public function checkOpeningTime() {
        try {
            $storeTimings = $this->getStoreModel()->getOpeningTime();
            $timings = array();
            if (!empty($storeTimings)) {
                foreach ($storeTimings as $key => $value) {
                    $timings[$value->day]['is_24hrs'] = $value->is_24hrs;
                    $timings[$value->day]['is_closed'] = $value->is_closed;
                    $timings[$value->day]['theDay'] = $value->day;
                    if ($value->is_24hrs) {
                        $close_time = "24:00";
                    } else {
                        $close_time = $value->close_time;
                    }
                    $tem_arr = $value->open_time . " - " . $close_time;
                    $timings[$value->day]['schedule'][] = $tem_arr;
                }
            } else {
                $timings['schedule'][] = array();
                $timings['is_24hrs'] = 0;
                $timings['is_closed'] = 0;
                $timings['theDay'] = "Monday";
            }
            //print_r($timings);exit;
            $view = view('partials.store_timing_content', ['openingTime' => $timings]);
            $view = $view->render();
            $response['html_content'] = (string) $view;
        } catch (Exception $ex) {
            Log::error(__METHOD__ . $ex->getMessage());
            return view('errors.500');
        }
        return $response;
    }

    /**
     * Function to fetch comapny house details.
     * 
     * @param Request $request
     * @return array
     */
    public function getCompanyDetails(Request $request) {
         $response = array(
            'status' => false,
            'message' => 'Error',
            'data' => array()
        );
        $companyDetails    = $this->getStoreModel()->getStoreCompanyHouseDetails($request->get('store_name'), TRUE);
        if ($companyDetails['status'] == TRUE) {
            $response['status']     = $companyDetails['status'];
            $response['data']       = $companyDetails['data'];
            $response['message']    = $companyDetails['message'];
        } else {
            $response['message'] = 'No Details Found';
        }
        return $response;
    }
    
    /**
     * Function to return officer related information.
     * 
     * @param Request $request
     * @return Array
     */
    public function getOfficerDetails(Request $request) {
         $response = array(
            'status' => false,
            'message' => 'Error',
            'data' => array()
        );
        $officerDetails    = $this->getStoreModel()->getCompanyDirectorDetails($request->get('store_number'));
        if ($officerDetails['status'] == TRUE) {
            $response['status']     = $officerDetails['status'];
            $response['data']       = $officerDetails['data'];
            $response['message']    = $officerDetails['message'];
        } else {
            $response['message'] = 'No Details Found';
        }
        return $response;
    }
    
    /**
     * This function will update vendor price.
     * 
     * @param Int $productId
     * @param Decimal $price
     */
    public function updateStorePrice(Request $request) {
        $response = [
            'status' => FALSE,
            'message'=> 'Some Error Occured',
            'data' => ''
        ];
        $rules = array(
                'product_id' => 'required',
                'vendor_price' => 'required|between:0,99999.99',
            );
            $validator = Validator::make($request->all(), $rules);
            $productId = $request->get('product_id');
            if ($validator->fails()) {
                $response = [
                    'status' => FALSE,
                    'message'=> 'Price cannot be empty',
                ];
                return $response;
            }
        $price = $request->get('vendor_price');
        $updateData = array(
            'vendor_price' => $price,
        );
        $storeId                    = CommonHelper::getSelectedSubStoreId();
        DB::table('products_store')->where('fk_product_id', $productId)
                ->where('fk_user_id', $storeId)
                ->update($updateData);
        return $response = [
                    'status' => TRUE,
                    'message'=> 'Price Updated Successfully',
                    'data' => ['vendor_price' => $price]
                ];
    }

    public function addStore(Request $request) {
        $response = array(
            'status' => false,
            'message' => trans('messages.common_error'),
            'data' => array(),
        );
        $data               = $request->all();
        $data               = $data['data'];
        $data['state']      = config('appConstants.store_default_country');
        $emailSuffix        = config('appConstants.vendor_store_default_email_suffix');
        $data['fk_users_role']  =  config('appConstants.vendor_role_id');
        $data['activated']      =  1;
        $userId                 = isset(Auth::user()['id']) ? Auth::user()['id'] : FALSE;
        $data['fk_parent_id']   =  $userId;
        //$data['email']          = time().$emailSuffix; // random Unique email generated for new substores.
        $userType               = config('mangopay.legal_user_type');
        $businessType           = $userType['SOLETRADER'];
        $storeModel             = new StoreModel();
        $validationRules        = $storeModel->getValidationRulesForRegistration($businessType, $legalValidations = false);
        $validation             = Validator::make($data, $validationRules, $storeModel->validationMessagesForAddress());
        if ($validation->fails()) {
            $errors =  $validation->errors()->toArray();
            foreach ($errors as $error) {
                $response['message'] = $error[0];
                break;
            }
        } else {
            $response = $storeModel->addNewStore($data);
        }
        return $response;
    }

    /**
     * 
     * @param type $subStoreId
     */
    public function setDefaultSubStore($subStoreId) {
        if (!empty($subStoreId)) {
            CommonHelper::setSelectedSubStoreId($subStoreId);
        }
        return redirect()->back();
    }

    /**
     * To set Password for registored vendor after succesful verification.
     * 
     * @package HomeController
     * @param Request $request
     * @return mixed
     */
    public function createPassword(Request $request) {
        if ($request->isMethod('post')) {
            $data = $request->all();
            $rules = array(
                'password' => 'required|min:6|confirmed',
            );
            $validation = Validator::make($data, $rules);
            if ($validation->fails()) {
                return back()->withInput()->withErrors($validation);
            } else {
                $update = DB::table('users')->where('id', $data['id'])
                        ->update(['password' => bcrypt($data['password']), 'activated' => $data['activated']]);
                if ($update) {
                    $storeId        = $data['id'];
                    $this->createVendorLegalAccount($storeId);
                    return redirect()->route('customer.landing');
                }
                else {
                    return redirect()
                        ->back()
                        ->withInput($request->all())->withErrors('error', 'Something went wrong, Please get in touch with Admin.');
                }
            }
        }
        return view('home.createPassword');
    }

    /**
     * Method to create legal user at mangoPay for store.
     * used as internal function
     * 
     * @param int $storeId
     */
    public function createVendorLegalAccount($storeId, $request = NULL) {
        try {
            if($request != NULL){
                $aUserDetails = $request;
                $this->getStoreModel()->saveStoreLegalAddress($request, $storeId);
                $this->getStoreModel()->saveStoreRegisteredAddress($request, $storeId);
                $exist = StoreDetails::where('fk_users_id', '=', $storeId)->first();
                $dob = $request['legal_representative_dob_yy']. '-' .$request['legal_representative_dob_mm']. '-'. $request['legal_representative_dob_dd'];
                if ($exist['fk_users_id']) {
                    StoreDetails::where('fk_users_id', '=', $exist['fk_users_id'])
                            ->update(['legal_fname'=> $request['legal_representative_fname'], 'legal_lname' => $request['legal_representative_lname'], 'legal_dob' => $dob]);
                } else {
                    StoreDetails::create(['legal_fname'=> $request['legal_representative_fname'], 'legal_lname' => $request['legal_representative_lname'], 'legal_dob' => $dob]);
                }
            }else{
                $aUserDetails = $this->getStoreModel()->getVendorDetailsForAccountCreation($storeId);
            }
            $mangoPayUser = $this->getPaymentModel()->getMangoUserLegal($storeId, $aUserDetails);
            return $mangoPayUser;
        } catch (Exception $ex) {
            Log::error(__METHOD__ . $ex->getMessage());
            return view('errors.500');
        }
    }

    /**
     * Method to create legal user at mangoPay for store.
     * Used as route
     * 
     * @param int $storeId
     */
    public function createVendorLegalAccountNew(Request $request) {
        try {
            $storeId = isset(Auth::user()['id']) ? Auth::user()['id'] : FALSE;
            $mangoPayUser = $this->createVendorLegalAccount($storeId,$request->all());
            if ($mangoPayUser['status'] == FALSE) {
                return redirect()->back()->withInput()->withErrors($mangoPayUser['message']->Errors);
            }
            return redirect()->back()->withInput();
        } catch (Exception $ex) {
            Log::error(__METHOD__ . $ex->getMessage());
            return view('errors.500');
        }
    }

    /**
     * Method to render Legal Page for Web & Apps
     *
     * @param string $isApi
     * @return view Object
     */
    public function getCmsPageContent($urlPath, $isApi = NULL) {
        $templatePrefix = 'store.cms';
        if (!empty($isApi)
            && ($isApi == 'api')) {
            $templatePrefix = 'api';
        }
        $type           = config('cms.user_type.Store');
        $cmsModel       = new Cms();
        $cmsData        = $cmsModel->getCmsPageData($type, $urlPath);
        $storeDetails   = array();
        if ($cmsData->title == config('cms.page_seller_aggrement')) {
            $storeDetails   = $this->getStoreSellerAggrementData();
        }
        return view($templatePrefix .'.cms', compact('cmsData', 'storeDetails'));
    }

    /**
     * 
     * @return array $storeDetails
     */
    public function getStoreSellerAggrementData() {
        $storeDetails = array(
            'company_name' => '',
            'company_number' => '',
            'company_address' => '',
            'store_name' => '',
            'store_address' => '',
        );
        $userId         = isset(Auth::user()['id']) ? Auth::user()['id'] : FALSE;
        $userData       = $this->getUserModel()->getStoreDetails($userId);
        $storeName      = $this->getUserModel()->getSubStoreDetails($userId);
        if ($userData['storeDetails']->business_type == 'SOLETRADER') {
            $storeAddress   = $this->getStoreModel()->getStoreAddressByStoreId($userId);
            $storeDetails['store_name'] = $storeName['subStoreDetails']->store_name;
            $storeDetails['store_address'] = $storeAddress->address . ", " . $storeAddress->city. ", " . $storeAddress->state
                    . ", " . $storeAddress->pin;
        } else {
            $storeDetails['company_name'] = $userData['storeDetails']->cname;
            $storeDetails['company_number'] = $userData['storeDetails']->company_number;
            $storeAddress       = $this->getUserModel()->getStoreLegalAddress($userId);
            $storeDetails['company_address'] = '';
            if (!empty($storeAddress['storeLegalAddress'])) {
                $storeDetails['company_address'] = $storeAddress['storeLegalAddress']->address_line_1 . ", " . $storeAddress['storeLegalAddress']->city. ", " . $storeAddress['storeLegalAddress']->region
                    . ", " . $storeAddress['storeLegalAddress']->pin;
            }
        }
        return $storeDetails;
    }

}
