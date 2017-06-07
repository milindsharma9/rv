<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
//use App\Category;
//use App\Event;
use App\User;
//use App\Product;
use Intervention\Image\Facades\Image;
use Validator;
//use App\UserAddress;
//use App\Occasion;
use App\Http\Controllers\Traits\FileUploadTrait;
//use App\ValidPostcode;
//use App\SalesOrder;
//use MangoPay\MangoPayApi;
//use App\PaymentModel;
use App\Http\Helper\CommonHelper;
use Exception;
use Log;
//use App\Cms;
//use App\Blog;
use View;
use Session;
use App\Http\Helper\SLIProduct;

//use App\Locale;
//use App\Brand;


/**
 * CustomerController 
 * 
 * PHP version 5.6
 * 
 * @category  Laravel 5.2
 * @package   CustomerController
 * @copyright 2016 
 * @license   http://52.50.219.163/
 * @link      http://52.50.219.163/
 * 
 */
class CustomerController extends Controller {

    const FILE_SUB_DIR = 'user';

    /**
     * @var \MangoPay\MangoPayApi
     */
    private $mangopay;

    /**
     *
     * @var App\PaymentModel 
     */
    private $_paymentModel = NULL;

    /**
     *
     * @var App\Event 
     */
    private $_eventModel = NULL;

    /**
     *
     * @var App\Occasion 
     */
    private $_occasionModel = NULL;

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
     * @var App\Product 
     */
    private $_userModel = NULL;

    /**
     *
     * @var App\Cms 
     */
    private $_cmsModel = NULL;
	 /**
     * Fetch landing page.
     * 
     * @package CustomerController
     * @return resources/views/customer mixed
     */
    public function rendorLandingPage() {
		//echo "<pre/>";
		//$data = Session::all();
		$user = Auth::user();
		//echo($user->fk_users_role);
		
		$id = Auth::id();
		if($id>0 && $user->fk_users_role==1){
			//for administrator
		 return redirect('/admin/dashboard');
			
		}
		//echo $roleid = Auth::fk_users_role();
		//print_r($data);
        try {
           /* $isServiceable      = TRUE;
            $postcode           = '';
            if (session()->has('isServiceable')) {
                $isServiceable  = FALSE;
            }
            if (session()->has('postcode')) {
                $postcode  = session()->get('postcode');
            } */
           // $bannerType     = config('banner.banner_type_landing');
            //$bannerImage    = CommonHelper::getBannerImage($bannerType, 0);
            //$bannerImageMobile    = CommonHelper::getBannerImage($bannerType, 1);
            return view('welcome'); //, compact('isServiceable', 'bannerImage', 'bannerImageMobile', 'postcode'));
        } catch (Exception $ex) {
            Log::error('error in customerController/rendorLandingPage' . $ex->getMessage());
            return view('errors.500');
        }
    }
    /**
     * Instantiate Mangopay API
     * 
     * @package CustomerController
     * @param \MangoPay\MangoPayApi $mangopay
     */
    public function __construct() {
        //$this->mangopay = $mangopay;
        parent::__construct();
    }
	
	
	
    /**
     * Function to Instatntiate Event Model.
     * 
     * @package CustomerController
     * @return object App\Event Model
     */
    private function getEventModel() {
        if ($this->_eventModel == NULL) {
            $this->_eventModel = new Event();
        }
        return $this->_eventModel;
    }

    /**
     * Function to Instatntiate Occasion Model.
     * 
     * @package CustomerController
     * @return object App\Occasion Model
     */
    private function getOccasionModel() {
        if ($this->_occasionModel == NULL) {
            $this->_occasionModel = new Occasion();
        }
        return $this->_occasionModel;
    }

    /**
     * Function to Instatntiate Category Model.
     * 
     * @package CustomerController
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
     * @package CustomerController
     * @return object App\Product Model
     */
    private function getProductModel() {
        if ($this->_productModel == NULL) {
            $this->_productModel = new Product();
        }
        return $this->_productModel;
    }

    /**
     * Function to Instatntiate Payment Model.
     * 
     * @package CustomerController
     * @return object App\PaymentModel
     */
    private function getPaymentModel() {
        if ($this->_paymentModel == NULL) {
            $this->_paymentModel = new PaymentModel($this->mangopay);
        }
        return $this->_paymentModel;
    }
    
    /**
     * Function to Instatntiate User Model.
     * 
     * @package CustomerController
     * @return object App\User Model
     */
    private function _getUserModel() {
        if ($this->_userModel == NULL) {
            $this->_userModel = new User();
        }
        return $this->_userModel;
    }

    /**
     * Function to Instatntiate CMS Model.
     * 
     * @return object App\Cms Model
     */
    private function _getCmsModel() {
        if ($this->_cmsModel == NULL) {
            $this->_cmsModel = new Cms();
        }
        return $this->_cmsModel;
    }

    /**
     * Show the application dashboard.
     *
     * @package CustomerController
     * @return resources/views/customer mixed
     */
    public function index() { 
        try {
            $catTree            = $this->getCategoryModel()->getCategoryTree();
            $primaryEvents      = $this->getEventModel()->getEventTree();
            $primaryOccasion    = $this->getOccasionModel()->getOccassionTree();
            $shopByData         = $this->getShopData();
            $bannerType         = config('banner.banner_type_home');
            $bannerImage        = CommonHelper::getBannerImage($bannerType);
            //return view('customer.home', compact('catTree', 'primaryEvents', 'primaryOccasion', 'shopByData'));
            return view('customer.home-new', compact('catTree', 'primaryEvents', 'primaryOccasion', 'shopByData', 'bannerImage'));
        } catch (Exception $ex) {
            Log::error('error in customerController/index' . $ex->getMessage());
            return view('errors.500');
        }
    }

    /**
     * Method to Get Product to be listed on Store Product page
     * 
     * @package CustomerController
     * @return resources/views/customer mixed
     */
    public function getProducts($catName = NULL, $selectedId = NULL, $subcatName = NULL, $selectedSubCatId = NULL, $subsubcatName = NULL, $subSubCatId = NULL) {
        try {
            $aCatTree           = $this->getCategoryModel()->getCategoryTree();
            return view('customer.products-category', compact('aCatTree'
                            , 'selectedId', 'selectedSubCatId', 'subSubCatId'));
        } catch (Exception $ex) {
            Log::error('error in customerController/getProducts' . $ex->getMessage());
            return view('errors.500');
        }
    }

    /**
     * Method to Get Product Sub Categories.
     * 
     * @param integer $pCatId
     * @package CustomerController
     * @return array
     */
    public function getSubcatTree($pCatId) {
        try {
            return $this->_getSubSubcatTree($pCatId);
        } catch (Exception $ex) {
            Log::error('error in customerController/getSubcatTree' . $ex->getMessage().$ex->getLine());
            return view('errors.500');
        }
    }

    /**
     * Method to get all sub categories.
     * 
     * @param integer $subCatId
     * @package CustomerController
     * @return array
     */
    public function getSubSubcatTree($subCatId) {
        try {
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
        $nearCheck          = $this->checkForNearProducts();
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
        $view = view('partials.product_category', 
            [
                'aSubCats' => $aSubCats,
                'subcatDetails' => $getProducts,
                'selsubCatId' => $subCatId,
                'subCatName' => $subCatName,
            ]
        );
        $view               = $view->render();
        $response['html_content'] = (string) $view;
        return $response;
    }

    /**
     * To render Creations Page
     * 
     * @package CustomerController
     * @return resources/views/customer mixed
     */
    public function renderCreationsPage() {
        try {
            $primaryEvents      = $this->getEventModel()->getEventTree();
            $firstEvent         = array_first($primaryEvents);
            $occasionName       = $firstEvent['name'];
            $subOccasions       = $firstEvent['subEvents'];
            $bannerType         = config('banner.banner_type_theme');
            $bannerImage        = CommonHelper::getBannerImage($bannerType);
            return view('customer.themes-new', compact('primaryEvents', 'occasionName', 'subOccasions', 'bannerImage'));
        } catch (Exception $ex) {
            Log::error(__METHOD__ . $ex->getMessage());
            return view('errors.500');
        }
    }

    /**
     * To render Occassions Page
     * 
     * @package CustomerController
     * @return resources/views/customer mixed
     */
    public function renderOccasionssPage() {
        try {
            $primaryOccasion    = $this->getOccasionModel()->getOccassionTree();
            $firstOccasion      = array_first($primaryOccasion);
            $occasionName       = $firstOccasion['name'];
            $subOccasions       = $firstOccasion['subOccasions'];
            $bannerType         = config('banner.banner_type_occasion');
            $bannerImage        = CommonHelper::getBannerImage($bannerType);
            return view('customer.occasion-new', compact('primaryOccasion', 'occasionName', 'subOccasions', 'bannerImage'));
        } catch (Exception $ex) {
            Log::error(__METHOD__ . $ex->getMessage());
            return view('errors.500');
        }
    }

    /**
     * To get bundle detail page
     * 
     * @package CustomerController
     * @param integer $productId
     * @return resources/views/customer mixed
     */
    public function bundleDetail($productId) {
        try {
            if (isset($productId) && $productId != 0) {
                $nearCheck          = $this->checkForNearProducts();
                $bundleDetails      = $this->getProductModel()->getBundleDetail($productId, $nearCheck);
                if (!empty($bundleDetails)) {
                    $relatedOccasion    = $this->getProductModel()->getRelatedOccasionProduct($productId, TRUE);
                    $relatedProducts    = $this->getProductModel()->getRelatedProducts($productId, TRUE, $nearCheck);
                    $total = 0.00;
                    foreach ($bundleDetails as $key => $value) {
                        $total += $value->priceTot;
                    }
                    return view('customer.create-event-description', compact('bundleDetails', 'total', 'relatedOccasion', 'relatedProducts'));
                }
            }
            return view('errors.404');
        } catch (Exception $ex) {
            Log::error('error in customerController/bundleDetail' . $ex->getMessage());
            return view('errors.500');
        }
    }

    /**
     * Function to edit customer profile
     * 
     * @package CustomerController
     * @return resources/views/customer mixed
     */
    public function editProfile() {
        try {
            $userId         = isset(Auth::user()['id']) ? Auth::user()['id'] : FALSE;
            $user           = User::Find($userId);
            $fileSubDir     = self::FILE_SUB_DIR;
            return view('customer.editProfile', compact('user', 'fileSubDir'));
        } catch (Exception $ex) {
            Log::error('error in customerController/editProfile' . $ex->getMessage());
            return view('errors.500');
        }
    }

    /**
     * Funtion to save profile details in database.
     * 
     * @package CustomerController
     * @param Request $request
     * @return resources/views/customer mixed
     */
    public function saveProfile(Request $request) {
        $response = ['message' => 'Some error occured', 'status' => FALSE, 'data' => ''];
        try {
            $userId         = isset(Auth::user()['id']) ? Auth::user()['id'] : FALSE;
            $user           = User::findOrFail($userId);
            $validateFile   = $this->validateFile($request);
            if ($validateFile['status']) {
                $request = $this->saveFiles($request, self::FILE_SUB_DIR);
                $rules = $this->_getUserModel()->getCustomerProfileValidationRules();
                $validator = Validator::make($request->all(), $rules);
                $phoneNumber = isset($request['phone']) ? $request['phone'] : "";
                $validatePhoneResponse = CommonHelper::validatePhoneNumber($phoneNumber);
                if (!$validatePhoneResponse['status']) {
                    $validator->after(function($validator) {
                        $validator->errors()->add('phone', trans('messages.invalid_phone_number'));
                    });
                }
                if ($validator->fails()) {
                    if ($request->ajax()) {
                        $response['message'] = 'Validation Failed';
                        $response['data'] = $validator->messages();
                        return response()->json($response);
                    } else {
                        return redirect()
                                        ->back()
                                        ->withInput($request->all())
                                        ->withErrors($validator);
                    }
                }
                $this->_getUserModel()->updateCustomerProfile($request->all(), $userId);
                if ($request->ajax()) {
                    $response['message'] = 'Profile saved successfully';
                    $response['status'] = TRUE;
                    $phoneNumber        = $this->_getUserModel()->formatPhoneNumberBeforSaving($request['phone']);
                    $response['data']   = array('phone' => $phoneNumber);
                    return response()->json($response);
                } else {
                    return redirect()->route('customer.editProfile');
                }
            } else {
                if ($request->ajax()) {
                    $response['message'] = 'File upload Validation Failed';
                    $response['data'] = $validateFile['error']->messages();
                    return response()->json($response);
                } else {
                    return redirect()
                                    ->back()
                                    ->withInput($request->all())
                                    ->withErrors($validateFile['error']);
                }
            }
        } catch (Exception $ex) {
            Log::error(__METHOD__ . $ex->getMessage());
            return view('errors.500');
        }
    }

    /**
     * Function to upload store profile image & update it in database.
     * 
     * @package CustomerController
     * @param Request $request
     * @return resources/views/customer mixed
     */
    public function uploadImage(Request $request) {
        try {
            $rules = array(
                'image'     => 'required|mimes:jpeg,jpg|max:10000'
            );
            $validateFile   = $this->validateFile($request);
            if ($validateFile['status']) {
                $validator  = Validator::make($request->all(), $rules);
                if ($validator->fails()) {
                    return redirect()->back()->withInput($request->all())->withErrors($validator);
                }
                $userId         = Auth::user()['id'];
                $user           = User::findOrFail($userId);
                $request        = $this->saveFiles($request, self::FILE_SUB_DIR, '95', '75');
                $user->update(['image' => $request['image']]);
                return redirect()->back();
            } else {
                return redirect()->back()->withInput($request->all())->withErrors($validateFile['error']);
            }
        } catch (Exception $ex) {
            Log::error('error in customerController/uploadImage' . $ex->getMessage());
            return view('errors.500');
        }
    }

    /**
     * Edit address page
     * 
     * @package CustomerController
     * @return resources/views/customer mixed
     */
    public function editAddress(CartController $cartControllerInstance) {
        try {
            $url = explode("/", \Illuminate\Support\Facades\URL::previous());
            if (end($url) == 'dashboard') {
                session()->put("urlAddress", 1);
            }
            $userId         = isset(Auth::user()['id']) ? Auth::user()['id'] : FALSE;
            $userData       = $this->_getUserModel()->getUserAddress($userId);
            $mapAddress     = '';
            if (!empty($userData['userAddress'])) {
                $aAddress   = $userData['userAddress'];
                $mapAddress = $aAddress['address'] . "," . $aAddress['city'] . "," . $aAddress['state'] . "," . $aAddress['pin'];
            }
            $cartEmptyWarning = false;
            $userDeliveryPostcodeDetails    = $cartControllerInstance->getDeliveryPostcodeDetails();
            $cartPostcode                   = $userDeliveryPostcodeDetails['postcode'];
            if (!empty($cartPostcode)) {
                $cartQuantity = $cartControllerInstance->getTotalQuantity();
                if (!empty($cartQuantity)) {
                    $cartEmptyWarning = true;
                }
            }
            return view('customer.editAddress', compact('userData', 'mapAddress', 'cartEmptyWarning', 'cartPostcode'));
        } catch (Exception $ex) {
            Log::error('error in customerController/editAddress' . $ex->getMessage());
            return view('errors.500');
        }
    }
    
    /**
     * Method will update default card status.
     * 
     * @param Request $request
     * @return array
     */
    public function updateCardDefault(Request $request) {
        $response = ['message' => 'Some error occured', 'status' => FALSE, 'data' => ''];

        try {
            if ($request->isMethod('post')) {
                $userId = isset(Auth::user()['id']) ? Auth::user()['id'] : FALSE;
                $this->_getUserModel()->updateCardDefault($userId, $request->get('cardId'));
                $response['message'] = 'Updated Successfully';
                $response['status']  = TRUE;
                return response()->json($response);
            }
        } catch (Exception $ex) {
            Log::error('error in customerController/updateCardDefault' . $ex->getMessage());
            return view('errors.500');
        }
    }

    /**
     * Save customer address in database.
     * 
     * @package CustomerController
     * @param Request $request
     * @return resources/views/customer mixed
     */
    public function saveAddress(Request $request, CartController $cartControllerInstance) {
        $response = ['message' => 'Some error occured', 'status' => FALSE, 'data' => ''];
        try {
            if ($request->isMethod('post')) {
                //$this->validate($request, $this->_getUserModel()->getValidationForAddress($request['pin']), $this->_getUserModel()->validationMessages());
                $validator = Validator::make($request->all(), $this->_getUserModel()->getValidationForAddress($request['pin']), $this->_getUserModel()->validationMessages());
                if ($validator->fails()) {
                    if ($request->ajax()) {
                        $response['message'] = 'Validation Failed';
                        $response['data'] = $validator->messages();
                        return response()->json($response);
                    } else {
                        return redirect()->back()->withInput()->withErrors($validator);
                    }
                }
                $postcodeModel = new ValidPostcode();
                $postCodeDetails = $postcodeModel->getPostcodeDetail($request['pin']);
                if (!empty($postCodeDetails)) {
                    $request['pin'] = $postCodeDetails['postcode'];
                } else {
                    $validator->errors()->add('pin', trans('messages.postcode_error_not_serviceable'));
                    if ($request->ajax()) {
                        $response['message'] = 'Validation Failed';
                        $response['data'] = $validator->messages();
                        return response()->json($response);
                    } else {
                        return redirect()->back()->withInput()->withErrors($validator);
                    }
                }
                $this->_getUserModel()->updateCustomerAddress($request['id'], $request->all());
                $url = 'customer.checkout';
                if (session()->has('urlAddress')) {
                    session()->forget('urlAddress');
                    $url = 'customer.dashboard';
                }
                $newPostcode = $request['pin'];
                $cartControllerInstance->comparePostcodeAndResetCart($newPostcode);
                if ($request->ajax()) {
                    $response['message'] = 'Profile saved successfully';
                    $response['status'] = TRUE;
                    return response()->json($response);
                } else {
                    return redirect()->route($url);
                }
            }
        } catch (Exception $ex) {
            Log::error('error in customerController/saveAddress' . $ex->getMessage());
            return view('errors.500');
        }
    }

    /**
     * Get product detail on basis of productId
     * 
     * @package CustomerController
     * @param integer $productId
     * @return resources/views/customer mixed
     */
    public function getProductDetail($productId) {
        try {
            $nearCheck          = $this->checkForNearProducts();
            $product            = $this->getProductModel()->getProductDetails($productId, false, $nearCheck);
            $relatedOccasion    = $this->getProductModel()->getRelatedOccasionProduct($productId);
            $relatedProducts    = $this->getProductModel()->getRelatedProducts($productId, false, $nearCheck);
            if ($product && isset($product->price) && !empty($product->price)) { // @todo change detail query in case product doesn't exists in postcode.
                return view('customer.products-summary', compact('product', 'relatedOccasion', 'relatedProducts'));
            } else {
                return redirect()->route('customer.products');
            }
        } catch (Exception $ex) {
            Log::error(__METHOD__ . $ex->getMessage());
            return view('errors.500');
        }
    }

    /**
     * Get bundle detail on basis of bundle id
     * 
     * @package CustomerController
     * @param integer $bundleId
     * @return resources/views/customer mixed
     */
    public function getBundleDetail($bundleId) {
        try {
            $customerStatus = $this->checkCustomerCartStatus();
            if (!$customerStatus['validated']) {
                return $customerStatus;
            }
            $nearCheck          = $this->checkForNearProducts();
            $bundleDetails      = $this->getProductModel()->getBundleDetail($bundleId, $nearCheck);
            $total = 0;
            foreach ($bundleDetails as $key => $value) {
                $total += $value->priceTot;
            }
            return array(
                'bundleDetails'     => $bundleDetails,
                'total'             => $total,
                'validated'         => true
            );
        } catch (Exception $ex) {
            Log::error('error in customerController/getBundleDetail' . $ex->getMessage());
            return view('errors.500');
        }
    }

    /**
     * Method for track Order Page
     * 
     * @package CustomerController
     * @param string $orderNumber
     * @return resources/views/customer mixed
     */
    public function trackorder($orderNumber) {
        try {
            $cardDetails        = [];
            $orderModel         = new SalesOrder();
            $orderData          = $orderModel->getOrderDetails($orderNumber);
            $cardId             = $orderModel->getOrderCardId($orderData['orderId']);
            if ($cardId) {
                $cardDetails        = $this->getPaymentModel()->getCardDetails($cardId);
                $cardDetails        = $cardDetails->CardProvider . ' ending with ' . substr($cardDetails->Alias, -4);
            }
            if (!empty($orderData)) {
                return view('customer.order-status', compact('orderData', 'cardDetails'));
            } else {
                return view('errors.404');
            }
        } catch (Exception $ex) {
            Log::error('error in customerController/trackorder' . $ex->getMessage());
            return view('errors.500');
        }
    }
      
    /**
     * Return customer dashboard.
     * 
     * @package CustomerController
     * @return resources/views/customer mixed
     */
    public function getDashboard() {
        try {
            $userId         = isset(Auth::user()['id']) ? Auth::user()['id'] : FALSE;
            $userData       = User::where('id', '=', $userId)->first();
            $fileSubDir     = self::FILE_SUB_DIR;
            $history        = User::getUserOrders($userId, 0);
            $subscribe      = User::getUserSubscription($userId);
            $cmsUserType    = config('cms.user_type');
            $userTypeToExclude = $cmsUserType['Store'];
            $userLegaldata  = $this->_getCmsModel()->getUserCmsData($userTypeToExclude);
            $user           = User::Find($userId);
            
            $hasCard        = FALSE;
            $card           = [];
            if (!empty($userId)) {
                $paymentDetails     = $this->getPaymentModel()->getUserCardDetails($userId);
                $hasCard            = $paymentDetails['hasCard'];
                if ($hasCard) {
                    $aCard          = json_decode($paymentDetails['cardDetails'], TRUE);
                    foreach ($aCard as $key => $value) {
                        $card[$key]['cardDetails'] = $value['CardProvider'] . " ending with " . substr($value['Alias'], -4);
                        $card[$key]['id'] = $value['Id'];
                        foreach ($paymentDetails['cardData']['cardData'] as $cardCount => $mangoCard) {
                            if($mangoCard['mango_users_card_id'] == $value['Id']){
                                $card[$key]['default'] = $mangoCard['is_default'];
                                break;
                            }
                        }
                    }
                }
            } else {
                session()->put("checkout_login", 1);
            }
            return view('customer.customer-profile', compact('userData', 
                    'fileSubDir', 'history', 'subscribe', 'userLegaldata', 
                    'user', 'hasCard', 'card'));
        } catch (Exception $ex) {
            Log::error('error in CustomerController/getDashboard' . $ex->getMessage());
            return view('errors.500');
        }
    }

    /**
     * Return Customer Order History
     * 
     * @package CustomerController
     * @return resources/views/customer mixed
     */
    public function getHistoryData() {
        try {
            $history        = User::getUserOrders(Auth::user()['id'], 1);
            return view('customer.order-history-all', compact('history'));
        } catch (Exception $ex) {
            Log::error('error in customerController/getHistoryData' . $ex->getMessage());
            return view('errors.500');
        }
    }

    /**
     * Return Customer faq's
     * 
     * @package CustomerController
     * @return resources/views/customer
     */
    public function getFaq() {
        try {
            return view('customer.faq');
        } catch (Exception $ex) {
            Log::error('error in customerController/getFaq' . $ex->getMessage());
            return view('errors.500');
        }
    }

    /**
     * Return payment Details of customer.
     * 
     * @package CustomerController
     * @return resources/views/customer mixed
     */
    public function getPaymentDetails() {
        try {
            $hasCard        = FALSE;
            $card           = 'card';
            $userId         = isset(Auth::user()->id) ? Auth::user()->id : NULL;
            if (!empty($userId)) {
                $paymentDetails     = $this->getPaymentModel()->getUserCardDetails($userId);
                $hasCard            = $paymentDetails['hasCard'];
                if ($hasCard) {
                    $aCard          = json_decode($paymentDetails['cardDetails'], TRUE);
                    $card           = $aCard['CardProvider'] . " ending with " . substr($aCard['Alias'], -4);
                    $cartContent    = $card;
                }
            } else {
                session()->put("checkout_login", 1);
            }
            return view('customer.payment-details', compact('hasCard', 'card'));
        } catch (Exception $ex) {
            Log::error('error in customerController/getPaymentDetails' . $ex->getMessage());
            return view('errors.500');
        }
    }

   
	
	
	

    /**
     * Mood Search.
     * 
     * @package CustomerController
     * @param integer $moodID
     * @param string $moodName
     * @return resources/views/customer mixed
     */
    public function moodSearch($moodID = '0', $moodName = '') {
        try {
            if ($moodID != 0 && $moodName != '') {
                $nearCheck          = $this->checkForNearProducts();
                $prodMapping        = $this->getProductModel()->getProductEvents($moodID, $fetchParentDetail = TRUE, $nearCheck);
                $productsEvents     = $prodMapping['products'];
                $parentDetails      = $prodMapping['parentDetails'];
                $bundleMapping      = $this->getProductModel()->getProductBundles($moodID, $nearCheck);
                $products           = $productsEvents;
                return view('customer.create-event-listing', compact('products', 'parentDetails', 'bundleMapping'));
            }
            return view('errors.404');
        } catch (Exception $ex) {
            Log::error(__METHOD__ . $ex->getMessage());
            return view('errors.500');
        }
    }

    /**
     * Occasion Search
     * 
     * @package CustomerController
     * @param integer $occasionID
     * @param string $occasionName
     * @return resources/views/customer mixed
     */
    public function occasionSearch($occasionID = '0', $occasionName = '') {
        try {
            if ($occasionID != 0 && $occasionName != '') {
                $nearCheck          = $this->checkForNearProducts();
                $prodMapping        = $this->getProductModel()->getProductOccasions($occasionID, $fetchParentDetail = TRUE, $nearCheck);
                $productsOccasions  = $prodMapping['products'];
                $parentDetails      = $prodMapping['parentDetails'];
                $bundleMapping      = $this->getProductModel()->getProductOccasionBundles($occasionID, $nearCheck);
                $products           = $productsOccasions;
                return view('customer.occasion-product-listing', compact('products', 'parentDetails', 'bundleMapping'));
            }
            return view('errors.404');
        } catch (Exception $ex) {
            Log::error(__METHOD__ . $ex->getMessage());
            return view('errors.500');
        }
    }

    /**
     * Method for Customer search
     * 
     * @package CustomerController
     * @param Request $request
     * @return resources/views/customer mixed
     */
    public function search(Request $request) {
        try {
            $param              = trim($request['param']);
            $nextIndex          = 0;
            $aCatTree           = $this->getCategoryModel()->getCategoryTree();
            $nearCheck          = $this->checkForNearProducts();
            $sliProducts        = SLIProduct::getSLIProducts($request, $param, SLIProduct::STANDARD_QUERY);
            $productPagination  = isset($sliProducts['data']['paginationData']) ? $sliProducts['data']['paginationData'] : NULL;
            if(NULL != $productPagination && isset($productPagination['next'])){
                $nextIndex = $productPagination['next']['start'];
            }
            $products           = $sliProducts['data']['results'];
            $productsId         = [];
            foreach ($products as $key => $value) {
                $productsId[] = $value['productID'];
            }
            $matchedProducts    = $this->getProductModel()->searchProducts($productsId, $nearCheck);
            $searchTerm         = $param;
            $matchedDataBlog    = SLIProduct::searchSLIBlogAPI($request, $param, SLIProduct::SUGGESTION_QUERY);
            $showSuggestions    = false;
            return view('customer.search-results', compact('matchedProducts', 'aCatTree', 'searchTerm', 'nextIndex', 'matchedDataBlog', 'showSuggestions'));
        } catch (Exception $ex) {
            Log::error(__METHOD__. $ex->getMessage());
            return view('errors.500');
        }
    }

    /**
     * Return autosuggest matched products.
     * 
     * @package CustomerController
     * @param Request $request
     * @return mixed
     */
    public function getMatchedProducts(Request $request) {
        $response = array(
            'status' => false,
            'html_content' => '',
        );
        $matchedProducts = $suggestionsData = $matchedDataBlog= [];
        $searchParam        = $request->input('term');
        $nextIndex          = 1;
        try {
            $nearCheck          = $this->checkForNearProducts();
            $matchedData        = SLIProduct::getSLIProducts($request, $searchParam, SLIProduct::SUGGESTION_QUERY);
            if ($matchedData['status']) {
                $param = array();
                $suggestionsData = $matchedData['data'];
                foreach($matchedData['data']['results'] as $products) {
                    $param[] = $products['productID'];
                    if (count($param) > 5) {
                        break;
                    }
                }
                $matchedDataBlog    = SLIProduct::searchSLIBlogAPI($request, $searchParam, SLIProduct::SUGGESTION_QUERY);
                $matchedProducts    = $this->getProductModel()->searchProducts($param, $nearCheck);
                $response['status'] = true;
            }
        } catch (Exception $ex) {
            Log::error(__METHOD__ . $ex->getMessage());
        }
        $view = view('customer.partials.header.header-search-results', [
                'matchedData'   => $suggestionsData,
                'searchTerm'    => $searchParam,
                'matchedProducts' => $matchedProducts,
                'nextIndex'         => $nextIndex,
                'matchedDataBlog' => $matchedDataBlog,
                'showSuggestions' => true,
        ]);
        $view = $view->render();
        $response['html_content'] = (string) $view;
        return $response;
    }

    /**
     * Method to set delivery Postcode of user in session
     * Makes External API hit and fetch Lat Lng of postcode
     *
     * @param Object $request Input Request Object
     * @return array $response
     *
     */
    public function setDeliveryPostCode(Request $request, CartController $cartControllerInstance) {
        $response = array(
            'status' => false,
            'message' => '',
        );
        $inputRequest = $request->all();
        $postCode     = isset($inputRequest['pin']) ? $inputRequest['pin'] : "";
        $postcodeModel      = new ValidPostcode();
        $postCodeDetails    = $postcodeModel->getPostcodeDetail($postCode);
        if (!empty($postCodeDetails)) {
            $postCode   = $postCodeDetails['postcode'];
            $lat        = $postCodeDetails['lat'];
            $lng        = $postCodeDetails['lng'];
            $nearByStores = CommonHelper::getNearByStoresForOrder($postCode, $lat, $lng);
            if (!empty($nearByStores)) {
                $productModel = new Product();
                $products = $productModel->getStoreProductCount($nearByStores);
                if (!empty($products)) {
                    ValidPostcode::logPostcode($postCode, $isSuccess = 1);
                    $response['status'] = true;
                    $cartControllerInstance->comparePostcodeAndResetCart($postCode);
                    $cartDeliveryPostcodeSessionKey = CommonHelper::getUserCartDeliveryPostcodeSessionKey();
                    $cartDeliveryLatSessionKey      = \Config::get('appConstants.user_landing_lat_session_key');
                    $cartDeliveryLngSessionKey      = \Config::get('appConstants.user_landing_lng_session_key');
                    session()->put($cartDeliveryPostcodeSessionKey, $postCode);
                    session()->put($cartDeliveryLatSessionKey, $lat);
                    session()->put($cartDeliveryLngSessionKey, $lng);
                }
            }
            if (!$response['status']) {
                ValidPostcode::logPostcode($postCode, $isSuccess = 0);
            }
            $response['message'] = trans('messages.postcode_error_no_products');
            return $response;
        } else {
            ValidPostcode::logPostcode($postCode, $isSuccess = 0);
            $response['message'] = trans('messages.postcode_error_not_serviceable_new');
        }
        return $response;
    }

    /**
     * Method to check whether Delivery Postcode is set by user or not.
     * If not it generates HTML & send back in response.
     *
     * @return array $response
     *
     */
    public function checkCustomerCartStatus() {
        $response           = array(
            'validated'                 => false,
            'html_content'              => '',
            'is_close'              => false,
        );
        //
        if ((CommonHelper::checkForSiteTimings())) {
            $response['is_close'] = true;
            $storeModel = new \App\StoreModel();
            $storeTimings = $storeModel->getOpeningTime();
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
            $view = view('partials.store_timing_content', ['openingTime' => $timings]);
            $view = $view->render();
            $response['html_content'] = (string) $view;
        } else {
            //
            $userDeliveryPostcodeDetails    = CommonHelper::getUserCartDeliveryPostcodeDetails();
            $cartPostcode                   = $userDeliveryPostcodeDetails['postcode'];
            if (!empty($cartPostcode)) {
                $response['validated'] = true;
            } else {
                $userId = isset(Auth::user()->id) ? Auth::user()->id : NULL;
                $isGuest = true;
                $deliveryPostcode = '';
                if (!empty($userId)) {
                    $isGuest = false;
                    $userModel = $this->_getUserModel();
                    $userAddress = $userModel->getUserSavedAddress($userId);
                    $hasAddress = $userAddress['hasAddress'];
                    if ($hasAddress) {
                        $aAddress = json_decode($userAddress['address'], true);
                        $deliveryPostcode = $aAddress['pin'];
                    }
                }
                $view = view('customer.partials.delivery-postcode', ['isGuest' => $isGuest, 'deliveryPostcode' => $deliveryPostcode]);
                $view = $view->render();
                $response['html_content'] = (string) $view;
            }
        }


        return $response;
    }

    /**
     * Method to check whether near By check will be applicable while showing product listing.
     * Checks two different session set on landing page / Cart Page
     *
     * @return boolean
     *
     */
    public function checkForNearProducts() {
        return CommonHelper::checkForNearProducts();
    }

    /**
     * Method to get matched Product By Category
     *
     * @param int CategoryId
     * @param string $searchTerm Input search Parameter
     *
     * @return array  $response
     */
    public function searchByCategory($catId, $searchTerm) {
        $response = array(
            'status' => false,
            'message' => '',
            'html_content' => '',
        );
        try {
            $nearCheck                  = $this->checkForNearProducts();
            $matchedProducts            = $this->getProductModel()->searchProductsByCategory($catId, $searchTerm, $nearCheck);
            $view                       = view('customer.partials.search', ['matchedProducts' => $matchedProducts]);
            $view                       = $view->render();
            $response['status']         = true;
        } catch (Exception $ex) {
            $view                       =  view('customer.partials.search', ['matchedProducts' => []]);
            $view                       =  $view->render();
        }
        $response['html_content']   =  (string) $view;
        return $response;
    }

    /**
     * Method to render Cookies Page for Web & Apps
     *
     * @param string $isApi
     * @return view Object
     */
    public function getcookiesPolicy($isApi = NULL) {
        $templatePrefix = 'customer.legal';
        if (!empty($isApi)
            && ($isApi == 'api')) {
            $templatePrefix = 'api';
        } else {
            if (Auth::user()
                    && Auth::user()->fk_users_role == \Config::get('appConstants.vendor_role_id')) {
                $templatePrefix = 'store.legal';
            }
        }
        $type   = config('cms.user_type.General');
        $title  = config('cms.page_cookies');
        $cmsData = $this->_getCmsModel()->getCmsPageContent($type, $title);
        return view($templatePrefix .'.cookies', compact('cmsData'));
    }

    /**
     * Method to render Privacy Page for Web & Apps
     *
     * @param string $isApi
     * @return view Object
     */
    public function getPrivacyPolicy($isApi = NULL) {
        $templatePrefix = 'customer.legal';
        if (!empty($isApi)
            && ($isApi == 'api')) {
            $templatePrefix = 'api';
        } else {
            if (Auth::user()
                    && Auth::user()->fk_users_role == \Config::get('appConstants.vendor_role_id')) {
                $templatePrefix = 'store.legal';
            }
        }
        $type   = config('cms.user_type.General');
        $title  = config('cms.page_privacy');
        $cmsData = $this->_getCmsModel()->getCmsPageContent($type, $title);
        return view($templatePrefix .'.privacy', compact('cmsData'));
    }

    /**
     * Method to render Legal Page for Web & Apps
     *
     * @param string $isApi
     * @return view Object
     */
    public function getlegalterms($isApi = NULL) {
        $templatePrefix = 'customer.legal';
        if (!empty($isApi)
            && ($isApi == 'api')) {
            $templatePrefix = 'api';
        } else {
            if (Auth::user()
                    && Auth::user()->fk_users_role == \Config::get('appConstants.vendor_role_id')) {
                $templatePrefix = 'store.legal';
            }
        }
        $type   = config('cms.user_type.General');
        $title  = config('cms.page_legal');
        $cmsData = $this->_getCmsModel()->getCmsPageContent($type, $title);
        return view($templatePrefix .'.legal', compact('cmsData'));
    }

    /**
     * Method to Careers Page
     *
     * @return view Object
     */
    public function getCarrersPage() {
        $templatePrefix = 'customer.cms';
        $type   = config('cms.user_type.General');
        $title  = config('cms.page_careers');
        $cmsData = $this->_getCmsModel()->getCmsPageContent($type, $title);
        return view($templatePrefix .'.cms', compact('cmsData'));
    }
    
    /**
     * Method to update subscription details of a customer.
     * 
     * @param Request $request
     * @return array
     */
    public function updateSubscription(Request $request) {
        $response = [
            'status' => 'error',
            'message'=> 'Some Error Occured',
        ];
        try {
            $userId = isset(Auth::user()->id) ? Auth::user()->id : NULL;
            $response = User::updateSubscriptionStatus($userId,$request->get('is_subscribed'));
        } catch (Exception $ex) {
            Log::error(__METHOD__ . $ex->getMessage());
        }
        return $response;
    }

    /**
     * Method to render Legal Page for Web & Apps
     *
     * @param string $isApi
     * @return view Object
     */
    public function getCmsCustomerPageContent($urlPath, $isApi = NULL) {
        $templatePrefix = 'customer.cms';
        if (!empty($isApi)
            && ($isApi == 'api')) {
            $templatePrefix = 'api';
        }
        $type   = config('cms.user_type.User');
        $cmsData = $this->_getCmsModel()->getCmsPageData($type, $urlPath);
        
        return view($templatePrefix .'.cms', compact('cmsData'));
    }

    /**
     * Method to render Legal Page for Web & Apps
     *
     * @param string $isApi
     * @return view Object
     */
    public function getCmsPageContent($urlPath, $isApi = NULL) {
        $templatePrefix = 'customer.cms';
        if (!empty($isApi)
            && ($isApi == 'api')) {
            $templatePrefix = 'api';
        } else {
            if (Auth::user()
                    && Auth::user()->fk_users_role == \Config::get('appConstants.vendor_role_id')) {
                $templatePrefix = 'store.cms';
            }
        }
        $type   = config('cms.user_type.General');
        $cmsData = $this->_getCmsModel()->getCmsPageData($type, $urlPath);
        return view($templatePrefix .'.cms', compact('cmsData'));
    }

    /**
     * 
     */
    public function getShopData() {
        $shopData = array(
            'popular'   => array(),
            'recipe'    => array(),
            'bundle'    => array(),
            'gift'      => array(),
        );
        try {
            $nearCheck          = $this->checkForNearProducts();
            $popularProducts    = $this->getProductModel()->getPopularProducts($isPopular = 1, $isGift = 0, $limit = 10, $nearCheck);
            if (count($popularProducts) < 10) {
                $remainingPopularCount   = 10 - count($popularProducts);
                $popularBundles    = $this->getProductModel()->getPopularBundles($isPopular = 1, $isGift = 0, $limit = $remainingPopularCount, $nearCheck);
                $shopData['popular']['bundles'] = $popularBundles;
            }
            $shopData['popular']['products'] = $popularProducts;
            $giftProducts    = $this->getProductModel()->getPopularProducts($isPopular = 0, $isGift = 1, $limit = 10, $nearCheck);
            if (count($giftProducts) < 10) {
                $remainingGiftCount   = 10 - count($giftProducts);
                $giftBundles    = $this->getProductModel()->getPopularBundles($isPopular = 0, $isGift = 1, $limit = $remainingGiftCount, $nearCheck);
                $shopData['gift']['bundles'] = $giftBundles;
            }
            $shopData['gift']['products'] = $giftProducts;
            $topRecipe    = $this->getProductModel()->getTopBundles($isRecipe = 1, $limit = 10, $nearCheck);
            $topBundle    = $this->getProductModel()->getTopBundles($isRecipe = 0, $limit = 10, $nearCheck);
            $shopData['bundle']     = $topBundle;
            $shopData['recipe']     = $topRecipe;
        } catch (Exception $ex) {

        }
        return $shopData;
        
    }

    /**
     * Method to get Event Content Page
     * 
     * @param string $eventContentUrl
     * @return view Object
     */
    public function getEventContent($eventContentUrl = null) {
        $blogModel  = new Blog();
        $type       = config('blog.type_event');
        if (empty($eventContentUrl)) {
            $currDate =  date('Y-m-d');
            $aCurrDate = explode("-", $currDate);
            $currentMonth   = $aCurrDate[1];
            $currentYear    = $aCurrDate[0];
            $currentDate    = $aCurrDate[2];
            $contentData = $blogModel->getListingData($type, $urlPath = NULL, $currentDate, $currentMonth, $currentYear);
            $allMonth = config('content_month_mapping.website_map');
            $currentMonthLabel = $allMonth[$currentMonth];
            $bannerType         = config('banner.banner_type_content_event');
            $bannerImage        = CommonHelper::getBannerImage($bannerType);
            return view('customer.events', compact('contentData', 'currentMonthLabel', 'bannerImage', 'currentMonth', 'currentYear'));
        } else {
            $contentData = $blogModel->getListingData($type, $eventContentUrl);
            if (empty($contentData)) {
                return view('errors.404');
            }
            $relatedData = $this->getRelatedContent($contentData);
            return view('customer.event-details', compact('contentData', 'relatedData'));
        }
    }

    /**
     * Method to get Related Content on sidebar section
     * 
     * @param array $keywordUrl
     * @return view Object
     */
    public function getKeywordsContent($keywordUrl, Request $request) {
        $blogModel          = new Blog();
        $type               = $request->get('content',config('blog.type_event'));
        $contentData        = $blogModel->getListingDataForKeyword($keywordUrl, $type, false, true);
        $bannerType         = config('banner.banner_type_content_event');
        $bannerImage        = CommonHelper::getBannerImage($bannerType);
        $selectedKeyword    = $keywordUrl;
        return view('customer.keywords', compact('contentData', 'bannerImage', 'selectedKeyword'));
    }

    /**
     * Method to get Archive Content on sidebar section
     * 
     * @param int $monthId
     * @param int $year
     * @return view Object
     */
    public function getArchieveContent($monthId, $year = null) {
        $blogModel          = new Blog();
        $type               = config('blog.type_event');
        $bannerType         = config('banner.banner_type_content_event');
        $bannerImage        = CommonHelper::getBannerImage($bannerType);
        $allMonth           = config('content_month_mapping.website_map');
        $currentMonthLabel  = $allMonth[$monthId];
        if (empty($year)) {
            $year = date('Y');
        }
        $contentData        = $blogModel->getListingData($type, $urlPath = NULL, NULL, $monthId, $year, $isArchive = true);
        return view('customer.archives', compact('contentData', 'bannerImage', 'currentMonthLabel', 'year', 'monthId'));
    }
    
    /**
     * Method to return events details w.r.t months.
     * 
     * @param Request $request
     * @return array
     */
    public function getEventDetailsByMonth(Request $request) {
        $response       = [];
        $blogModel      = new Blog();
        $currDate       =  date('Y-m-d');
        $aCurrDate      = explode("-", $currDate);
        $currentYear    = $aCurrDate[0];
        try {
            $type       = config('blog.type_event');
            $urlPath    = NULL;
            $date       = NULL;
            $month      = $request->get('month', 01);
            $year       = $request->get('year', $currentYear);
            $isArchive  = $request->get('archive', 0);
            $data       = $blogModel->getListingData($type, $urlPath, $date, $month, $year, $isArchive);
        } catch (Exception $ex) {
             Log::error(__METHOD__ . $ex->getMessage());
        }
        $view = View::make('customer.partials.event-listing', ['contentData' => $data]);
        $response['html_content'] = $view->render();
        return $response;
    }

    /**
     * Method to render Place Listing page.
     * 
     * @param string $placeContentUrl
     * @return view object
     */
    public function getPlaceContent($placeContentUrl = null) {
        $blogModel  = new Blog();
        $type       = config('blog.type_place');
        if (empty($placeContentUrl)) {
            $contentData    = $blogModel->getListingData($type);
            $bannerType     = config('banner.banner_type_content_place');
            $bannerImage    = CommonHelper::getBannerImage($bannerType);
            return view('customer.places', compact('contentData', 'currentMonthLabel', 'bannerImage'));
        } else {
            $contentData = $blogModel->getListingData($type, $placeContentUrl);
            if (empty($contentData)) {
                return view('errors.404');
            }
            $relatedData = $this->getRelatedContent($contentData);
            return view('customer.places-details', compact('contentData', 'relatedData'));
        }
    }

    /**
     * Method to render Blog Listing page.
     *
     * @param string $blogContentUrl
     * @return view object
     */
    public function getBlogContent($blogContentUrl = null) {
        $blogModel  = new Blog();
        $type       = config('blog.type_blog');
        if (empty($blogContentUrl)) {
            $contentData    = $blogModel->getListingData($type);
            $bannerType     = config('banner.banner_type_content_blog');
            $bannerImage    = CommonHelper::getBannerImage($bannerType);
            return view('customer.blog', compact('contentData', 'currentMonthLabel', 'bannerImage'));
        } else {
            $contentData = $blogModel->getListingData($type, $blogContentUrl);
            if (empty($contentData)) {
                return view('errors.404');
            }
            $relatedData = $this->getRelatedContent($contentData);
            return view('customer.blog-details', compact('contentData', 'relatedData'));
        }
    }

    /**
     * Method to get Related Keyword Content on sidebar section
     * 
     * @param Request $request
     * @return array
     */
    public function getKeywordRelatedContent(Request $request) {
        $aInputRequest  = $request->all();
        $type           = $aInputRequest['content'];
        $keywordUrl     = $aInputRequest['keyword'];
        $blogModel      = new Blog();
        $contentData    = $blogModel->getListingDataForKeyword($keywordUrl, $type, false, true);
        $viewFilePath = 'customer.partials.event-listing';
        if ($type == config('blog.type_blog')) {
            $viewFilePath = 'customer.partials.content-blog-listing';
        }
        if ($type == config('blog.type_place')) {
            $viewFilePath = 'customer.partials.content-places-listing';
        }
        $view = View::make($viewFilePath, ['contentData' => $contentData]);
        $response['html_content'] = $view->render();
        return $response;
        
    }

    /**
     * Method to get Related Content on sidebar section
     * 
     * @param obj $contentData
     * @return array
     */
    public function getRelatedContent($contentData) {
        $blogModel      = new Blog();
        $id             = $contentData->id;
        $keywords       = $blogModel->getKeywordForToken($id);
        $response       = array(
            'blog' => '',
            'event' => '',
            'place' => '',
            'keyword' => array(),
        );
        $blogData = $eventData = $placeData = '';
        foreach ($keywords as $keyword) {
            if (empty($response['blog'])
                || empty($response['event'])
                || empty($response['place'])
            ) {
                if (empty($response['blog'])) {
                    $type           = config('blog.type_blog'); 
                    $relatedData    = $blogModel->getListingDataForKeyword($keyword->machine_name, $type, $related = true, $selective = true, $contentId = $id);
                    $response['blog'] = $relatedData;
                }
                if (empty($response['event'])) {
                    $type = config('blog.type_event');
                    $relatedData    = $blogModel->getListingDataForKeyword($keyword->machine_name, $type, $related = true, $selective = true, $contentId = $id);
                    $response['event'] = $relatedData;
                } if (empty($response['place'])) {
                    $type = config('blog.type_place');
                    $relatedData    = $blogModel->getListingDataForKeyword($keyword->machine_name, $type, $related = true, $selective = true, $contentId = $id);
                    $response['place'] = $relatedData;
                }
            } else {
                break;
            }
        }
        $response['keyword'] = $blogModel->getKeywordForToken($id, 'blog', $random = false);
        return $response;
    }
    
    /**
     * Method to get Archive Content on sidebar section
     * 
     * @param int $monthId
     * @param int $year
     * @return view Object
     */
    public function getLocaleContent($url) {
        $localeModel    = new Locale();
        $localeData     = $localeModel->getLocaleDetails($url);
        if (!empty($localeData)) {
            $contentData = [];
            $keywordUrl  = "";
            if (!empty($localeData->machine_name)) {
                $keywordUrl = $localeData->machine_name;
                $blogModel          = new Blog();
                $aType = [
                    config('blog.type_place'),
                    config('blog.type_blog'),
                    config('blog.type_event')
                ];
                $contentData        = $blogModel->getListingDataForLocale($keywordUrl, $aType);
            }
            $nextPage           = $contentData->toArray()['next_page_url'];
            $nearCheck          = $this->checkForNearProducts();
            $prodMapping        = $this->getProductModel()->getLocaleProducts($localeData->id, $nearCheck);
            $bundleMapping      = $this->getProductModel()->getLocaleBundles($localeData->id, 1, $nearCheck);
            $recipeMapping      = $this->getProductModel()->getLocaleBundles($localeData->id, 0, $nearCheck);
            $localeProducts     = $prodMapping['products'];
            return view('customer.locale', compact('localeData', 'contentData', 'localeProducts', 'bundleMapping', 'recipeMapping', 'keywordUrl', 'nextPage'));
        } else {
            return view('errors.404');
        }
    }
    
    /**
     * Method to get Related Keyword Content on sidebar section
     * 
     * @param Request $request
     * @return array
     */
    public function getLocaleRelatedContent(Request $request) {
        $aInputRequest  = $request->all();
        $aType          = $aInputRequest['content'];
        $keywordUrl     = $aInputRequest['keyword'];
        $currentPage       = $request->get('nextPage', '');
        $blogModel      = new Blog();
        $contentData    = $blogModel->getListingDataForLocale($keywordUrl, $aType, $currentPage);
        $viewFilePath   = 'customer.partials.locale-content-listing';
        $view = View::make($viewFilePath, ['contentData' => $contentData]);
        $response['html_content'] = $view->render();
        $response['nextPage']     = $contentData->toArray()['next_page_url'];
        return $response;
        
    }

    /**
     * Method to get Brand Template Content
     * 
     * @param string $url
     * @return view Object
     */
    public function getBrandContent($url) {
        $brandModel    = new Brand();
        $brandData     = $brandModel->getBrandDetails($url);
        if (!empty($brandData)) {
            $nearCheck          = $this->checkForNearProducts();
            $prodMapping        = $this->getProductModel()->getBrandProducts($brandData->id, $nearCheck);
            $bundleMapping      = $this->getProductModel()->getBrandBundles($brandData->id, 1, $nearCheck);
            $recipeMapping      = $this->getProductModel()->getBrandBundles($brandData->id, 0, $nearCheck);
            $brandProducts      = $prodMapping['products'];
            return view('customer.brand', compact('brandData', 'brandProducts', 'bundleMapping', 'recipeMapping'));
        } else {
            return view('errors.404');
        }
    }

    /**
     * Ajax for search pagination.
     * 
     * @param Request $request
     * @return View
     * 
     */
    public function searchWithPagination(Request $request) {
        $response = [
            'status' => FALSE,
            'data'   => [],
            'message'=> 'some error occured',
        ];
        try {
            $param              = trim($request['param']);
            $offset             = trim($request['offset']);
            $nextIndex          = '0';
            $nearCheck          = $this->checkForNearProducts();
            $sliProducts        = SLIProduct::getSLIProducts($request, $param, SLIProduct::STANDARD_QUERY, ['offset' => $offset]);
             $productPagination  = isset($sliProducts['data']['paginationData']) ? $sliProducts['data']['paginationData'] : NULL;
            if(NULL != $productPagination && isset($productPagination['next'])){
                $nextIndex = $productPagination['next']['start'];
            }
            $products           = $sliProducts['data']['results'];
            $productsId         = [];
            foreach ($products as $key => $value) {
                $productsId[] = $value['productID'];
            }
            $matchedProducts    = $this->getProductModel()->searchProducts($productsId, $nearCheck);
            $response           = ['status' => TRUE, 'message' => 'detailes fetched successfully'];
            $view = view('customer.partials.search-sli-products', [
                'isAjax'            => '1',
                'matchedProducts'   => $matchedProducts,
                'nextIndex'         => $nextIndex,
                'searchTerm'        => $param,
            ]);
            $view = $view->render();
            $response['html_content'] = (string) $view;
            $response['nextIndex'] = $nextIndex;
        } catch (Exception $ex) {
            Log::error(__METHOD__. $ex->getMessage());
        }
        return $response;

    }
}
