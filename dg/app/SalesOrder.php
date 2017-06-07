<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
use Log;
use App\User;
use Illuminate\Support\Facades\Auth;
use Exception;
use App\Http\Helper\CommonHelper;
//use App\Http\Helpers\Email;
use App\Model\OrderStatus;
use App\SubStoreDetails;
use App\Http\Helper\EmailForce24;
use App\Http\Helper\SlackService;
/**
 * SalesOrder Model
 */
class SalesOrder extends Model {

    /**
     *
     * @var table name 
     */
    protected $table = "sales_order";
    //only allow the following items to be mass-assigned to our model
    /**
     *
     * @var Allow write on db table columns 
     */
    protected $fillable = ['order_id', 'fk_users_id', 'fk_sales_address_id', 'total', 'rider_charges', 'estimated_delivery_time', 'fk_order_status_id'];
    protected $generatedOrderNumber = NULL;
    protected $_orderId = NULL;
    
    protected $_orderAddress = NULL;
    protected $_orderEstimatedDeliveryDateTime = NULL;
    
    private $cartModel = null;
    
    private function setCartModel($userId) {
        if ($this->cartModel == null) {
            $this->cartModel = new Cart($userId);
        }
        return $this->cartModel;
    }

    private $orderStatusModel = null;

    private function getOrderStatusModel() {
        if ($this->orderStatusModel == null) {
            $this->orderStatusModel = new OrderStatus();
        }
        return $this->orderStatusModel;
    }

    /**
     * Wrapper Method for storing order Detials in DB.
     *
     * @package SalesOrder Model
     * @param array $cartContent
     * @param decimal $cartTotal Total Value
     * @param int $cartQuantity Total quantity
     * @return array $response
     */
    public function placeOrder($cartContent, $cartTotal, $cartQuantity, $userId) {
        $response = array(
            'status' => FALSE,
            'message' => trans('messages.common_error'),
            'data' => '',
        );
        try {
            $this->setCartModel($userId);
            if (!empty($cartQuantity)) {
                $isValidOrder = $this->_validateOrder($cartContent, $cartTotal);
                if ($isValidOrder) {
                    $response['status'] = $this->_saveOrder($cartContent, $cartTotal, $userId);
                    $response['data'] = array(
                        'orderNumber'   => $this->generatedOrderNumber, 
                        'orderId'       => $this->_orderId,
                        'orderAddress'     => $this->_orderAddress,
                        'orderEstimatedDeliveryDateTime'     => $this->_orderEstimatedDeliveryDateTime,
                    );
                } else {
                    $errMsg = trans('messages.order_error') . trans('messages.order_error_invalid_order') . trans('messages.order_error_price_mismatch');
                    CommonHelper::event($errMsg, CommonHelper::ORDER_LOG_FILE, CommonHelper::DAILY);
                }
            }
        } catch (Exception $ex) {
            $errMsg = trans('messages.order_exception') . $ex->getMessage();
            CommonHelper::event($errMsg, CommonHelper::ORDER_LOG_FILE, CommonHelper::DAILY);
        }
        return $response;
    }

    /**
     * Protected Method for revalidate order at server side.
     *
     * @package SalesOrder Model
     * @param array $cartContent
     * @param decimal $cartTotal Total Value
     * @return boolean
     */
    protected function _validateOrder($cartContent, $cartTotal) {
        $orderTotal = 0.00;
        foreach ($cartContent as $key => $content) {
            $productPrice = $content['price'];
            $productQuantity = $content['qty'];
            $totalprice = $productPrice * $productQuantity;
            $orderTotal += $totalprice;
        }
        $orderTotal = $this->cartModel->getAmountAfterDiscount($orderTotal);
        //$driverCharge = \Config::get('appConstants.driver_charge');
        $driverCharges = $this->cartModel->getConsolidatedDeliveryCharges();
        $orderTotal += $driverCharges;
        $orderTotal = CommonHelper::formatPrice($orderTotal);
        if ($orderTotal === $cartTotal) {
            return TRUE;
        }
        return FALSE;
    }

    /**
     * Protected Wrapper Method for storing data in order address table
     * & items table
     *
     * @package SalesOrder Model
     * @param array $cartContent
     * @param decimal $cartTotal Total Value
     * @return boolean
     */
    protected function _saveOrder($cartContent, $cartTotal, $userId) {
        try {
            DB::beginTransaction();
            $addressId = $this->_saveOrderAddress($userId);
            $this->_orderId = $orderId = $this->_saveOrderData($cartTotal, $addressId, $userId);
            $this->_saveOrderItems($orderId, $cartContent);
            $this->_saveOrderAdditionalData($orderId);
            if ($this->cartModel->hasCouponCode()) {
                $aCouponDetails = $this->cartModel->getCouponDetails();
                $couponId       = $aCouponDetails['id'];
                $this->_logCouponUsage($couponId, $userId);
            }
            DB::commit();
            return TRUE;
        } catch (Exception $ex) {
            DB::rollBack();
            $errMsg = trans('messages.order_exception') . $ex->getMessage() . $ex->getFile() . $ex->getLine();
            CommonHelper::event($errMsg, CommonHelper::ORDER_LOG_FILE, CommonHelper::DAILY);
        }
        return FALSE;
    }

    /**
     * Protected Method for storing order address data
     * 
     * @package SalesOrder Model
     * @return int $addressId Primary key of newly inserted address
     */
    protected function _saveOrderAddress($userId) {
        $userModel = new User();
        $userDetail = $userModel->getUserAddress($userId);
        $firstName = $userDetail->first_name;
        $lastName = $userDetail->last_name;

        $userDetail = $userDetail->toArray();
        $userAddress = $userDetail['user_address'];
        // Unset Not required Columns
        unset(
                $userAddress['id_user_addres'], $userAddress['fk_users_id'], $userAddress['created_at'], $userAddress['updated_at']
        );
        $userAddress['first_name'] = $firstName;
        $userAddress['last_name'] = $lastName;
        $userAddress['created_at'] = CommonHelper::getCurrentDateTime();
        $userAddress['updated_at'] = CommonHelper::getCurrentDateTime();
        $this->_orderAddress = $userAddress;
        $addressId = DB::table('sales_order_address')->insertGetId($userAddress);
        return $addressId;
    }

    /**
     * Protected Method for storing data in sales_order table
     *
     * @package SalesOrder Model
     * @param decimal $cartTotal Total Price
     * @param int $addressId Adress id
     * @return int $orderId Primary key of newly inserted order
     */
    protected function _saveOrderData($cartTotal, $addressId, $userId) {
        $orderNumber = $this->generateOrderNumber();
        $orderCoupon = $this->cartModel->getCouponCode();
        $driverCharges = $this->cartModel->getDriverDeliveryCharges();
        $orderDateTime = date('Y-m-d H:i:s');
        $configModel            = new Configurations();
        $estimatedDeliveryTime      = $configModel->get(config('configurations.order_delivery_time_key'));
        $estimatedDeliveryDateTime  = date("Y-m-d H:i:s", strtotime('+' . $estimatedDeliveryTime . ' minutes', strtotime($orderDateTime)));
        $this->_orderEstimatedDeliveryDateTime       = $estimatedDeliveryDateTime;
        $insertData = array(
            'order_id' => $orderNumber,
            'fk_users_id' => $userId,
            'fk_sales_address_id' => $addressId,
            'total' => $cartTotal,
            'rider_charges' => $driverCharges,
            'estimated_delivery_time' => $estimatedDeliveryTime,
            'created_at' => $orderDateTime,
            'updated_at' => $orderDateTime
        );
        if (!empty($orderCoupon)) {
            $insertData['coupon'] = $orderCoupon;
        }
        $orderId = DB::table('sales_order')->insertGetId($insertData);
        return $orderId;
    }

    /**
     * Protected Method for storing data in sales_order_item table
     *
     * @package SalesOrder Model
     * @param int $orderId Sales Order Primary Id
     * @param array $cartContent Cart Item data
     * @return null
     */
    protected function _saveOrderItems($orderId, $cartContent) {
        $insertData = array();
        $orderDefault = \Config::get('appConstants.available_order_condition');
        foreach ($cartContent as $cartKey => $content) {
            $aCartKey = explode("_", $cartKey);
            $bundleId = $aCartKey[0];
            $productId = $aCartKey[1];
            $productPrice = $content['price'];
            $storePrice = $content['store_price'];
            $storeId = $content['store_id'];
            $productQuantity = $content['qty'];
            for ($i = 1; $i <= $productQuantity; $i++) {
                $aData = array(
                    'fk_sales_order_id' => $orderId,
                    'fk_product_id' => $productId,
                    'GTIN' => $productId,
                    'bundle_id' => $bundleId,
                    'fk_driver_id' => $orderDefault,
                    'fk_order_status_id' => $orderDefault,
                    'fk_store_id' => $storeId,
                    'price' => $productPrice,
                    'store_price' => $storePrice,
                    'product_commission' => $content['productCommission'],
                    'vendor_commission' => $content['vendorCommission'],
                    'created_at' => CommonHelper::getCurrentDateTime(),
                    'updated_at' => CommonHelper::getCurrentDateTime()
                );
                array_push($insertData, $aData);
            }
        }
        DB::table('sales_order_item')->insert($insertData);
        //DB::statement('call updateStorePrice()');
    }

    /**
     * Method for generating random unique order number
     *
     * @package SalesOrder Model
     * @return string $orderNumber
     */
    protected function generateOrderNumber() {
        //$orderNumber = uniqid("AL-");
        $orderNumber = $this->_generateOrderNumber();
        $this->generatedOrderNumber = $orderNumber;
        return $orderNumber;
    }

    /**
     * Get all sales order for admin.
     * 
     * @package SalesOrder Model
     * @param int $id Optional Sales Order Id
     * @return Object mixed
     */
    public function getSalesOrder($id = NULL) {
        $data = DB::table('sales_order AS so')
                ->select('so.id_sales_order', 'so.order_id AS orderId', 'so.total as totalPrice', 'users.first_name', 'users.last_name', 'users.email'
                        , 'soa.phone', 'soa.address', 'soa.city', 'soa.state'
                        , 'soa.pin', 'order_status.name AS orderStatus'
                        , 'so.coupon', 'so.created_at', 'so.fk_order_status_id as order_status_id')
                ->selectRaw('count(soi.id_sales_order_item) as items_count')
                ->selectRaw('group_concat(soi.fk_order_status_id) as item_status')
                ->join('users', 'users.id', '=', 'so.fk_users_id')
                ->join('sales_order_address AS soa', 'soa.id_sales_order_address', '=', 'so.fk_sales_address_id')
                ->join('order_status', 'order_status.id_order_status', '=', 'so.fk_order_status_id')
                ->join('sales_order_item AS soi', 'soi.fk_sales_order_id', '=', 'so.id_sales_order')
                ->orderBy('so.id_sales_order', 'DESC')
                ->groupBy('so.id_sales_order')
                ->where('order_status.id_order_status', '!=', 4);
        if (NULL != $id) {
            $data = $data->where('so.id_sales_order', '=', $id);
        }
        $data = $data->get();
        $orderStatusModel = $this->getOrderStatusModel();
        foreach($data as $dataSingle) {
            $dataSingle->allowed_operations = $orderStatusModel->getAllowedManualStatusForOrder(
                $dataSingle->order_status_id,
                $dataSingle->item_status
            );
        }
        return $data;
    }

    /**
     * Update Order status to closed.
     * 
     * @todo Obselete Change location also remove this function
     * @package SalesOrder Model
     * @param int $id
     * @return boolean
     */
    protected function updateStatus($id) {
        $data = DB::table('sales_order')->where('id_sales_order', '=', $id)
                ->update(['fk_order_status_id' => 3]);
        return $data;
    }

    /**
     * Update Sales_order table status to confirmed.
     * 
     * @package SalesOrder Model
     * @param Int $id
     * @return boolean
     */
    private function updateSalesOrderStatus($id) {
        $newStatusId = OrderStatus::ORDER_STATUS_CONFIRMED_ID;
        $this->getOrderStatusModel()->changeOrderStatusInternal($id, $newStatusId);
    }

    /**
     * Save data in transaction table.
     * 
     * @package SalesOrder Model
     * @param int $orderId
     * @param object $payIndata
     * @return boolean
     */
    private function saveTransactionDetails($orderId, $payIndata) {
        $insertData = array(
            'fk_order_id' => $orderId,
            'userMWalletId' => 0, //@todo can be removed
            'adminMWalletId' => $payIndata->CreditedWalletId,
            'userMId' => $payIndata->AuthorId,
            'adminMUserId' => $payIndata->CreditedUserId,
            'transferDataId' => 0, //@todo can be removed because transfer step is removed
            'payInDAtanId' => $payIndata->Id,
            'cardId' => $payIndata->PaymentDetails->CardId,
            'rawData' => json_encode($payIndata) . json_encode($payIndata)
        );
        if (DB::table('transaction_details')->insertGetId($insertData))
            return TRUE;
        else
            return FALSE;
    }

    /**
     * Update transaction data.
     * 
     * @package SalesOrder Model
     * @param int $orderId
     * @param object $transferData
     * @param object $payIndata
     */
    public function updateTransactionDetails($orderId, $payIndata) {
        $transData = self::saveTransactionDetails($orderId, $payIndata);
        if ($transData) {
            self::updateSalesOrderStatus($orderId);
        }
    }

    /**
     * Get Order Complete details
     * 
     * @package SalesOrder Model
     * @param string $orderNumber
     * @return array $orderDetails Order Item Details
     */
    public function getOrderDetails($orderNumber) {
        $orderData = DB::table('sales_order AS so')
                ->select('so.id_sales_order', 'so.order_id AS orderId', 'so.total as totalPrice'
                        , 'so.rider_charges', 'so.estimated_delivery_time', 'users.first_name', 'users.last_name', 'users.email'
                        , 'soa.phone', 'soa.address', 'soa.city', 'soa.state'
                        , 'soa.pin', 'order_status.name AS orderStatus', 'sales_order_item.fk_product_id', 'sales_order_item.bundle_id'
                        , 'so.fk_order_status_id', 'so.coupon'
                        , 'products.name as productName', 'sales_order_item.price as productPrice'
                        , 'bundles.name as bundleName ')
                ->addSelect('products.description as description')
                ->addSelect('bundles.serves as serves')
                ->addSelect('coupon.discount_amount', 'coupon.discount_type')
                ->addSelect('sales_order_additional_info.after_midnight_charge', 'sales_order_additional_info.special_category_charge')
                ->addSelect('sales_order_additional_info.min_basket_charge')
                ->join('users', 'users.id', '=', 'so.fk_users_id')
                ->join('sales_order_address AS soa', 'soa.id_sales_order_address', '=', 'so.fk_sales_address_id')
                ->join('order_status', 'order_status.id_order_status', '=', 'so.fk_order_status_id')
                ->join('sales_order_item', 'sales_order_item.fk_sales_order_id', '=', 'so.id_sales_order')
                ->leftJoin('coupon', 'coupon.coupon_code', '=', 'so.coupon')
                ->leftJoin('sales_order_additional_info', 'sales_order_additional_info.fk_sales_order_id', '=', 'so.id_sales_order')
                // @todo can be optimize
                ->leftJoin('bundles', 'sales_order_item.bundle_id', '=', 'bundles.id')
                ->join('products', 'sales_order_item.fk_product_id', '=', 'products.id');
        $orderData = $orderData->where('so.order_id', '=', $orderNumber);
        $orderData = $orderData->get();
        $orderDetails = array();
        if (!empty($orderData)) {
            //$orderData = $orderData->toArray();
            $orderNumber = $orderData[0]->orderId;
            $orderStatusId = $orderData[0]->fk_order_status_id;
            $orderAddress = $orderData[0]->address . ", "
                    . $orderData[0]->city . ", " . $orderData[0]->state . ", " . $orderData[0]->pin;
            $orderTotal = $orderData[0]->totalPrice;
            $driverCharges = $orderData[0]->rider_charges;
            $estimatedDeliveryTime = $orderData[0]->estimated_delivery_time;
            $orderId = $orderData[0]->id_sales_order;
            $coupon = $orderData[0]->coupon;
            $aCouponDetails = array();
            $aCharges = array(
                'delivery_charge' => array(
                    'label' => 'Driver Charge',
                    'value' => $orderData[0]->rider_charges,
                ),
                'after_midnight_charge' => array(
                    'label' => 'After midnight',
                    'value' => $orderData[0]->after_midnight_charge,
                ),
                'special_category_charge' => array(
                    'label' => 'Special Product Charges',
                    'value' => $orderData[0]->special_category_charge,
                ),
                'min_basket_charge' => array(
                    'label' => 'Minimum Basket Charges',
                    'value' => $orderData[0]->min_basket_charge,
                )
            );
            if (!empty($coupon)) {
                $aCouponDetails = array(
                    'type' => $orderData[0]->discount_type,
                    'amount' => $orderData[0]->discount_amount,
                    'coupon' => $coupon,
                );
            }
            $orderDetails = array(
                'orderId' => $orderId,
                'orderNumber' => $orderNumber,
                'orderStatusId' => $orderStatusId,
                'orderAddress' => $orderAddress,
                'orderTotal' => $orderTotal,
                'driverCharges' => $driverCharges,
                'estimatedDeliveryTime' => $estimatedDeliveryTime,
                'products' => array(),
                'bundle' => array(),
                'coupon_data' => $aCouponDetails,
                'charges' => $aCharges,
            );
            foreach ($orderData as $item) {
                $bundleId = $item->bundle_id;
                $productId = $item->fk_product_id;
                $productPrice = $item->productPrice;
                if ($bundleId == 0) {
                    $aProduct = array(
                        'name' => $item->productName,
                        'price' => $item->productPrice,
                        'id' => $item->fk_product_id,
                        'count' => 1,
                        'description' => $item->description,
                    );
                    if (!isset($orderDetails['products'][$productId])) {
                        $orderDetails['products'][$productId] = $aProduct;
                    } else {
                        $orderDetails['products'][$productId]['count'] ++;
                        $orderDetails['products'][$productId]['price'] = $productPrice * $orderDetails['products'][$productId]['count'];
                    }
                } else {
                    $aBundle = array(
                        'name' => $item->bundleName,
                        'id' => $bundleId,
                        'price' => $item->productPrice,
                        'serves' => $item->serves,
                    );
                    $aProduct = array(
                        'name' => $item->productName,
                        'price' => $item->productPrice,
                        'id' => $item->fk_product_id,
                        'count' => 1,
                        'description' => $item->description,
                    );
                    $aBundle['product'][$productId] = $aProduct;
                    if (!isset($orderDetails['bundle'][$bundleId])) {
                        $orderDetails['bundle'][$bundleId] = $aBundle;
                    } else {
                        $orderDetails['bundle'][$bundleId]['price'] = $orderDetails['bundle'][$bundleId]['price'] + $productPrice;
                        if (!isset($orderDetails['bundle'][$bundleId]['product'][$productId])) {
                            $orderDetails['bundle'][$bundleId]['product'][$productId] = $aProduct;
                        } else {
                            $orderDetails['bundle'][$bundleId]['product'][$productId]['count'] ++;
                        }
                    }
                }
            }
        }
        return $orderDetails;
    }
    
    /**
     * Method returns store's child Id's
     * 
     * @param Int $storeId
     * @param Boolean $withStore
     * @return Array
     */
    public function getStoreChildId($storeId, $withStore = FALSE) {
        $subStoreDetailObj = new SubStoreDetails();
        $subStoreid = $subStoreDetailObj->getStoreSubStores($storeId, FALSE);
        $userId = [];
        foreach ($subStoreid as $key => $value){
            $userId[] = $value['fk_users_id'];
        }
        if($withStore)
            return $userId;
        $userId[] = $storeId;
        return $userId;
    }

    /**
     * Function to get purchase details for last month.
     * 
     * @package SalesOrder Model
     * @param type $storeId
     * @param date $date
     * @return Object
     */
    public function getTransactionDetails($storeId, $storePaymentId = NULL) {
        $userId = $this->getStoreChildId($storeId);
        $releasedStore = DB::table('sales_order_item AS SOI')
                ->addSelect('SOI.fk_sales_order_id AS id')
                ->addSelect('SOI.fk_product_id AS productId')
                ->addSelect(DB::raw('COUNT(SOI.id_sales_order_item) AS quantity'))
                ->addSelect('SOI.price')
                ->addSelect(DB::raw('ROUND(SOI.store_price - (SOI.store_price * (SOI.vendor_commission / 100)),2) AS store_price'))
                ->addSelect(DB::raw('COUNT(SOI.id_sales_order_item) * ROUND(SOI.store_price - (SOI.store_price * (SOI.vendor_commission / 100)),2) AS totalPrice'))
                ->addSelect('SO.order_id AS orderId')
                ->addSelect('products.name AS productName')
                ->join('sales_order AS SO', 'SO.id_sales_order', '=', 'SOI.fk_sales_order_id')
                ->join('products', 'products.id', '=', 'SOI.fk_product_id')
                ->where('SOI.fk_order_status_id', '!=', config('appConstants.available_order_condition'))
                ->where('SOI.fk_driver_id', '!=', config('appConstants.available_order_condition'))
                ->whereIn('SOI.fk_store_id', $userId);
        if ($storePaymentId != NULL) {
            $releasedStore = $releasedStore->join('store_payment AS SP', 'SP.fk_order_id', '=', 'SOI.fk_sales_order_id')
                    ->where('SP.id', '=', $storePaymentId);
        } 
        $releasedStore = $releasedStore->groupBy(DB::raw('SOI.fk_sales_order_id, SOI.fk_product_id'))
                ->get();
        return $releasedStore;
    }

    /**
     * Return Card if for an order else current user card id.
     * 
     * @package SalesOrder Model
     * @param int $orderId
     * @return mixed Boolean , Int
     */
    public function getOrderCardId($orderId) {
        $cardId = DB::table('transaction_details AS TD')
                ->addSelect('TD.cardId')
                ->where('TD.fk_order_id', '=', $orderId)
                ->first();
        if (!empty($cardId)) {
            return $cardId->cardId;
        }
        $cardUser = DB::table('mangopay_card_details')
                ->addSelect('mango_users_card_id')
                ->where('fk_users_id', '=', Auth::user()['id'])
                ->first();
        if (!empty($cardUser)) {
            return $cardUser->mango_users_card_id;
        } return FALSE;
    }

    /**
     * Get Order Complete details for Admin
     * NearBy Stores & Collected product details
     *
     * @param string $orderNumber
     * @return array $orderDetails Order Item Details
     */
    public function getOrderDetailsForAdmin($orderNumber) {
        $orderData = DB::table('sales_order AS so')
                ->select('so.id_sales_order', 'so.order_id AS orderId', 'so.total as totalPrice'
                        , 'so.rider_charges', 'so.estimated_delivery_time', 'users.first_name', 'users.last_name', 'users.email'
                        , 'soa.phone', 'soa.address', 'soa.city', 'soa.state'
                        , 'soa.pin', 'order_status.name AS orderStatus', 'sales_order_item.fk_product_id', 'sales_order_item.bundle_id'
                        , 'so.fk_order_status_id'
                        , 'products.name as productName', 'sales_order_item.price as productPrice'
                        , 'bundles.name as bundleName '
                        , 'sales_order_item.fk_driver_id', 'sales_order_item.fk_store_id')
                ->addSelect('products.description as description')
                ->addSelect('bundles.serves as serves')
                ->addSelect('valid_postcodes.lat', 'valid_postcodes.lng', 'valid_postcodes.postcode')
                ->addSelect('sales_order_item.updated_at')
                ->join('users', 'users.id', '=', 'so.fk_users_id')
                ->join('sales_order_address AS soa', 'soa.id_sales_order_address', '=', 'so.fk_sales_address_id')
                ->join('order_status', 'order_status.id_order_status', '=', 'so.fk_order_status_id')
                ->join('sales_order_item', 'sales_order_item.fk_sales_order_id', '=', 'so.id_sales_order')
                // @todo can be optimize
                ->leftJoin('bundles', 'sales_order_item.bundle_id', '=', 'bundles.id')
                ->join('valid_postcodes', 'valid_postcodes.postcode', '=', 'soa.pin')
                ->join('products', 'sales_order_item.fk_product_id', '=', 'products.id')
                ->join('users as user_store', 'user_store.id', '=', 'sales_order_item.fk_store_id')
                ->addSelect('store_address.address as store_address', 'store_address.city as store_city', 'store_address.state as store_state', 'store_address.pin as store_pin')
                ->addSelect('sub_store_details.store_name')
                ->join('store_address', 'store_address.fk_users_id', '=', 'sales_order_item.fk_store_id')
                ->join('sub_store_details', 'sub_store_details.fk_users_id', '=', 'sales_order_item.fk_store_id');
        $orderData = $orderData->where('so.order_id', '=', $orderNumber);
        $orderData = $orderData->get();
        $orderDetails = array();
        $aProductId = array();
        $aCollectedProductId = array();
        if (!empty($orderData)) {
            $orderDefault = \Config::get('appConstants.available_order_condition');
            $orderNumber = $orderData[0]->orderId;
            $orderStatusId = $orderData[0]->fk_order_status_id;
            $orderAddress = $orderData[0]->address . ", "
                    . $orderData[0]->city . ", " . $orderData[0]->state . ", " . $orderData[0]->pin;
            $orderTotal = $orderData[0]->totalPrice;
            $driverCharges = $orderData[0]->rider_charges;
            $estimatedDeliveryTime = $orderData[0]->estimated_delivery_time;
            $orderId = $orderData[0]->id_sales_order;
            $customerName = $orderData[0]->first_name . " " . $orderData[0]->last_name;
            $postCode = $orderData[0]->postcode;
            $lat = $orderData[0]->lat;
            $lng = $orderData[0]->lng;
            $orderDetails = array(
                'orderId' => $orderId,
                'orderNumber' => $orderNumber,
                'orderStatusId' => $orderStatusId,
                'orderAddress' => $orderAddress,
                'orderTotal' => $orderTotal,
                'driverCharges' => $driverCharges,
                'estimatedDeliveryTime' => $estimatedDeliveryTime,
                'customer_name' => $customerName,
                'products' => array(),
                'bundle' => array(),
                'stores_available' => array(),
                'collected_products_details' => array(),
            );
            foreach ($orderData as $item) {
                $storeName = $item->store_name;
                $storeAddress = $item->store_address . "," . $item->store_city . ", " . $item->store_state. ", ". $item->store_pin;
                $bundleId = $item->bundle_id;
                $productId = $item->fk_product_id;
                $productPrice = $item->productPrice;
                $aProductId[$productId] = $productId;
                $itemStoreId = $item->fk_store_id;
                $itemDriverId = $item->fk_driver_id;
                if ($itemStoreId != $orderDefault && $itemDriverId != $orderDefault) {
                    $uniqueKey = $bundleId . "_" . $productId . "_" . $itemStoreId . "_" . $itemDriverId;
                    if (!isset($aCollectedProductId[$uniqueKey])) {
                        $aCollectedProductId[$uniqueKey] = array(
                            'name' => $item->productName,
                            'count' => 1,
                            'collected_at' => $item->updated_at,
                        );
                    } else {
                        $aCollectedProductId[$uniqueKey]['count'] ++;
                    }
                }
                if ($bundleId == 0) {
                    $aProduct = array(
                        'name' => $item->productName,
                        'price' => $item->productPrice,
                        'id' => $item->fk_product_id,
                        'count' => 1,
                        'description' => $item->description,
                        'store_name' => $storeName,
                        'store_address' => $storeAddress,
                    );
                    if (!isset($orderDetails['products'][$productId])) {
                        $orderDetails['products'][$productId] = $aProduct;
                    } else {
                        $orderDetails['products'][$productId]['count'] ++;
                        $orderDetails['products'][$productId]['price'] = $productPrice * $orderDetails['products'][$productId]['count'];
                    }
                } else {
                    $aBundle = array(
                        'name' => $item->bundleName,
                        'id' => $bundleId,
                        'price' => $item->productPrice,
                        'serves' => $item->serves,
                    );
                    $aProduct = array(
                        'name' => $item->productName,
                        'price' => $item->productPrice,
                        'id' => $item->fk_product_id,
                        'count' => 1,
                        'description' => $item->description,
                        'store_name' => $storeName,
                        'store_address' => $storeAddress,
                    );
                    $aBundle['product'][$productId] = $aProduct;
                    if (!isset($orderDetails['bundle'][$bundleId])) {
                        $orderDetails['bundle'][$bundleId] = $aBundle;
                    } else {
                        $orderDetails['bundle'][$bundleId]['price'] = $orderDetails['bundle'][$bundleId]['price'] + $productPrice;
                        if (!isset($orderDetails['bundle'][$bundleId]['product'][$productId])) {
                            $orderDetails['bundle'][$bundleId]['product'][$productId] = $aProduct;
                        } else {
                            $orderDetails['bundle'][$bundleId]['product'][$productId]['count'] ++;
                        }
                    }
                }
            }
            $collectedProductDetails = $this->getCollectedProductDetails($aCollectedProductId);
            $orderDetails['collected_products_details'] = $collectedProductDetails;
        }
        return $orderDetails;
    }

    /**
     * Get Near By Stores for Suggestion on Checkout page
     *
     * @param array $aProductId Product Ids of order
     * @param string $postCode Delivery Postcode of order
     * @param decimal $lat Latitude of Postcode
     * @param decimal $lng Longitude of Postcode
     * @return array $nearStoreMatchingProducts Store Details
     *
     */
    public function getNearByStoresForProductsCheckout($aProductId, $postCode, $lat, $lng) {
        $nearStoreMatchingProducts = array();
        try {
            $aNearByStores = CommonHelper::getNearByStoresForOrder($postCode, $lat, $lng, $storeDetails = true);
            if (empty($aNearByStores)) {
                return $nearStoreMatchingProducts;
            }
            $aNearByStoresId = array_keys($aNearByStores);
            $products = DB::table('products_store')
                    ->select('products_store.fk_product_id', 'products_store.vendor_price', 'products_store.fk_user_id')
                    ->addSelect('store_address.*')
                    ->addSelect('sub_store_details.store_name')
                    ->addSelect(DB::raw('IF(PM.psc = 0.00,'. CommonHelper::getGlobalGPSC() . ',PM.psc) as productCommission'))
                    ->addSelect(DB::raw('IF(PM.vpc = 0.00,'. CommonHelper::getGlobalGVPC() . ',PM.vpc) as vendorCommission'))
                    ->addSelect('users.email as store_email', 'users.phone as store_phone')
                    ->join('products', 'products.id', '=', 'products_store.fk_product_id')
                    ->join('store_address', 'store_address.fk_users_id', '=', 'products_store.fk_user_id')
                    ->join('sub_store_details', 'sub_store_details.fk_users_id', '=', 'products_store.fk_user_id')
                    ->join('products_meta AS PM', 'PM.fk_product_id', '=', 'products.id')
                    ->join('users', 'users.id', '=', 'products_store.fk_user_id')
                    ->whereIN('products_store.fk_user_id', $aNearByStoresId)
                    ->whereIN('products_store.fk_product_id', $aProductId)
                    ->get();
            foreach ($products as $product) {
                $storeId = $product->fk_user_id;
                $aProduct = array(
                    'id'    => $product->fk_product_id,
                    'price' => $product->vendor_price,
                    'productCommission' => $product->productCommission,
                    'vendorCommission'  => $product->vendorCommission,
                );
                if (!isset($nearStoreMatchingProducts[$storeId])) {
                    $storeAddress = $product->address . "," . $product->city . "," . $product->state . "," . $product->pin;
                    $storeDetails = array(
                        'id'        => $storeId,
                        'distance'  => $aNearByStores[$storeId]['distance'],
                        'name'      => $product->store_name,
                        'address'   => $storeAddress,
                        'postcode'  => $product->pin,
                        'store_email'  => $product->store_email,
                        'store_phone'  => $product->store_phone,
//                        'productCommission' => $product->productCommission,
//                        'vendorCommission'  => $product->vendorCommission,
                    );
                    $nearStoreMatchingProducts[$storeId] = array();
                    $nearStoreMatchingProducts[$storeId] = $storeDetails;
                }
                $nearStoreMatchingProducts[$storeId]['products'][$product->fk_product_id] = $aProduct;
            }
        } catch (Exception $ex) {
//            echo $ex->getMessage();
        }
        return $nearStoreMatchingProducts;
    }

    /**
     * Get Near By Stores for Suggestion in Admin
     *
     * @param array $aCollectedProductId Product Ids of items in order (In specific format)
     * @return array $collectedProductsDetails Collected Product Details like store name, driver details
     *
     */
    public function getCollectedProductDetails($aCollectedProductId) {
        $collectedProductsDetails = array();
        try {
            if (empty($aCollectedProductId)) {
                return $collectedProductsDetails;
            }
            $aProductId = $aBundleId = $aStoreId = $aDriverId = array();
            foreach ($aCollectedProductId as $key => $productAttr) {
                $aProductAttr = explode("_", $key);
                $bundleId = $aProductAttr[0];
                $productId = $aProductAttr[1];
                $storeId = $aProductAttr[2];
                $driverId = $aProductAttr[3];
                $aBundleId[$bundleId] = $bundleId;
                $aProductId[$productId] = $productId;
                $aStoreId[$storeId] = $storeId;
                $aDriverId[$driverId] = $driverId;
            }
            $driverDetails = DB::table('users')
                    ->select('first_name', 'last_name', 'email', 'id as driverId')
                    ->whereIN('id', $aDriverId)
                    ->get();

            $storeDetails = DB::table('sub_store_details')
                    ->select('store_name', 'fk_users_id as storeId')
                    ->whereIN('fk_users_id', $aStoreId)
                    ->get();
            $aStoreDetails = $aDriverDetails = array();
            foreach ($storeDetails as $storeDetail) {
                $storeId = $storeDetail->storeId;
                $storeName = $storeDetail->store_name;
                $aStoreDetails[$storeId] = array(
                    'name' => $storeName,
                );
            }
            foreach ($driverDetails as $driverDetail) {
                $driverId = $driverDetail->driverId;
                $driverEmail = $driverDetail->email;
                $aDriverDetails[$driverId] = array(
                    'email' => $driverEmail,
                );
            }
            foreach ($aCollectedProductId as $key => $productAttr) {
                $aProductAttr = explode("_", $key);
                $storeId = $aProductAttr[2];
                $driverId = $aProductAttr[3];
                $productAttr['store_name'] = $aStoreDetails[$storeId]['name'];
                $productAttr['driver_email'] = $aDriverDetails[$driverId]['email'];
                $collectedProductsDetails[$key] = $productAttr;
            }
        } catch (Exception $ex) {
            //@todo
        }
        return $collectedProductsDetails;
    }

    /**
     * Method to send order eMail
     *
     * @param array $cartViewData
     * @param string $orderNumber
     * @param array $aOrderDetails
     * @return null
     */
    public function sendOrderEmail($cartViewData, $orderNumber, $aOrderDetails, $cartRawContent = array()) {
        try {
            $cartItem           = [];
            $deliveryCharges    = isset($aOrderDetails['charges']) ? $aOrderDetails['charges']: array();
            $couponApplied      = isset($aOrderDetails['appliedcoupon']) ? $aOrderDetails['appliedcoupon']: [];
            $currencySymbol     = config('appConstants.currency_sign');
            $cartTotal          = isset($aOrderDetails['cart_total']) ? $aOrderDetails['cart_total'] : "0";
            $firstName          = isset($aOrderDetails['first_name']) ? $aOrderDetails['first_name'] : "";
            $lastName           = isset($aOrderDetails['last_name']) ? $aOrderDetails['last_name'] : "";
            //$availableStore     = isset($aOrderDetails['available_store']) ? $aOrderDetails['available_store'] : array();
            //$name = $firstName . ',' . $lastName;
            $email = isset($aOrderDetails['email']) ? $aOrderDetails['email'] : "";
            if ($email == 'customer@customer.com') {
                $email = env('MANGO_LEGAL_EMAIL', '');
            }
            foreach ($cartViewData as $key => $value) {
                $cartItem[] = $value;
            }
            if(empty($couponApplied)){
                $couponApplied = (object) [];
            }
            //$image = url('alchemy/images'). '/logo.png';
            /*$data = [
                    'name' => $name,
                    'ordernumber' => $orderNumber,
                    'logoimage' => url('alchemy/images'). '/logo.png',
                    'cartitem' => $cartViewData,
                    'deliverycharge' => $deliveryCharges,
                    'carttotal' => $cartTotal,
                    'availablestore' => $availableStore,
                    'currency' => $currencySymbol,
                    'coupon' => $couponApplied,
                    'tlink' =>config('appConstants.twitter'),
                    'timage' => url('alchemy/images'). '/twitter.png',
                'flink' =>config('appConstants.facebook'),
                    'fimage' => url('alchemy/images'). '/facebook.png',
                'ilink' =>config('appConstants.instagram'),
                    'iimage' => url('alchemy/images'). '/instagram.png',
                'plink' =>config('appConstants.pinterest'),
                    'pimage' => url('alchemy/images'). '/pinterest.png',
                'mlink' =>config('appConstants.mailto'),
                    'mimage' => url('alchemy/images'). '/email.png',
                ];*/
            $data = [
                'firstName'     => $firstName,
                'lastName'      => $lastName,
                'email'         => $email,
                'orderNumber'   => $orderNumber,
                'cartItem'      => $cartItem,
                'driverCharge'  => $deliveryCharges,
                'cartTotal'     => $cartTotal,
                'currency'      => $currencySymbol,
                'coupon'        => $couponApplied,
                'AddressLine1'  => $aOrderDetails['AddressLine1'],
                'Town'          => $aOrderDetails['Town'],
                'Postcode'      => $aOrderDetails['Postcode'],
                'Country'       => $aOrderDetails['Country'],
                'OrderDate'     => $aOrderDetails['OrderDate'],
                'OrderTime'     => $aOrderDetails['OrderTime'],
            ];

            //$mergeVars = array(array('name' => 'data', 'content' => $data));
            //Email::sendEmail($email, $mergeVars, 'Order Confirmation', env('MAIL_BCC_ADDRESS'));
            EmailForce24::sendEmailAPI($data, EmailForce24::ORDER_CONFIRMATION);
            SlackService::sendSlackNotification();
            $this->sendDataToTookan($cartRawContent, $orderNumber, $aOrderDetails);
        } catch (Exception $ex) {
            $errMsg = trans('messages.email_order_exception') . $ex->getMessage() . "|" . $ex->getFile() . "|". $ex->getLine();
            CommonHelper::event($errMsg, CommonHelper::EMAIL_FAILURE_LOG_FILE, CommonHelper::DAILY);
        }
    }

    /**
     * Method to log user coupon usage.
     *
     * @param int $couponId
     * @param int $userId
     */
    protected function _logCouponUsage($couponId, $userId) {
        $couponModel = new Coupon();
        $couponModel->logUserCouponUsage($couponId, $userId);
    }

    /**
     * Protected Method for storing data in sales_order table
     *
     * @package SalesOrder Model
     * @param decimal $cartTotal Total Price
     * @param int $addressId Adress id
     * @return int $orderId Primary key of newly inserted order
     */
    protected function _saveOrderAdditionalData($orderId) {
        $midnightCharges    = $this->cartModel->getMidnightDeliveryCharges();
        $specialCharges     = $this->cartModel->getSpecialDeliveryCharges();
        $minBasketCharges     = $this->cartModel->getMinBasketCharges();
        $insertData = array(
            'fk_sales_order_id'         => $orderId,
            'after_midnight_charge'     => $midnightCharges,
            'special_category_charge'   => $specialCharges,
            'min_basket_charge'         => $minBasketCharges,
            'created_at' => CommonHelper::getCurrentDateTime(),
            'updated_at' => CommonHelper::getCurrentDateTime()
        );
        DB::table('sales_order_additional_info')->insert($insertData);
    }

    /**
     * 
     * @return string $generatedOrderNumber
     */
    protected function _generateOrderNumber() {
        $generatedOrderNumber = substr(str_shuffle(str_repeat("0123456789ABCdDEFGHIJKLMNOPQRSTUVWXYZ", 5)), 0, 5);
        $isDuplicateOrderNumber = $this->_checkOrderNumberInDb($generatedOrderNumber);
        if ($isDuplicateOrderNumber) {
            $this->_generateOrderNumber();
        }
        return $generatedOrderNumber;
    }

    /**
     * 
     * @param string $orderNumber
     * @return boolean
     */
    protected function _checkOrderNumberInDb($orderNumber) {
        $data = DB::table('sales_order AS so')
                ->select('so.id_sales_order')
                ->where('so.order_id', '=', $orderNumber)
                ->first();
        if (!empty($data)) {
            return true;
        }
        return false;
    }

    /**
     * Method to get transaction details of order
     * 
     * @param int $orderId
     */
    public function getOrderTransactionDetails($orderId) {
        $orderData = DB::table('sales_order AS so')
                ->select('so.total')
                ->addSelect('transaction_details.userMId as authorId','transaction_details.payInDAtanId as payInId')
                ->join('transaction_details', 'transaction_details.fk_order_id', '=', 'so.id_sales_order')
                ->where('so.id_sales_order', '=', $orderId)
                ->first();
        return $orderData;
    }

    /**
     * Method to get order details from transaction id
     * 
     * @param int $transactionId
     */
    public function getOrderIdFromTransacationId($transactionId) {
        $orderData = DB::table('sales_order AS so')
                ->select('so.total', 'so.id_sales_order')
                ->addSelect('transaction_details.userMId as authorId','transaction_details.payInDAtanId as payInId')
                ->join('transaction_details', 'transaction_details.fk_order_id', '=', 'so.id_sales_order')
                ->where('transaction_details.payInDAtanId', '=', $transactionId)
                ->first();
        return $orderData;
    }
    
    /**
     * Return order data by orderid.
     * 
     * @param Int $orderId
     * @return object
     */
    public function getSalesOrderDetailsById($orderId) {
        $orderData = DB::table('sales_order_item AS soi')
                ->select('soi.*')
                ->where('soi.fk_sales_order_id', '=', $orderId)
                ->get();
        return $orderData;
    }

    /**
     * Method to prepare and send data to Tookan API
     *
     * @param type $cartViewData
     * @param type $orderNumber
     * @param type $aOrderDetails
     */
    public function sendDataToTookan($cartViewData, $orderNumber, $aOrderDetails) {
        $orderId        = $orderNumber;
        $jobDescription = "alchemy_order_delivery_".$orderId;
        $customerPhone  = isset($aOrderDetails['phone']) ? $aOrderDetails['phone'] : '0';
        $customerEmail  = $aOrderDetails['email'];
        $firstName      = $aOrderDetails['first_name'];
        $lastName       = $aOrderDetails['last_name'];
        $customerAddress    = $aOrderDetails['AddressLine1'] . "," . $aOrderDetails['Town'] . "," . $aOrderDetails['Postcode'] . "," . $aOrderDetails['Country'];
        $customerUserName   = $firstName . " " . $lastName;
        $orderDateTime      = date('Y-m-d H:i:s');
        $pickUPTime         = 15;
        $deliverTime        = 45;
        $pickupDateTime     = date("Y-m-d H:i:s", strtotime('+' . $pickUPTime . ' minutes', strtotime($orderDateTime)));
        $deliveryDateTime   = date("Y-m-d H:i:s", strtotime('+' . $deliverTime . ' minutes', strtotime($orderDateTime)));
        $aTemplateData      = [];
        $aStoreProductData  = [];
        foreach ($cartViewData as $cartData) {
            $aStoreProductData[$cartData['store_id']][] = $cartData;
        }
        // Get Current Task Type as per Tookan Availability set in Backend.
        $availabilityType = PartnerAvailability::getTookanAvailabilityType();
        if (count($aStoreProductData) == 1) {
            // Single Store Case
            $cartViewTempDataKeys   = array_keys($aStoreProductData);
            $cartViewTempData       = $aStoreProductData[$cartViewTempDataKeys[0]];
            $aTemplateData          = [];
            foreach ($cartViewTempData as $cartData) {
                $templateData = [
                    $cartData['options'][0]['alcohol_content'],
                    $cartData['qty'],
                ];
                $aTemplateData[] = $templateData;
            }
            $aPickupTemplateData    = [["label" => "PRODUCTS", "data" => $aTemplateData], ["label" => "order_number", "data" => $orderId]];
            $pickupAddress          = $cartData['store_address'];
            $pickupName             = $cartData['store_name'];
            $pickupPhone            = (!empty($cartData['store_phone'])) ? $cartData['store_phone'] : 0;
            $aTookanTemplateData = array(
                'order_id'              => $orderId,
                'job_description'       => $jobDescription,
                'job_pickup_phone'      => $pickupPhone,
                'job_pickup_name'       => $pickupName,
                'job_pickup_address'    => $pickupAddress,
                'job_pickup_datetime'   => $pickupDateTime,
                'customer_email'        => $customerEmail,
                'customer_username'     => $customerUserName,
                'customer_phone'        => $customerPhone,
                'customer_address'      => $customerAddress,
                'job_delivery_datetime' => $deliveryDateTime,
                'pickup_meta_data'      => $aPickupTemplateData,
            );
            $aTookanData    = $this->prepareTookanData($aTookanTemplateData, $pickupTemplateName = $availabilityType, $onlyPickup = false);
            $tookanResponse = Http\Helper\DeliveryAPIService::createTask($aTookanData, $availabilityType);
            self::logTrackingDetails($orderId, $tookanResponse);
        } else {
            if ($availabilityType == PartnerAvailability::TASK_INTERNAL) {
                // Internal task Mutiple Store Case
                $jobDescription = $jobDescription."_Two_part_task";
                $cartViewTempDataKeys   = array_keys($aStoreProductData);
                $cartViewTempData       = $aStoreProductData[$cartViewTempDataKeys[0]];
                $aTemplateData          = [];
                foreach ($cartViewTempData as $cartData) {
                    $templateData = [
                        $cartData['options'][0]['alcohol_content'],
                        $cartData['qty'],
                    ];
                    $aTemplateData[] = $templateData;
                }
                $aPickupTemplateData    = [["label" => "PRODUCTS", "data" => $aTemplateData], ["label" => "order_number", "data" => $orderId]];
                $pickupAddress          = $cartData['store_address'];
                $pickupName             = $cartData['store_name'];
                $pickupPhone            = (!empty($cartData['store_phone'])) ? $cartData['store_phone'] : 0;
                $pickUPTime             = 30;
                $pickupDateTime         = date("Y-m-d H:i:s", strtotime('+' . $pickUPTime . ' minutes', strtotime($orderDateTime)));
                $deliverTime            = 60;
                $deliveryDateTime       = date("Y-m-d H:i:s", strtotime('+' . $deliverTime . ' minutes', strtotime($orderDateTime)));
                $aTookanTemplateData = array(
                    'order_id'              => $orderId,
                    'job_description'       => $jobDescription,
                    'job_pickup_phone'      => $pickupPhone,
                    'job_pickup_name'       => $pickupName,
                    'job_pickup_address'    => $pickupAddress,
                    'job_pickup_datetime'   => $pickupDateTime,
                    'customer_email'        => $customerEmail,
                    'customer_username'     => $customerUserName,
                    'customer_phone'        => $customerPhone,
                    'customer_address'      => $customerAddress,
                    'job_delivery_datetime' => $deliveryDateTime,
                    'pickup_meta_data'      => $aPickupTemplateData,
                );
                $aTookanData    = $this->prepareTookanData($aTookanTemplateData, $pickupTemplateName = PartnerAvailability::TASK_INTERNAL, $onlyPickup = false);
                $tookanResponse = Http\Helper\DeliveryAPIService::createTask($aTookanData, $availabilityType);
                self::logTrackingDetails($orderId, $tookanResponse);
                // Second store Create PickUp Only
                $cartViewTempData = $aStoreProductData[$cartViewTempDataKeys[1]];
                $aTemplateData          = [];
                foreach ($cartViewTempData as $cartData) {
                    $templateData = [
                        $cartData['options'][0]['alcohol_content'],
                        $cartData['qty'],
                    ];
                    $aTemplateData[] = $templateData;
                }
                $aPickupTemplateData    = [["label" => "PRODUCTS", "data" => $aTemplateData], ["label" => "order_number", "data" => $orderId]];
                $pickupAddress          = $cartData['store_address'];
                $pickupName             = $cartData['store_name'];
                $pickupPhone            = (!empty($cartData['store_phone'])) ? $cartData['store_phone'] : 0;
                $pickUPTime             = 15;
                $pickupDateTime         = date("Y-m-d H:i:s", strtotime('+' . $pickUPTime . ' minutes', strtotime($orderDateTime)));
                $aTookanTemplateData = array(
                    'order_id'              => $orderId,
                    'job_description'       => $jobDescription,
                    'job_pickup_phone'      => $pickupPhone,
                    'job_pickup_name'       => $pickupName,
                    'job_pickup_address'    => $pickupAddress,
                    'job_pickup_datetime'   => $pickupDateTime,
                    'pickup_meta_data'      => $aPickupTemplateData,
                );
                $aTookanData    = $this->prepareTookanData($aTookanTemplateData, $pickupTemplateName = PartnerAvailability::TASK_INTERNAL, $onlyPickup = true);
                $tookanResponse = Http\Helper\DeliveryAPIService::createTask($aTookanData, $availabilityType);
                self::logTrackingDetails($orderId, $tookanResponse);
            } else {
                // Partner task Mutiple Store Case
                $jobDescription = $jobDescription."_Two_part_task";
                $cartViewTempDataKeys = array_keys($aStoreProductData);
                $cartViewTempData   = $aStoreProductData[$cartViewTempDataKeys[0]];
                $cartViewTempData2  = $aStoreProductData[$cartViewTempDataKeys[1]];
                $storeDistance1     = $cartViewTempData[0]['store_distance'];
                $storeDistance2     = $cartViewTempData2[0]['store_distance'];
                if ($storeDistance1 >= $storeDistance2) {
                    $aStoreNear = $cartViewTempData2;
                    $aStoreFar  = $cartViewTempData;
                } else {
                    $aStoreNear = $cartViewTempData;
                    $aStoreFar  = $cartViewTempData2;
                }
                $aTemplateData          = [];
                foreach ($aStoreFar as $cartData) {
                    $templateData = [
                        $cartData['options'][0]['alcohol_content'],
                        $cartData['qty'],
                    ];
                    $aTemplateData[] = $templateData;
                }
                $aPickupTemplateData    = [["label" => "PRODUCTS", "data" => $aTemplateData], ["label" => "order_number", "data" => $orderId]];
                $pickupAddress          = $cartData['store_address'];
                $pickupName             = $cartData['store_name'];
                $pickupPhone            = (!empty($cartData['store_phone'])) ? $cartData['store_phone'] : 0;
                $customerEmailStore          = $aStoreNear[0]['store_email'];
                $customerUserNameStore       = $aStoreNear[0]['store_name'];
                $customerPhoneStore          = $aStoreNear[0]['store_phone'];
                $customerAddressStore        = $aStoreNear[0]['store_address'];
                $pickUPTime                 = 15;
                $pickupDateTime             = date("Y-m-d H:i:s", strtotime('+' . $pickUPTime . ' minutes', strtotime($orderDateTime)));
                $deliverTime                = 30;
                $deliveryDateTime           = date("Y-m-d H:i:s", strtotime('+' . $deliverTime . ' minutes', strtotime($orderDateTime)));
                $aTookanTemplateData = array(
                    'order_id'              => $orderId,
                    'job_description'       => $jobDescription,
                    'job_pickup_phone'      => $pickupPhone,
                    'job_pickup_name'       => $pickupName,
                    'job_pickup_address'    => $pickupAddress,
                    'job_pickup_datetime'   => $pickupDateTime,
                    'customer_email'        => $customerEmailStore,
                    'customer_username'     => $customerUserNameStore,
                    'customer_phone'        => $customerPhoneStore,
                    'customer_address'      => $customerAddressStore,
                    'job_delivery_datetime' => $deliveryDateTime,
                    'pickup_meta_data'      => $aPickupTemplateData,
                );
                $aTookanData    = $this->prepareTookanData($aTookanTemplateData, $pickupTemplateName = PartnerAvailability::TASK_PARTNER, $onlyPickup = false);
                $tookanResponse = Http\Helper\DeliveryAPIService::createTask($aTookanData, $availabilityType);
                self::logTrackingDetails($orderId, $tookanResponse);
                // near store PickUp Customer Delivery Only
                $aTemplateData          = [];
                foreach ($aStoreNear as $cartData) {
                    $templateData = [
                        $cartData['options'][0]['alcohol_content'],
                        $cartData['qty'],
                    ];
                    $aTemplateData[] = $templateData;
                }
                $aPickupTemplateData    = [["label" => "PRODUCTS", "data" => $aTemplateData], ["label" => "order_number", "data" => $orderId]];
                $pickupAddress          = $cartData['store_address'];
                $pickupName             = $cartData['store_name'];
                $pickupPhone            = (!empty($cartData['store_phone'])) ? $cartData['store_phone'] : 0;
                $pickUPTime                 = 30;
                $pickupDateTime             = date("Y-m-d H:i:s", strtotime('+' . $pickUPTime . ' minutes', strtotime($orderDateTime)));
                $deliverTime                = 60;
                $deliveryDateTime           = date("Y-m-d H:i:s", strtotime('+' . $deliverTime . ' minutes', strtotime($orderDateTime)));
                $aTookanTemplateData = array(
                    'order_id'              => $orderId,
                    'job_description'       => $jobDescription,
                    'job_pickup_phone'      => $pickupPhone,
                    'job_pickup_name'       => $pickupName,
                    'job_pickup_address'    => $pickupAddress,
                    'job_pickup_datetime'   => $pickupDateTime,
                    'customer_email'        => $customerEmail,
                    'customer_username'     => $customerUserName,
                    'customer_phone'        => $customerPhone,
                    'customer_address'      => $customerAddress,
                    'job_delivery_datetime' => $deliveryDateTime,
                    'pickup_meta_data'      => $aPickupTemplateData,
                );
                $aTookanData    = $this->prepareTookanData($aTookanTemplateData, $pickupTemplateName = PartnerAvailability::TASK_PARTNER, $onlyPickup = false);
                $tookanResponse = Http\Helper\DeliveryAPIService::createTask($aTookanData, $availabilityType);
                self::logTrackingDetails($orderId, $tookanResponse);
            }
        } // Mutiple store ends
        return;
    }

    /**
     * Method to log tracking details of order
     *
     * @param string $orderId
     * @param array $tookanResponse
     * @return void
     */
    public function logTrackingDetails($orderId, $tookanResponse) {
        $jobId          = isset($tookanResponse['data']['data']['job_id']) ? $tookanResponse['data']['data']['job_id'] : 0;
        $insertData = array(
            'order_id'      => $orderId,
            'job_id'        => $jobId,
            'raw_data'      => json_encode($tookanResponse),
            'created_at'    => CommonHelper::getCurrentDateTime(),
            'updated_at'    => CommonHelper::getCurrentDateTime()
        );
        DB::table('sales_order_tracking')->insert($insertData);
    }

    /**
     * 
     * @param array $aParam
     * @param string $taskType
     * @param boolean $onlyPickup
     * @return array $aTookanData
     */
    public function prepareTookanData($aParam, $taskType, $onlyPickup) {
        $aTookanData = [
                    "order_id"          => isset($aParam["order_id"]) ? $aParam["order_id"] : "",
                    "auto_assignment"   => "1",
                    "job_description"   => isset($aParam["job_description"]) ? $aParam["job_description"] : "",
                    "job_pickup_phone"  => isset($aParam["job_pickup_phone"]) ? $aParam["job_pickup_phone"] : "",
                    "job_pickup_name"   => isset($aParam["job_pickup_name"]) ? $aParam["job_pickup_name"] : "", // optional
                    "job_pickup_email"  => "", // optional
                    "job_pickup_address"    => isset($aParam["job_pickup_address"]) ? $aParam["job_pickup_address"] : "",
                    "job_pickup_latitude"   => "", // optional
                    "job_pickup_longitude"  => "", // optional
                    "job_pickup_datetime"   => isset($aParam["job_pickup_datetime"]) ? $aParam["job_pickup_datetime"] : "",
                    "has_pickup"            => "1",
                    "has_delivery"          => "0",
                    "layout_type"           => 0,
                    "tracking_link"         => "0",
                    "timezone"              => "0", // London Timezone difference in minutes
                    "custom_field_template" => "",
                    "meta_data"             => "",
                    //"pickup_custom_field_template"  => $pickupTemplateName,
                    "pickup_meta_data"              => isset($aParam["pickup_meta_data"]) ? $aParam["pickup_meta_data"] : "",
                    "fleet_id"      => "", // Blank as we are auto assigning.
                    "p_ref_images"  => [],
                    "ref_images"    => [],
                    //"notify"        => 1,
                    //"tags"          => "",
                    //"geofence"      => 0,
                ];
        if (!$onlyPickup) {
            $aTookanData["customer_email"]      = isset($aParam["customer_email"]) ? $aParam["customer_email"] : ""; // optional
            $aTookanData["customer_username"]   = isset($aParam["customer_username"]) ? $aParam["customer_username"] : ""; // optional
            $aTookanData["customer_phone"]      = isset($aParam["customer_phone"]) ? $aParam["customer_phone"] : "";
            $aTookanData["customer_address"]    = isset($aParam["customer_address"]) ? $aParam["customer_address"] : "";
            $aTookanData["latitude"]            =  ""; // optional
            $aTookanData["longitude"]           =  ""; // optional
            $aTookanData["job_delivery_datetime"] =  isset($aParam["job_delivery_datetime"]) ? $aParam["job_delivery_datetime"] : "";
            $aTookanData["has_delivery"]           =  "1";
        }
        if ($taskType == PartnerAvailability::TASK_PARTNER) {
            //$aTookanData["pickup_delivery_relationship"] =  0;
            $aTookanData["partner_id"]          =  1;
            //$aTookanData["tookan_partner_id"]   =  0;
        }
        return $aTookanData;
    }
}
