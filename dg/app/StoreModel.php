<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Product;
use DB;
use Log;
use App\SalesOrder;
use App\SessionModel;
use App\Http\Helpers\Email;
use App\User;
use App\StoreDetails;
use Exception;
use App\StoreAddress;
use App\Http\Helper\CommonHelper;
use Illuminate\Support\Facades\Cache;
use GuzzleHttp\Client;
use App\StoreLegalAddress;
use App\StoreRegisteredAddress;
use App\Model\OrderStatus;
use App\SubStoreDetails;
use App\Http\Helper\EmailForce24;

/**
 * Store Model
 */
class StoreModel extends Model {

    private $aWeekDay = array(
        'Tuesday' => 0,
        'Wednesday' => 0,
        'Thursday' => 0,
        'Friday' => 0,
        'Saturday' => 0,
        'Sunday' => 0,
        'Monday' => 0,
    );
    
    /**
     *
     * @var App\User 
     */
    private $_userModel = NULL;
    
    /**
     *
     * @var App\StoreDetails 
     */
    private $_storeDetailsModel = NULL;
    
    /**
     *
     * @var App\SubStoreDetails 
     */
    private $_subStoreDetailsModel = NULL;
    
    
    /**
     *
     * @var App\StoreAddress 
     */
    private $_storeAddressModel = NULL;
    
    /**
     * Function to Instatntiate User Model.
     * 
     * @package StoreModel
     * @return object App\StoreDetails Model
     */
    private function _getSubStoreDetailsModel() {
        if ($this->_subStoreDetailsModel == NULL) {
            $this->_subStoreDetailsModel = new SubStoreDetails();
        }
        return $this->_subStoreDetailsModel;
    }
    
    /**
     * Function to Instatntiate User Model.
     * 
     * @package StoreModel
     * @return object App\StoreDetails Model
     */
    private function _getStoreAddressModel() {
        if ($this->_storeAddressModel == NULL) {
            $this->_storeAddressModel = new StoreAddress();
        }
        return $this->_storeAddressModel;
    }
    
    /**
     * Function to Instatntiate User Model.
     * 
     * @package StoreModel
     * @return object App\StoreDetails Model
     */
    private function _getStoreDetailsModel() {
        if ($this->_storeDetailsModel == NULL) {
            $this->_storeDetailsModel = new StoreDetails();
        }
        return $this->_storeDetailsModel;
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
     * Method to add / remove Products to Store
     * 
     * @package Store Model
     * @param Int $productId
     * @param Boolean $isAdd
     * @param Int $storeId
     * @return string
     */
    public function saveStoreProducts($productId, $isAdd, $storeId) {
        $aResponse = array(
            'status' => FALSE,
            'message' => 'Empty Products'
        );
        try {
            if (!empty($productId)) {
                DB::beginTransaction();
                if ($isAdd == 'true') {
                    $inStock = $this->isProductInStock($productId, $storeId);
                    if ($inStock['status'] == FALSE) {
                        $vendorPrice = DB::table('products')
                                ->select('store_price')
                                ->where('id', $productId)->first();
                        $insertData = array(
                            'fk_product_id' => $productId,
                            'fk_user_id' => $storeId,
                            'vendor_price' => $vendorPrice->store_price,
                        );
                        DB::table('products_store')->insert($insertData);
                    }
                } else {
                    DB::table('products_store')->where([
                        ['fk_user_id', $storeId],
                        ['fk_product_id', $productId],
                    ])->delete();
                }
                DB::commit();
                $aResponse['status'] = TRUE;
                $aResponse['message'] = 'Product mapping Updated';
                Log::info('Product mapping Updated');
            }
        } catch (Exception $ex) {
            Log::error('Error');
            $aResponse['message'] = 'Error : ' . $ex->getMessage();
            DB::rollBack();
        }
        return $aResponse;
    }

    /**
     * Get Store Products.
     * 
     * @package Store Model
     * @param Int $storeId
     * @return array
     */
    public function getStoreProducts($storeId) {
        $storeProducts = DB::table('products_store')
                ->select('fk_product_id as product_id')
                ->addSelect('vendor_price AS vendor_price')
                ->where('fk_user_id', '=', $storeId)
                ->get();
        $aResponse = array();
        foreach ($storeProducts as $result) {
            $aResponse[$result->product_id] = array(
                'pid'=>$result->product_id,
                'price' => $result->vendor_price);
        }
        return $aResponse;
    }

    /**
     * Get all Products of store.
     * 
     * @package Store Model
     * @return Object
     */
    public function getAllProducts() {
        $products = Product::paginate(10);
        return $products;
    }

    /**
     * Get sales_order id by Orderid (which is visible to end user).
     * 
     * @package Store Model
     * @param string $orderNumber
     * @return Int
     */
    public function getIdByOrderId($orderNumber) {
        try {
            $orderId = SalesOrder::select('id_sales_order')
                    ->where('order_id', '=', $orderNumber)
                    ->where('fk_order_status_id', '!=', 4)
                    ->first();
            $orderId = isset($orderId['id_sales_order']) ? $orderId['id_sales_order'] : FALSE;
            Log::info('Order Id found' . $orderId);
            return $orderId;
        } catch (Exception $ex) {
            Log::error('error in getIdByOrderId' . $ex->getMessage());
        }
    }

    /**
     * Get sales_order_item details for a orderId.
     * 
     * @package Store Model
     * @param Int $orderId
     * @return Object mixed
     */
    public function getOrderItemDetails($orderId, $storeId = null) {
        try {
            $orderItem = DB::table('sales_order_item')
                    ->select('sales_order_item.*', DB::raw('COUNT(id_sales_order_item) AS quantity'), 'products.name', 'products.price', 'products.description', 'products.barcode')
                    ->addSelect(DB::raw('sales_order_item.store_price - (sales_order_item.store_price * (sales_order_item.vendor_commission / 100)) AS store_price'))
                    ->join('products', 'products.id', '=', 'sales_order_item.fk_product_id')
                    ->join('sales_order', 'sales_order.id_sales_order', '=', 'sales_order_item.fk_sales_order_id')
                    ->where('fk_sales_order_id', '=', $orderId)
                    ->where('sales_order.fk_order_status_id', '=', config('appConstants.available_order_condition'))
                    ->where('fk_driver_id', '=', \Config::get('appConstants.available_order_condition'))
                    ->where('sales_order_item.fk_order_status_id', '=', \Config::get('appConstants.available_order_condition'))
                    ->where('fk_store_id', '=', $storeId)
                    ->groupBy('fk_product_id')
                    ->get();
            return $orderItem;
        } catch (Exception $ex) {
            Log::error(__METHOD__ . $ex->getMessage());
        }
    }

    /**
     * Get total price of order based on vendor selected item.
     * 
     * @package Store Model
     * @param Array $orderItem
     * @return Int
     */
    public function getTotalPrice($orderItem) {
        try {
            unset($orderItem['orderId']);
            unset($orderItem['total']);
            $totalPrice = 0;
            foreach ($orderItem as $key => $value) {
                if (isset($value['newQty'])) {
                    $totalPrice += ($value['store_price'] * $value['newQty']);
                } else {
                    $totalPrice += $value['store_price'];
                }
            }
            Log::info('getTotalPrice found');
            return $totalPrice;
        } catch (Exception $ex) {
            Log::error('error in getTotalPrice' . $ex->getMessage());
        }
    }

    /**
     * Set session for vendor.
     * 
     * @package Store Model
     * @param String $orderNumber
     * @return Array
     */
    public function updateVendorSession($orderNumber, $storeId = null) {
        try {
            $itemArray = ['id_sales_order_item', 'store_price', 'name', 'description', 'quantity', 'fk_product_id', 'fk_sales_order_id', 'barcode'];
            SessionModel::forgetSessionVariable('vendor.orderData');
            $orderId = self::getIdByOrderId($orderNumber);
            $orderItem = self::getOrderItemDetails($orderId, $storeId);
            if (isset($orderItem)) {
                SessionModel::putSessionVariable('vendor.orderData.orderId', $orderNumber);
                SessionModel::putSessionVariable('vendor.orderData.total', 0);
                foreach ($orderItem as $key => $value) {
                    foreach ($value as $key1 => $value2) {
                        if (in_array($key1, $itemArray))
                            SessionModel::putSessionVariable('vendor.orderData.' . $key . '.' . $key1, $value2);
                    }
                }
                Log::info('StoreModel::session updated');
                return $orderItem;
            }
        } catch (Exception $ex) {
            Log::error(__METHOD__ . $ex->getMessage());
        }
    }

    /**
     * 
     * @package Store Model
     * @param array $aCatId
     * @param Boolean $hasSubCat
     * @return Object mixed
     */
    public function getAssociatedProducts(array $aCatId, $hasSubCat) {
        if ($hasSubCat) {
            $prod = DB::table('xref_product_categories')
                    ->select('products.*', 'categories.name as catName')
                    ->join('products', 'products.id', '=', 'xref_product_categories.fk_product_id')
                    ->join('categories', 'categories.id', '=', 'xref_product_categories.fk_category_id')
                    ->whereIn('fk_category_id', $aCatId)
                    ->get();
            $returnResponse = array();
            foreach ($prod as $prodSingle) {
                $catName = $prodSingle->catName;
                if (!isset($returnResponse[$catName])) {
                    $returnResponse[$catName] = array();
                }
                array_push($returnResponse[$catName], $prodSingle);
            }
            return array($returnResponse);
        } else {
            $prod = DB::table('xref_product_categories')
                    ->select('products.*')
                    ->join('products', 'products.id', '=', 'xref_product_categories.fk_product_id')
                    ->whereIn('fk_category_id', $aCatId)
                    ->get();
            return $prod;
        }
    }

    /**
     * Return store sales data in detail.
     * 
     * @package Store Model
     * @param Int $storeId
     * @return Array
     */
    public function getStoreSalesData($storeId) {
        $todaySales = $this->getStoreTodaySales($storeId);
        $lastWeekSales = $this->getStoreLastWeekSales($storeId);
        $lastMonthSales = $this->getStoreLastMonthSales($storeId);
        $totalSales = $this->getStoreTotalSales($storeId);
        return array(
            'today_sales' => $todaySales,
            'last_week_sales' => $lastWeekSales,
            'last_month_sales' => $lastMonthSales,
            'total_sales' => $totalSales,
        );
    }

    /**
     * Fetches today's sale for a store.
     * 
     * @package Store Model
     * @param Int $storeId
     * @return int
     */
    private function getStoreTodaySales($storeId) {
        $results = DB::select(DB::raw("SELECT SUM(ROUND(soi.store_price - (soi.store_price * (soi.vendor_commission / 100)),2)) as sales FROM "
                . "sales_order_item AS soi INNER JOIN sales_order AS so "
                . "ON so.id_sales_order = soi.fk_sales_order_id "
                . "WHERE YEAR(soi.updated_at) = YEAR(NOW()) AND MONTH(soi.updated_at) = MONTH(NOW()) AND DAY(soi.updated_at) = DAY(NOW())"
                                . "and soi.fk_store_id = :storeId "
                . "AND so.fk_order_status_id = :orderComplete"), array(
                    'storeId' => $storeId,
                    'orderComplete' => config('appConstants.order_complete_condition'),
        ));
        $sales = $results[0]->sales;
        if (empty($sales)) {
            $sales = 0;
        }
        return $sales;
    }

    /**
     * Fetches last Month Sales for a store.
     * 
     * @package Store Model
     * @return int
     */
    private function getStoreLastMonthSales($storeId) {
        $results = DB::select(DB::raw("SELECT SUM(ROUND(soi.store_price - (soi.store_price * (soi.vendor_commission / 100)),2)) as sales FROM"
                . " sales_order_item AS soi INNER JOIN sales_order AS so "
                . "ON so.id_sales_order = soi.fk_sales_order_id "
                . "WHERE YEAR(soi.updated_at) = YEAR(NOW()) AND MONTH(soi.updated_at)=MONTH(NOW()) -1 "
                                . "and soi.fk_store_id = :storeId "
                . "AND so.fk_order_status_id = :orderComplete"), array(
                    'storeId' => $storeId,
                    'orderComplete' => config('appConstants.order_complete_condition'),
        ));
        $sales = $results[0]->sales;
        if (empty($sales)) {
            $sales = 0;
        }
        return $sales;
    }

    /**
     * Fetches last week sales for a store.
     * 
     * @package Store Model
     * @param Int $storeId
     * @return array
     */
    private function getStoreLastWeekSales($storeId) {
        $results = DB::select(DB::raw("SELECT DAYNAME(soi.created_at) AS weekday, "
                . "SUM(ROUND(soi.store_price - (soi.store_price * (soi.vendor_commission / 100)),2)) as sales FROM sales_order_item AS soi "
                . "INNER JOIN sales_order AS so ON so.id_sales_order = soi.fk_sales_order_id "
                . "WHERE WEEKOFYEAR(soi.updated_at)=WEEKOFYEAR(NOW()) -1 "
                . "and soi.fk_store_id = :storeId GROUP BY DAYNAME(soi.updated_at)"
                . "AND so.fk_order_status_id = :orderComplete"), array(
                    'storeId' => $storeId,
                    'orderComplete' => config('appConstants.order_complete_condition'),
        ));
        $aWeekSales = $this->aWeekDay;
        $lastWeekSales = 0;
        foreach ($results as $result) {
            if (isset($this->aWeekDay[$result->weekday])) {
                $aWeekSales[$result->weekday] = $result->sales;
                $lastWeekSales = $lastWeekSales + $result->sales;
            }
        }
        return array(
            'week_detail' => $aWeekSales,
            'week_total' => $lastWeekSales,
        );
    }

    /**
     * Get total sales of Store
     * 
     * @package Store Model
     * @param Int $storeId
     * @return int
     */
    private function getStoreTotalSales($storeId) {
        $results = DB::select(DB::raw("SELECT SUM(ROUND(soi.store_price - (soi.store_price * (soi.vendor_commission / 100)),2)) as sales FROM"
                . " sales_order_item AS soi INNER JOIN sales_order AS so ON so.id_sales_order = soi.fk_sales_order_id "
                . "WHERE soi.fk_store_id = :storeId AND so.fk_order_status_id = :orderComplete"), array(
                    'storeId' => $storeId,
                    'orderComplete' => config('appConstants.order_complete_condition'),
        ));
        $sales = $results[0]->sales;
        if (empty($sales)) {
            $sales = 0;
        }
        return $sales;
    }

    /*
     * @TODO : Logic needs to be change also model
     */

    /**
     * Fetch Bestsellar products
     * 
     * @package Store Model
     * @return Object
     */
    public function getBestSellerProducts() {
        $prodModel          = new Product();
        $bestSellerProducts = $prodModel->getBestSellerProducts();
        return $bestSellerProducts;
    }

    /**
     * Update Order Item in database
     * 
     * @package Store Model
     * @param Int $storeId
     * @param Int $riderId
     */
    public function updateOrderItem($storeId, $riderId) {
        $response = array(
            'status' => FALSE,
            'message' => '',
        );
        $validTransaction = TRUE;
        try {
            $orderItem = self::getOrderArray();
            DB::beginTransaction();
            foreach ($orderItem as $key => $value) {
                if (isset($value['newQty'])) {
                    $quantity = $value['newQty'];
                    while ($quantity > 0) {
                        $affected = DB::table('sales_order_item')
                                ->where('fk_driver_id', \Config::get('appConstants.available_order_condition'))
                                ->where('fk_order_status_id', \Config::get('appConstants.available_order_condition'))
                                ->where('fk_store_id', $storeId)
                                ->where('fk_product_id', $value['fk_product_id'])
                                ->where('fk_sales_order_id', $value['fk_sales_order_id'])
                                ->limit(1)
                                ->update(['fk_driver_id' => $riderId, 'fk_order_status_id' => 2, 'updated_at' => (DB::raw('CURRENT_TIMESTAMP'))]);
                        if ($affected == '0') {
                            $validTransaction = false;
                            $response['message'] = 'Status of some items have already changed.';
                            SessionModel::forgetSessionVariable('vendor');
                            return $response;
                            break;
                        }
                        self::updateSalesOrderStatus($value['fk_sales_order_id']);
                        $quantity--;
                    }
                } else {
                    Log::error('updateOrdertem|"newQty" not present in session');
                }
            }
            SessionModel::forgetSessionVariable('vendor');
            DB::commit();
            $response['status'] = TRUE;
        } catch (Exception $ex) {
            Log::error('error in orderComplete' . $ex->getMessage());
            $response['message'] = $ex->getMessage();
            DB::rollBack();
        }
        return $response;
    }

    /**
     * Get only order items array.
     * 
     * @package Store Model
     * @return type
     */
    public function getOrderArray() {
        try {
            $orderItem = SessionModel::getSessionVariable('vendor.orderData');
            unset($orderItem['orderId']);
            unset($orderItem['total']);
            return $orderItem;
        } catch (Exception $ex) {
            Log::error('error in getOrderArray' . $ex->getMessage());
        }
    }

    /**
     * Provides order History.
     * 
     * @package Store Model
     * @param Int $storeId
     * @param Int $orderId
     * @param Int $paginate
     * @return object
     */
    public function getStoreHistory($storeId, $orderId = NULL, $paginate = NULL) {
        try {
            $orderItem = DB::table('sales_order_item')
                    ->select('sales_order_item.*', 'sales_order.order_id AS orderId', DB::raw('COUNT(id_sales_order_item) AS quantity'),
                            DB::raw('SUM(sales_order_item.store_price - (sales_order_item.store_price * (sales_order_item.vendor_commission / 100))) AS totalPrice'))
                    ->addSelect(DB::raw('sales_order_item.store_price - (sales_order_item.store_price * (sales_order_item.vendor_commission / 100)) AS store_price'))
                    ->addSelect(DB::raw('GROUP_CONCAT(DISTINCT(sales_order_item.fk_product_id)) AS productId'))
                    ->join('sales_order', 'sales_order.id_sales_order', '=', 'sales_order_item.fk_sales_order_id')
                    ->where('fk_driver_id', '!=', \Config::get('appConstants.available_order_condition'))
                    ->where('sales_order_item.fk_order_status_id', '!=', \Config::get('appConstants.available_order_condition'))
                    ->where('fk_store_id', '=', $storeId)
                    ->orderBy('sales_order_item.updated_at', 'DESC');
            if (NULL != $orderId) {
                $orderItem = $orderItem->addSelect('products.name', 'products.barcode', 'products.description')->addSelect(DB::raw('COUNT(id_sales_order_item) AS prodQuantity'))->join('products', 'products.id', '=', 'sales_order_item.fk_product_id')
                                ->where('fk_sales_order_id', $orderId)->groupBy('fk_product_id');
            } else {
                $orderItem = $orderItem->groupBy('fk_sales_order_id');
            }
            if (NULL != $paginate) {
                $orderItem = $orderItem->paginate($paginate);
            } else {
                $orderItem = $orderItem->get();
            }
            foreach ($orderItem as $key => $value) {
                $productIds = array_slice(explode(',', $value->productId), 0, 7);
                $product = new Product();
                $productDetail['productDetail'] = $product->getProductDetails($productIds, TRUE);
                $orderItem[$key] = collect($orderItem[$key])->merge(collect($productDetail));
            }
            return $orderItem;
        } catch (Exception $ex) {
            Log::error('error in getOrderArray' . $ex->getMessage());
        }
    }

    /**
     * Return boolean product is in stock or not
     * 
     * @package Store Model
     * @param Int $productId
     * @param Int $storeId
     * @return boolean
     */
    public function isProductInStock($productId, $storeId) {
        $storeProduct = DB::table('products_store')
                ->select('fk_product_id as product_id')
                ->addSelect('vendor_price as vendor_price')
                ->where('fk_user_id', '=', $storeId)
                ->where('fk_product_id', '=', $productId)
                ->first();
        $productStock = FALSE;
        if (!empty($storeProduct)) {
            $productStock = ['status' => TRUE, 'vendor_price' => $storeProduct->vendor_price];
        }else{
            $productPrice = DB::table('products')
                ->select('store_price as vendor_price')
                ->where('id', '=', $productId)
                ->first();
            $productStock = ['status' => FALSE, 'vendor_price' => $productPrice->vendor_price];
        }
        return $productStock;
    }

    /**
     * Update sales_order table status to collected if all the items are 
     * collected by driver.
     * 
     * @package Store Model
     * @param Int $salesOrderId
     * @return void
     */
    private function updateSalesOrderStatus($salesOrderId) {
        $isCollected = TRUE;
        $data = DB::table('sales_order_item')
                        ->select('sales_order_item.fk_order_status_id')
                        ->where('fk_sales_order_id', '=', $salesOrderId)->get();
        foreach ($data as $key => $value) {
            if ($value->fk_order_status_id != 2) {
                $isCollected = FALSE;
                return;
            }
        }
        if ($isCollected) {
            $newStatusId        = OrderStatus::ORDER_STATUS_COLLECTED_ID;
            $orderStatusModel   = new OrderStatus();
            $orderStatusModel->changeOrderStatusInternal($salesOrderId, $newStatusId);
        }
    }

    /**
     * Get order sales_order-id on the basis of order number.
     * 
     * @package Store Model
     * @param string $orderNumber
     * @return array
     */
    public function getOrderId($orderNumber) {
        return DB::table('sales_order')
                        ->where('order_id', '=', $orderNumber)
                        ->pluck('id_sales_order');
    }

    /**
     * Return validation rules for Store Registration.
     * 
     * @return array
     */
    public function getValidationRulesForRegistration($businessType = null, $legalValidations = true) {
        $rules = array(
            'store_name'    => 'required|max:255',
            'email'         => 'required|email|max:255|unique:users',
            'CaptchaCode'   => 'valid_captcha'
        );
        $userType = config('mangopay.legal_user_type');
        if ($businessType == $userType['BUSINESS']) {
            $rules['cname'] = 'required|max:255';
            $rules['director'] = 'required|max:255';
        }
        $addressRules = $this->getValidationRulesForStoreAddress();
        $rules = array_merge($rules, $addressRules);
        if ($legalValidations) {
            $legalRules = $this->getValidationRulesForLegalUser();
            $rules = array_merge($rules, $legalRules);
        }
        return $rules;
    }

    /**
     * Method to register Vendor on Alchemy Platform
     * @param array $data Data required for Registration
     * @return array $aResponse
     *
     */
    public function registerVendor(array $data) {
        $aResponse = array(
            'status' => false,
            'message' => trans('messages.common_error'),
        );
        try {
            DB::beginTransaction();
            $token = CommonHelper::getToken(); //Generate Token
            $data['token'] = $token;
            $userData = $this->getUserModel()->createVendorUser($data);
            if ($userData->id) {
                $data['id_user'] = $userData->id;
                $this->_getStoreDetailsModel()->createNewStore($data); // save vendor details.
                $this->_getSubStoreDetailsModel()->createSubStore($data); //save sub store details.
                $this->_getStoreAddressModel()->createStoreAddress($data); // save store address.
                $this->saveStoreRegisteredAddress($data, $userData->id); // save vendor_registered address
                $this->saveStoreLegalAddress($data, $userData->id); // save vendor legal address
                DB::commit();
                $link = route('home.activate');
                $data = [
                    'firstName' => $data['legal_representative_fname'],
                    'lastName'  => $data['legal_representative_lname'],
                    'email'     => $userData->email,
                    'link' => $link . '/' . $token
                ];
//                $mergeVars = array(array('name' => 'data', 'content' => $data));
//                $emailResp = Email::sendEmail($userData->email, $mergeVars, 'Register Vendor');
                $emailResp = EmailForce24::sendEmailAPI($data, 'vendor_registration');
                if ($emailResp['status'] == TRUE) {
                    $aResponse['message'] = trans('messages.store_register_success');
                    $aResponse['status'] = true;
                } else {
                    $aResponse['message'] = trans('messages.email_failure');
                    $aResponse['status'] = false;
                }
            }
        } catch (Exception $ex) {
            DB::rollBack();
            CommonHelper::event($ex->getMessage() . "|" . $ex->getFile() . "|" . $ex->getLine(), CommonHelper::USER_REGISTER_LOG_FILE, CommonHelper:: DAILY);
        }
        return $aResponse;
    }

    /**
     * Return validation rules for Store Address
     * 
     * @return array
     */
    public function getValidationRulesForStoreAddress() {
        $rules = array(
            'address' => 'required|max:255',
            'city' => 'required|max:255',
            'pin' => 'required|max:255',
            'state' => 'required|max:255',
        );
        return $rules;
    }
            
    /**
     * Return validation rules for Store Address
     * 
     * @return array
     */
    public function getValidationRulesForLegalUser() {
        $rules = array(
            'legal_representative_fname' => 'required|max:255',
            'legal_representative_lname' => 'required|max:255',
            'legal_representative_dob_dd' => 'required',
            'legal_representative_dob_mm' => 'required',
            'legal_representative_dob_yy' => 'required',
            'legal_representative_country_residence' => 'required',
            'legal_representative_nationality' => 'required',
        );
        return $rules;
    }


    /**
     * Method to save Store Address
     * @param array $data Address data
     * @param int $storeId StoreId
     * @return array $aResponse
     *
     */
    public function saveStoreAddress(array $data, $storeId) {
        $aResponse = array(
            'status' => false,
            'message' => trans('messages.common_error'),
        );
        try {
            $postCode = $data['pin'];
            $commonModel = new CommonHelper();
            $latlngResponse = $commonModel->getLatLngFromPostCode($postCode);
            $aUpdate = array_only($data, ['address', 'city', 'state', 'pin']);
            if ($latlngResponse['status']) {
                $aUpdate['lat'] = $latlngResponse['data']['lat'];
                $aUpdate['lng'] = $latlngResponse['data']['lng'];
            }
            $storeAddress = StoreAddress::where('fk_users_id', $storeId)->first();
            if (empty($storeAddress)) {
                $aUpdate['fk_users_id'] = $storeId;
                StoreAddress::create($aUpdate);
            } else {
                StoreAddress::where('fk_users_id', $storeId)
                        ->update($aUpdate);
            }
            // Clears cache as near by store as are set In cache day Wise & postcode. Check StoreAddress->getNearbyStores
            Cache::flush();
            $aResponse['status'] = true;
            $aResponse['message'] = trans('messages.store_address_edit_success');
        } catch (Exception $ex) {
            Log::error(__METHOD__ . $ex->getMessage() . "|" . $ex->getLine());
        }
        return $aResponse;
    }

    /**
     * Method to get address for store
     * @param int $storeId StoreId
     *
     */
    public function getStoreAddressByStoreId($storeId) {
        try {
            $aStoreAddress = StoreAddress::where('fk_users_id', $storeId)->first();
            return $aStoreAddress;
        } catch (Exception $ex) {
            Log::error(__METHOD__ . $ex->getMessage() . "|" . $ex->getLine());
        }
    }

    /**
     * Function to getCustomised Validation message for Address Field
     * 
     * @return array
     */
    public function validationMessagesForAddress() {
        return [
            'address.required' => 'Address is required.',
            'city.required' => 'Town Name is required.',
            'pin.required' => 'PostCode is required.',
            'state.required' => 'Country is required.',
            'cname.required' => 'Registered business name is required.',
            'name.required' => 'Store name name is required.',
        ];
    }

    /**
     * Method to get store timings.
     * 
     * @param Int $storeId
     * @return array
     */
    public function getStoreTimings($storeId) {
        $aResponse = array(
            'data' => '',
            'status' => false,
            'message' => trans('messages.common_error'),
        );
        try {
            $data = $this->getStoreTime($storeId);
            if (empty($data)) {
                $this->createDefaultTimeEntries($storeId);
                $data = $this->getStoreTime($storeId);
            }
            $aResponse['data'] = $data;
            $aResponse['status'] = true;
            $aResponse['message'] = trans('messages.store_time_success');
        } catch (Exception $ex) {
            Log::error(__METHOD__ . $ex->getMessage() . "|" . $ex->getLine());
        }
        return $aResponse;
    }
    
    /**
     * Method to get store timings.
     * 
     * @param Int $storeId
     * @return array
     */
    public function getTimings($storeId, $theDay) {
        $aResponse = array(
            'data' => '',
            'status' => false,
            'message' => trans('messages.common_error'),
        );
        try {
            $data = $this->getTime($storeId, $theDay);            
            $aResponse['data'] = $data;
            $aResponse['status'] = true;
            $aResponse['message'] = trans('messages.store_time_success');
        } catch (Exception $ex) {
            Log::error(__METHOD__ . $ex->getMessage() . "|" . $ex->getLine());
        }
        return $aResponse;
    }    

    /**
     * Return store time from database.
     * 
     * 
     * @param Int $storeId
     * @return store_timing object
     */
    private function getStoreTime($storeId) {
        return DB::table('store_timings AS st')
                        ->select(DB::raw("SUBSTR(st.day,1,3) AS day"))
                        ->addSelect('st.is_closed')
                        ->addSelect('st.id')
                        ->addSelect('st.is_24hrs')
                        ->addSelect('st.open_time')
                        ->addSelect('st.close_time')
                        ->where('fk_user_id', '=', $storeId)->get();
    }
    
    /**
     * Return store time from database.
     * 
     * 
     * @param Int $storeId
     * @return store_timing object
     */
    private function getTime($storeId , $theDay) {
        return DB::table('timings AS st')
                        ->select("st.day AS day")
                        ->addSelect('st.is_closed')
                        ->addSelect('st.id')
                        ->addSelect('st.is_24hrs')
                        ->addSelect(DB::raw("SUBSTR(st.close_time,1,5) AS close_time"))
                        ->addSelect(DB::raw("SUBSTR(st.open_time,1,5) AS open_time"))
                        ->where('fk_user_id', '=', $storeId )
                        ->where('st.day', '=', $theDay)->get();
    }
    
    /**
     * Method to create default entires for store timing.
     * 
     * @param Int $storeId
     */
    private function createDefaultTimeEntries($storeId) {
        try {
            $insertData = array();
            $days = \Config::get('appConstants.store_days');
            foreach ($days as $day) {
                $aData = array(
                    'fk_user_id' => $storeId,
                    'day' => $day,
                );
                array_push($insertData, $aData);
            }
            DB::table('store_timings')->insert($insertData);
        } catch (Exception $ex) {
            Log::error(__METHOD__ . $ex->getMessage() . "|" . $ex->getLine());
        }
    }

    
    /**
     * Method to update store timing in database.
     * 
     * @param Int $storeId
     * @param array $data
     */
    public function updateStoreTimeEntries($storeId, array $data) {
        try {
            $weekday = explode(',', rtrim($data['weekdayId'], ','));
            foreach ($weekday as $key) {
                $aData = array(
                    'is_closed' => isset($data['is_closed'][$key]) ? 1 : 0,
                    'is_24hrs' => isset($data['is_24hrs'][$key]) ? 1 : 0,
                    'open_time' => !empty($data['open_time'][$key]) ? $data['open_time'][$key] : '00:00',
                    'close_time' => !empty($data['close_time'][$key]) ? $data['close_time'][$key] : '00:00'
                );
                DB::table('store_timings')->where('fk_user_id', '=', $storeId)
                        ->where('id', '=', $key)->update($aData);
            }
            // Clears cache as near by store as are set In cache day Wise. Check StoreAddress->getNearbyStores
            Cache::flush();
        } catch (Exception $ex) {
            Log::error(__METHOD__ . $ex->getMessage() . "|" . $ex->getLine());
        }
    }
    
     /**
     * Method to update store timing in database.
     * 
     * @param Int $storeId
     * @param array $data
     */
     public function updateSiteTimeEntries($storeId, array $data) {
        try {
            $insertData = array();
            $is_24hrs = 0;
            $is_closed = 0;
            if ($data['schedule'] == "is_24hrs") {
                $is_24hrs = 1;
            } elseif ($data['schedule'] == "is_closed") {
                $is_closed = 1;
            }         
            if ($is_closed == 0 && $is_24hrs == 0) {
                foreach ($data['site_time'] as $key => $val) {
                    $open_close_time = explode("-", $key);
                    $aData = array(
                        'day' => isset($data['theDay']) ? $data['theDay'] : '',
                        'is_closed' => $is_closed,
                        'is_24hrs' => $is_24hrs,
                        'open_time' => !empty($open_close_time[0]) ? $open_close_time[0] : '00:00',
                        'close_time' => !empty($open_close_time[1]) ? $open_close_time[1] : '00:00',
                        'fk_user_id' => $storeId
                    );
                    array_push($insertData, $aData);
                }
            } else {
                $aData = array(
                    'day' => isset($data['theDay']) ? $data['theDay'] : '',
                    'is_closed' => $is_closed,
                    'is_24hrs' => $is_24hrs,
                    'open_time' => '00:00',
                    'close_time' => '00:00',
                    'fk_user_id' => $storeId
                );
                array_push($insertData, $aData);
            }
            //print_r($insertData);exit;
            DB::table('timings')->where([
                ['fk_user_id', $storeId],
                ['day', $data['theDay']],
            ])->delete();
            DB::table('timings')->insert($insertData);

            // Clears cache as near by store as are set In cache day Wise. Check StoreAddress->getNearbyStores
            Cache::flush();
        } catch (Exception $ex) {
            Log::error(__METHOD__ . $ex->getMessage() . "|" . $ex->getLine());
        }
    }

    /**
     * FUnction return Company house API Response.
     * 
     * @param string $storename
     * @param boolean $resultAll
     * @param Int $items_per_page
     * @param Int $start_index
     * @return Array
     */
    public function getStoreCompanyHouseDetails($storename, $showALL = FALSE, $items_per_page = 0, $start_index = 1) {
        $response = array(
            'status' => false,
            'message' => '',
            'data' => '',
        );
        try {
            $client = new Client();
            $res = $client->request('GET', 'https://api.companieshouse.gov.uk/search/companies?q=' . $storename
                    . '&items_per_page=' . $items_per_page . '&start_index=' . $start_index, [
                'headers' => [
                    'Authorization' => 'Basic ' . env('COMPANY_HOUSE_API_KEY')
                ]
                    ]
            );
            if ($res->getStatusCode() == '200') {
                $responseBody = json_decode($res->getBody(), TRUE);
                if (isset($responseBody['items'])) {
                    if($showALL){
                        $response['data']= $responseBody['items'];
                        $response['status'] = TRUE;
                        $response['message'] = trans('messages.details_found');
                        return $response;
                    }
                    foreach ($responseBody['items'] as $key => $storeDetails) {
                        if (strtolower(trim($storeDetails['title'])) == strtolower(trim($storename)) || strtolower(trim($storeDetails['snippet'])) == strtolower(trim($storename))) {
                            $response['data'] = $storeDetails['address'];
                            $response['status'] = TRUE;
                            $response['message'] = trans('messages.details_found');
                        }
                    }
                } elseif (isset($responseBody['error'])) {
                    CommonHelper:: event($responseBody['error'], CommonHelper::PAYMENT_KYC_LOG_FILE, CommonHelper:: DAILY);
                }
            } else {
                $response['message'] = trans('messages.company_house_api_error');
            }
        } catch (Exception $ex) {
            Log::error(__METHOD__ . $ex->getMessage() . "|" . $ex->getLine());
            CommonHelper::event($ex->getMessage() . "|" . $ex->getLine(), CommonHelper::PAYMENT_KYC_LOG_FILE, CommonHelper:: DAILY);
        }
        return $response;
    }
    
    /**
     * Save Legal Address in database.
     * @param array $data
     * @param Int $storeId
     * @return Array
     */
    public function saveStoreLegalAddress(array $data, $storeId) {
        $aResponse = array(
            'status' => false,
            'message' => trans('messages.common_error'),
        );
        try {
            $aUpdate = array_only($data, ['legal_representative_address_1',
                'legal_representative_address_2', 'legal_representative_city',
                'legal_representative_region', 'legal_representative_postcode',
                'legal_representative_country',
                'legal_representative_country_residence',
                'legal_representative_nationality', 'legal_premises']);
            $aUpdate['fk_users_id'] = $storeId;
            if($data['business_type'] == 'SOLETRADER'){
                $aUpdate['legal_premises'] = isset($data['address']) ? $data['address'] : $data['legal_representative_region'];
                $aUpdate['legal_representative_address_1'] = isset($data['legal_representative_address_1']) ? $data['legal_representative_address_1'] : '';
                $aUpdate['legal_representative_address_2'] = isset($data['legal_representative_address_2']) ? $data['legal_representative_address_2'] : '';
                $aUpdate['legal_representative_city'] = isset($data['city']) ? $data['city'] : $data['legal_representative_city'];
                $aUpdate['legal_representative_region'] = 'Greater London';
                $aUpdate['legal_representative_postcode'] = isset($data['pin']) ? $data['pin'] : $data['legal_representative_postcode'];
                $aUpdate['legal_representative_country'] = isset($data['state']) ? $data['state'] : $data['legal_representative_country'];
            }
            $aUpdate['address_line_1'] = !empty($aUpdate['legal_premises'])?$aUpdate['legal_premises'] :$aUpdate['legal_representative_address_1'];
            $aUpdate['address_line_2'] = !empty($aUpdate['legal_premises'])?$aUpdate['legal_representative_address_1']:$aUpdate['legal_representative_address_2'];
            $aUpdate['city'] = $aUpdate['legal_representative_city'];
            $aUpdate['region'] = $aUpdate['legal_representative_region'];
            $aUpdate['pin'] = $aUpdate['legal_representative_postcode'];
            $aUpdate['country'] = $aUpdate['legal_representative_country'];
            $exist = StoreLegalAddress::where('fk_users_id', '=', $storeId)->first();
            if($exist['id_vendor_legal_address']){
                StoreLegalAddress::where('id_vendor_legal_address', '=', $exist['id_vendor_legal_address'])
                        ->update(array_only($aUpdate,(['address_line_1', 'address_line_2', 'city', 'region', 'pin', 'country'])));
            }else{
                StoreLegalAddress::create($aUpdate);
            }
            $aResponse['status'] = true;
            $aResponse['message'] = trans('messages.store_address_edit_success');
        } catch (Exception $ex) {
            Log::error(__METHOD__ . $ex->getMessage() . "|" . $ex->getLine());
        }
        return $aResponse;
    }

    /**
     * Save Legal Address in database.
     * @param array $data
     * @param Int $storeId
     * @return Array
     */
    public function saveStoreRegisteredAddress(array $data, $storeId) {
        $aResponse = array(
            'status' => false,
            'message' => trans('messages.common_error'),
        );
        try {
            $aUpdate = array_only($data, ['headquarters_address_1',
                'headquarters_address_2', 'headquarters_city',
                'headquarters_region', 'headquarters_postcode',
                'headquarters_country', 'headquarters_premises']);
            $aUpdate['fk_users_id'] = $storeId;
            if($data['business_type'] == 'SOLETRADER'){
                $aUpdate['headquarters_premises'] = isset($data['address']) ?$data['address'] : $data['headquarters_region'];
                $aUpdate['headquarters_address_1'] = isset($data['headquarters_address_1']) ?$data['headquarters_address_1'] :'';
                $aUpdate['headquarters_address_2'] = isset($data['headquarters_address_2']) ?$data['headquarters_address_2'] :'';
                $aUpdate['headquarters_city'] = isset($data['city']) ?$data['city'] :$data['headquarters_city'];
                $aUpdate['headquarters_region'] = 'Greater London';
                $aUpdate['headquarters_postcode'] = isset($data['pin']) ?$data['pin'] :$data['headquarters_postcode'];
                $aUpdate['headquarters_country'] = isset($data['state']) ?$data['state'] :$data['headquarters_country'];
            }
            $aUpdate['address_line_1'] = !empty($aUpdate['headquarters_premises'])?$aUpdate['headquarters_premises']:$aUpdate['headquarters_address_1'];
            $aUpdate['address_line_2'] = !empty($aUpdate['headquarters_premises'])?$aUpdate['headquarters_address_1']:$aUpdate['headquarters_address_2'];
            $aUpdate['city'] = $aUpdate['headquarters_city'];
            $aUpdate['region'] = $aUpdate['headquarters_region'];
            $aUpdate['pin'] = $aUpdate['headquarters_postcode'];
            $aUpdate['country'] = $aUpdate['headquarters_country'];
            $exist = StoreRegisteredAddress::where('fk_users_id', '=', $storeId)->first();
            if($exist['id_vendor_legal_address']){
                StoreRegisteredAddress::where('id_vendor_legal_address', '=', $exist['id_vendor_legal_address'])
                        ->update(array_only($aUpdate,(['address_line_1', 'address_line_2', 'city', 'region', 'pin', 'country'])));
            }else{
                StoreRegisteredAddress::create($aUpdate);
            }
            $aResponse['status'] = true;
            $aResponse['message'] = trans('messages.store_address_edit_success');
        } catch (Exception $ex) {
            Log::error(__METHOD__ . $ex->getMessage() . "|" . $ex->getLine());
        }
        return $aResponse;
    }

    
    /**
     * Method to update store timing in database.
     * 
     * @param Int $storeId
     * @param array $data
     */
    public function getOpeningTime() {
        return DB::table('timings AS st')
                        ->select("st.day AS day")
                        ->addSelect(DB::raw("SUBSTR(st.close_time,1,5) AS close_time"))
                        ->addSelect('st.id')
                        ->addSelect('st.is_24hrs')
                        ->addSelect(DB::raw("SUBSTR(st.open_time,1,5) AS open_time"))
                        ->addSelect('st.is_closed')
                        ->where('fk_user_id', '=', 1)
                        ->orderBy('day', 'asc')
                        ->orderBy('open_time', 'asc')             
                        ->get();
    }
    
    /**
     * Method to fetch company director details.
     * 
     * @param Int $companyNumber
     * @return Array
     */
    public function getCompanyDirectorDetails($companyNumber = '09104776') {
        $response = array(
            'status' => false,
            'message' => '',
            'data' => '',
        );
        try {
            $client = new Client();
            $res = $client->request('GET', 'https://api.companieshouse.gov.uk/company/' . $companyNumber . '/officers', [
                'headers' => [
                    'Authorization' => 'Basic ' . env('COMPANY_HOUSE_API_KEY')
                ]
                    ]
            );
            if ($res->getStatusCode() == '200') {
                $responseBody = json_decode($res->getBody(), TRUE);
                if (isset($responseBody['items'])) {
                    $response['data'] = $responseBody['items'];
                    $response['status'] = TRUE;
                    $response['message'] = trans('messages.details_found');
                    return $response;
                } elseif (isset($responseBody['error'])) {
                    CommonHelper:: event($responseBody['error'], CommonHelper::PAYMENT_KYC_LOG_FILE, CommonHelper:: DAILY);
                }
            } else {
                $response['message'] = trans('messages.company_house_api_error');
            }
        } catch (Exception $ex) {
            Log::error(__METHOD__ . $ex->getMessage() . "|" . $ex->getLine());
            CommonHelper::event($ex->getMessage() . "|" . $ex->getLine(), CommonHelper::PAYMENT_KYC_LOG_FILE, CommonHelper:: DAILY);
        }
        return $response;
    }
    
    /**
     * Method to check whether Store has uploaded all required KYC Documents
     * Need to be check for Product Upload
     *
     * @param int $storeId
     * @return boolean
     */
    public function getStoreKYCStatus($storeId) {
        $isKYCComplete = false;
        try {
            $userKyc            = $this->getUserModel()->getUserCompleteMangoPayDetails($storeId);
            $userKycDetails     = $userKyc['mangoUserKyc'];
            $userKycDetails     = $userKycDetails->toArray();
            $userStoreDetails   = $userKyc['storeDetails']->toArray();
            $userMangoDetails   = $userKyc['mangoUser'];
            if (!empty($userMangoDetails)) {
                $isKYCComplete = true;
                /*$error = false;
                $aKYCStatusAll = config('mangopay.kyc_document_status');
                foreach ($userKycDetails as $kycDetail) {
                    if ($kycDetail['status'] != $aKYCStatusAll['VALIDATED']) {
                        $error = true;
                        break;
                    }
                }
                $isKYCComplete = (!$error) ? true : false;
                return true; //@todo Remove after discussion*/
            }
        } catch (Exception $ex) {
            //@No Need
        }
        return $isKYCComplete;
    }
    
    /**
     * Method to register Vendor on Alchemy Platform
     * @param array $data Data required for Registration
     * @return array $aResponse
     *
     */
    public function addNewStore(array $data) {
        $aResponse = array(
            'status' => false,
            'message' => trans('messages.common_error'),
        );
        try {
            DB::beginTransaction();
            $userData = $this->getUserModel()->createVendorUser($data);
            if ($userData->id) {
                $data['id_user'] = $userData->id;
                $this->_getSubStoreDetailsModel()->createSubStore($data); //save sub store details.
                $this->_getStoreAddressModel()->createStoreAddress($data); // save store address.
                $aResponse['message'] = trans('messages.store_register_success');
                $aResponse['status'] = true;
                DB::commit();
            }
        } catch (Exception $ex) {
            DB::rollBack();
            CommonHelper::event($ex->getMessage() . "|" . $ex->getLine(), CommonHelper::USER_REGISTER_LOG_FILE, CommonHelper:: DAILY);
        }
        return $aResponse;
    }
    
    /**
     * Method to get Store Details for MangoPay Account Creations
     * 
     * @param int $storeId
     */
    public function getVendorDetailsForAccountCreation($storeId) {
        $storeData = DB::table('users AS us')
                ->select('us.email as legal_person_email')
                ->addSelect('sd.business_type', 'sd.legal_fname as legal_representative_fname'
                        , 'sd.legal_lname as legal_representative_lname', 'sd.legal_dob'
                        , 'sd.nationality as legal_representative_nationality', 'sd.country_residence as legal_representative_country_residence')
                ->addSelect('ssd.store_name')
                ->addSelect('vla.address_line_1 as legal_representative_address_1', 'vla.address_line_2 as legal_representative_address_2'
                        , 'vla.city as legal_representative_city', 'vla.region as legal_representative_region'
                        , 'vla.pin as legal_representative_postcode', 'vla.country as legal_representative_country')
                ->addSelect('vra.address_line_1 as headquarters_address_1', 'vra.address_line_2 as headquarters_address_2'
                        , 'vra.city as headquarters_city', 'vra.region as headquarters_region'
                        , 'vra.pin as headquarters_postcode', 'vra.country as headquarters_country')
                ->join('sub_store_details AS ssd', 'ssd.fk_users_id', '=', 'us.id')
                ->join('store_details AS sd', 'sd.fk_users_id', '=', 'us.id')
                ->leftJoin('vendor_legal_address AS vla', 'vla.fk_users_id', '=', 'us.id')
                ->leftJoin('vendor_registered_address AS vra', 'vra.fk_users_id', '=', 'us.id')
                ->where('us.id', '=', $storeId)
                ->first();
        $aStoreData = json_decode(json_encode($storeData), true);
        if (!empty($aStoreData)) {
            $aLegalDob = explode("-", $aStoreData['legal_dob']);
            if($aLegalDob[0] == '0000' && $aLegalDob[1] == '00'){
                $aStoreData['legal_dob'] = date('Y-m-d');
                $aLegalDob = explode("-", $aStoreData['legal_dob']);
            }
            $aStoreData['legal_representative_dob_dd'] = $aLegalDob[2];
            $aStoreData['legal_representative_dob_mm'] = $aLegalDob[1];
            $aStoreData['legal_representative_dob_yy'] = $aLegalDob[0];
        }
        return $aStoreData;
    }

}