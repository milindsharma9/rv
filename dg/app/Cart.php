<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Collection;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Exception;
use App\Product;
use DB;
use App\Http\Helper\CommonHelper;
use App\SalesOrder;
use App\Coupon;

class Cart extends Collection {

    protected $cartCollection;
    
    protected $cartKey = 'cart_custom';
    
    protected $product = null;
    
    protected $bundleInfo = array();
    protected $bundleRefined = array();
    protected $bundleTotal = 0.00;
    
    protected $appliedCoupon    = array();
    protected $deliveryPostcode = array();
    protected $deliveryCharges  = array(
        'delivery_charge' => array(
            'label' => 'Driver Charge',
            'value' => 0.00,
        ),
        'after_midnight_charge' => array(
            'label' => 'After midnight',
            'value' => 0.00,
        ),
        'special_category_charge' => array(
            'label' => 'Special Product Charges',
            'value' => 0.00,
        ),
        'min_basket_charge' => array(
            'label' => 'Minimum Basket Charges',
            'value' => 0.00,
        )
    );
    
    protected $_deliveryDiscount = FALSE;

    protected $_storeProductDetails = null;

    /**
     *
     * @var mixed
     */
    private $userCartId = null;
    
    private $couponModel = null;
    
    private function getCouponModel() {
        if ($this->couponModel == null) {
            $this->couponModel = new Coupon();
        }
        return $this->couponModel;
    }
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct($userCartId)
    {
        if (empty($userCartId)) {
            throw new Exception('Invalid UserId');
        }
        $this->userCartId = $userCartId;
        //$cartData = session()->get($this->cartKey, new Collection);
        $cartDataResponse = $this->getUserCartData($userCartId);
        $cartData = $cartDataResponse['data'];
        $this->cartCollection = $cartData;
        $this->deliveryCharges = $cartDataResponse['delivery_charges'];
        //$user = (Auth::user()) ? Auth::user()->id : 0;
        //session()->put($this->cartKey."_user", $user);
        if ($this->product == null) {
            $this->product = new Product();
        }
        
    }
    
    public function add($id, $name = null, $qty = null, $price = null, array $options = []) {
        // If the first parameter is an array we need to call the add() function again
        if (is_array($id)) {
            // And if it's not only an array, but a multidimensional array, we need to
            // recursively call the add function
            if ($this->is_multi($id)) {
                // Fire the cart.batch event
                event('cart.batch', $id);
                foreach ($id as $item) {
                    $options = array_get($item, 'options', []);
                    $result = $this->addRow($item['id'], $item['name'], $item['qty'], $item['price'], $options);
                }
                // Fire the cart.batched event
                event('cart.batched', $id);
                return $result;
            }
            $options = array_get($id, 'options', []);
            // Fire the cart.add event
            event('cart.add', array_merge($id, ['options' => $options]));
            $result = $this->addRow($id['id'], $id['name'], $id['qty'], $id['price'], $options);
            // Fire the cart.added event
            event('cart.added', array_merge($id, ['options' => $options]));
            return $result;
        }
        // Fire the cart.add event
        event('cart.add', compact('id', 'name', 'qty', 'price', 'options'));
        $result = $this->addRow($id, $name, $qty, $price, $options);
        // Fire the cart.added event
        event('cart.added', compact('id', 'name', 'qty', 'price', 'options'));
        return $result;
    }
    
    public function setCartData() {
        $cartCount = 0;
        if ($this->cartCollection == null) {
            //session()->forget($this->cartKey);
            session()->forget($this->cartKey."_total");
            //session()->forget($this->cartKey."_user");
            $userCartSessionKey = \Config::get('appConstants.user_cart_unique_session_key');
            session()->forget($userCartSessionKey);
        } else {
            //session()->put($this->cartKey, $this->cartCollection);
            $cartCount = $this->count($totalItems = true);
            //session()->put($this->cartKey."_total", $cartCount);
        }
        $this->saveUserCartData($this->userCartId, $this->cartCollection, $cartCount);
    }

    /**
     * Get the CarCollection
     *
     * @return 
     */
    protected function getCartCollection() {
        return $this->cartCollection;
    }

    protected function addRow($id, $name, $qty, $price, array $options = []) {
        if (empty($id) || empty($name) || empty($qty) || !isset($price)) {
            throw new Exception('Invalid Item');
        }
        if (!is_numeric($qty)) {
            throw new Exception('InvalidQty');
        }

        if (!is_numeric($price)) {
            throw new Exception('InvalidPrice');
        }

        $cartCollection = $this->getCartCollection();

        //$rowId = $this->generateRowId($id, $options);
        //$rowId = $id;

        if ($options['bundleId'] != 0
                && empty($this->bundleInfo)) {
            $this->prepareBundleData($options['bundleId']);
            /*$this->bundleInfo = $this->product->getBundleDetailForCart($options['bundleId']);
            foreach ($this->bundleInfo as $bundleDetail) {
                $this->bundleTotal += $bundleDetail->priceTot;
                $this->bundleRefined[$bundleDetail->fk_product_id] = $bundleDetail;
            }*/
        }
        $rowId = $options['bundleId']."_".$id;
        //

        if ($cartCollection->has($rowId)) {
            $row = $cartCollection->get($rowId);
            $cartCollection = $this->updateRow($rowId, ['qty' => $row['qty'] + $qty]);
        } else {
            $cartCollection = $this->createRow($rowId, $id, $name, $qty, $price, $options);
        }
        return $this->setCartCollection($cartCollection);
    }
    
    /**
     * Create a new row Object
     *
     * @param  string  $rowId    The ID of the new row
     * @param  string  $id       Unique ID of the item
     * @param  string  $name     Name of the item
     * @param  int     $qty      Item qty to add to the cart
     * @param  float   $price    Price of one item
     * @param  array   $options  Array of additional options, such as 'size' or 'color'
     */
    protected function createRow($rowId, $id, $name, $qty, $price, $options) {
        $price          = $this->formatPrice($price);
        $cartCollection = $this->getCartCollection();
        // Set Delivery Postcode
        $deliveryPostCode = $this->deliveryPostcode;
        if (isset($options['delivery_postcode'])) {
            $deliveryPostCode = $options['delivery_postcode'];
            unset($options['delivery_postcode']);
        }
        $this->setDeliveryPostcode($deliveryPostCode);
        // For Bundle Products
        if ($options['bundleId'] != 0) {
            $newRow         = array(
                'rowid'     => $rowId,
                'id'        => $id,
                'name'      => $name,
                'qty'       => (int) $qty,
                'price'     => $this->bundleRefined[$id]->price,
                'options'   => array($options),
                'subtotal'  => $qty * $price,
                'prod_cat'  => $this->bundleRefined[$id]->prodCatId,
                'specialCategory' => $this->bundleRefined[$id]->specialCategory,
            );
            $newRow['bundleName']                   = $this->bundleRefined[$id]->bundleName;
            $newRow['bundleId']                     = $options['bundleId'];
            $newRow['bundleDefaultProductQuantity'] = $this->bundleRefined[$id]->product_quantity;
            $newRow['bundleDefaultTotalPrice']      = $this->formatPrice($this->bundleTotal);
        } else {
            // Simple Products. Fetch Price & Product Catgeory From DB.
            $aProdDetails   = $this->product->getProductDetailForCart($id);
            $price          = $aProdDetails->price;
            $newRow         = array(
                'rowid'     => $rowId,
                'id'        => $id,
                'name'      => $name,
                'qty'       => (int) $qty,
                'price'     => $price,
                'options'   => array($options),
                'subtotal'  => $qty * $price,
                'prod_cat'  => $aProdDetails->prodCatId,
                'specialCategory' => $aProdDetails->specialCategory,
            );
        }
        $cartCollection->put($rowId, $newRow);
        return $cartCollection;
    }
    
    protected function setCartCollection($cartCollection) {
        $this->cartCollection = $cartCollection;
        // save current changes
        $this->setCartData();
        return $this->getCartCollection();
    }
    
    /**
     * Update a row if the rowId already exists
     *
     * @param  string   $rowId  The ID of the row to update
     * @param  array  	$attributes    The quantity and price to add to the row
     */
    protected function updateRow($rowId, $attributes) {
        $cartCollection = $this->getCartCollection();
        $row = $cartCollection->get($rowId);
        foreach ($attributes as $key => $value) {
            if ($key == 'options') { //@todo
                //$options = $row->options->merge($value);
                //$row->put($key, $options);
                //$options = $row['options'];
                //$row[$key] = $options;
            } else {
                $row[$key] = $value;
            }
        }

        if (!is_null(array_keys($attributes, ['qty', 'price']))) {
            //$row->put('subtotal', $row['qty'] * $row['price']);
            $row['subtotal'] = $row['qty'] * $row['price'];
        }
        $cartCollection->put($rowId, $row);
        return $cartCollection;
    }
    
    /**
     * @param $rowId
     */
    public function remove($rowId) {
        if (!$this->hasRowId($rowId)) {
            throw new Exception('InvalidRowID');
        }
        $aBundleData    = explode("_", $rowId);
        $bundleId       = $aBundleData[0];
        $productId      = $aBundleData[1];
            
        $cartCollection = $this->getCartCollection();
        if ($bundleId == 0) {
            // Fire the cart.remove event
            event('cart.remove', $rowId);
            $cartCollection->forget($rowId);
            // Fire the cart.removed event
            event('cart.removed', $rowId);
        } else {
            $this->prepareBundleData($bundleId);
            foreach ($this->bundleRefined as $productId => $aProduct) {
                $rowId = $bundleId . "_" . $productId;
                if ($this->hasRowId($rowId)) {
                    event('cart.remove', $rowId);
                    $cartCollection->forget($rowId);
                    // Fire the cart.removed event
                    event('cart.removed', $rowId);
                }
            }
        }
        if (empty($this->count($totalItems = true))) {
            return $this->destroy();
        }
        return $this->setCartCollection($cartCollection);
    }
    
    /**
     * Empty the cart
     *
     * @return boolean
     */
    public function destroy() {
        // Fire the cart.destroy event
        event('cart.destroy');

        //$result = session()->put($this->instance, null);
        $result = $this->setCartCollection(null);
        
        // Fire the cart.destroyed event
        event('cart.destroyed');

        return $result;
    }
    
    /**
     * Get the cart content
     *
     */
    public function content() {
        $cartCollection = $this->getCartCollection();
        return (empty($cartCollection)) ? null : $cartCollection;
    }
    
    /**
     * Get the price subtotal, sum of all rows except transport
     *
     * @return float
     */
    public function subtotal($addDriverCharges = false, $applyDiscount = true) {
        $total = 0;
        $cartCollection = $this->getCartCollection();

        if (empty($cartCollection)) {
            return $total;
        }

        foreach ($cartCollection as $row) {
            $total += $row['subtotal'];
        }
        if ($applyDiscount) {
            if ($this->hasCouponCode()) {
                $total = $this->getAmountAfterDiscount($total);
            }
        }
        if ($addDriverCharges) {
            //$driverCharge = \Config::get('appConstants.driver_charge');
            //$total = $total += $driverCharge;
            $deliveryCharges = $this->getConsolidatedDeliveryCharges();
            $total += $deliveryCharges;
        }
        //$total = number_format((float)$total, 2, '.', '');
        $total = $this->formatPrice($total);
        return $total;
    }
    
    /**
     * Get the number of items in the cart
     *
     * @param  boolean  $totalItems  Get all the items (when false, will return the number of rows)
     * @return int
     */
    public function count($totalItems = true) {
        $cartCollection = $this->getCartCollection();

        if (!$totalItems) {
            return $cartCollection->count();
        }
        $count = 0;
        if ($cartCollection != null) {
            foreach ($cartCollection as $row) {
                //$count += $row->qty;
                $count += $row['qty'];
            }
        }
        return $count;
    }
    
    /**
     * Check if a rowid exists in the current cart instance
     *
     * @param  string  $rowId  Unique ID of the item
     * @return boolean
     */
    protected function hasRowId($rowId) {
        return $this->getCartCollection()->has($rowId);
    }

    /**
     * Check if the array is a multidimensional array
     *
     * @param  array   $array  The array to check
     * @return boolean
     */
    protected function is_multi(array $array) {
        return is_array(head($array));
    }
    
    /**
     * Update the quantity of one row of the cart
     *
     * @param  string         $rowId       The rowid of the item you want to update
     * @param  integer|array  $attribute   New quantity of the item|Array of attributes to update
     * @return boolean
     */
    public function update($rowId, $qty, $isBundle) {
        if (!$this->hasRowId($rowId)) {
            throw new Exception('InvalidRowID');
        }

        if ($isBundle == 0) {
            // Fire the cart.update event
            event('cart.update', $rowId);

            $result = $this->updateQty($rowId, $qty);

            // Fire the cart.updated event
            event('cart.updated', $rowId);
        } else {
            $result = $this->updateBundleQty($rowId, $qty);
        }
        $this->setCartData();

        return $result;
    }

    /**
     * Update an attribute of the row
     *
     * @param  string  $rowId       The ID of the row
     * @param  array   $attributes  An array of attributes to update
     */
    protected function updateAttribute($rowId, $attributes) {
        return $this->updateRow($rowId, $attributes);
    }

    /**
     * Update the quantity of a row
     *
     * @param  string  $rowId  The ID of the row
     * @param  int     $qty    The qty to add
     */
    protected function updateQty($rowId, $qty) {
        if ($qty <= 0) {
            return $this->remove($rowId);
        }
        return $this->updateRow($rowId, ['qty' => $qty]);
    }

    protected function updateBundleQty($rowId, $qty) {
        $cartCollection = $this->getCartCollection();
        $aBundleData = explode("_", $rowId);
        $bundleId = $aBundleData[0];
        $productId = $aBundleData[1];
        $this->prepareBundleData($bundleId);
        $aBundleProdId = array_keys($this->bundleRefined);
        foreach ($aBundleProdId as $prodId) {
            $rowId = $bundleId ."_" . $prodId;
            $row = $cartCollection->get($rowId);
            $row['qty'] = $row['bundleDefaultProductQuantity'] * $qty;
            $row['price']          = $this->formatPrice($row['price']);
            $row['subtotal'] = $row['qty'] * $row['price'];
            $cartCollection->put($rowId, $row);
        }
        return $cartCollection;
    }

    protected function prepareBundleData($bundleId) {
        $this->bundleInfo = $this->product->getBundleDetailForCart($bundleId);
        foreach ($this->bundleInfo as $bundleDetail) {
            $this->bundleTotal += $bundleDetail->priceTot;
            $this->bundleRefined[$bundleDetail->fk_product_id] = $bundleDetail;
        }
    }

    public function formatPrice($price) {
        return CommonHelper::formatPrice($price);
    }

    /**
     * Method to get Cart Data for User Key
     *
     * @param string $userCartId Unique Id of CurrentUser
     * @return array $cartResponse
     *
     */
    public function getUserCartData($userCartId) {
        $cartResponse = array(
            'existing' => false,
            'data' => new Collection(),
            'order_data' => array(),
            'delivery_charges' => $this->deliveryCharges,
        );
        $userCartDataDB = DB::table('user_cart_data')
            ->select('user_id', 'data', 'total_quantity')
            ->where('user_id', '=', $userCartId)
            ->first();
        if (!empty($userCartDataDB)) {
            $orderData = unserialize($userCartDataDB->data);
            $cartItems = isset($orderData['items']) ? $orderData['items'] : new Collection();
            if (empty($this->appliedCoupon)) {
                $this->appliedCoupon = isset($orderData['coupon']) ? $orderData['coupon'] : $this->appliedCoupon;
            }
            $deliveryCharges = isset($orderData['delivery_charges']) ? $orderData['delivery_charges'] : $this->deliveryCharges;
            $this->deliveryPostcode = isset($orderData['delivery_postcode']) ? $orderData['delivery_postcode'] : $this->deliveryPostcode;
            $cartResponse['data'] = $cartItems;
            $cartResponse['delivery_charges'] = $deliveryCharges;
            $cartResponse['existing'] = true;
            $cartResponse['order_data'] = $orderData;
        }
        return $cartResponse;
    }

    /**
     * Method to get Cart Data for User Key
     *
     * @param string $userCartId Unique Id of CurrentUser
     * @param object $cartData Collection object containing Cart Item Data
     * @param int $quantity Item Quantity In Cart
     * @return void
     *
     */
    public function saveUserCartData($userCartId, $cartData, $quantity = 0) {
        $orderObj = $this->_getCartOrderObject($cartData);
        $userCartDetails = $this->getUserCartData($userCartId);
        if (!$userCartDetails['existing']) {
            $insertData = array(
                'user_id'           => $userCartId,
                //'data'              => serialize($cartData),
                'data'              => serialize($orderObj),
                'total_quantity'    => $quantity,
                'created_at'        => CommonHelper::getCurrentDateTime(),
                'updated_at'        => CommonHelper::getCurrentDateTime(),
            );
            DB::table('user_cart_data')->insert($insertData);
        } else {
            if ($cartData == null) {
                DB::table('user_cart_data')->where('user_id', '=', $userCartId)->delete();
            } else {
                $updateData = array(
                    //'data'              => serialize($cartData),
                    'data'              => serialize($orderObj),
                    'total_quantity'    => $quantity,
                );
                $data = DB::table('user_cart_data')->where('user_id','=',$userCartId)
                    ->update($updateData);
            }
        }
    }

    /**
     * Method to delete Cart Data for User Key.
     * When user logs in, in same browser session, no need to keep data
     *
     * @param string $userCartId Unique Id of CurrentUser
     * @return void
     *
     */
    public function deleteUserCartData($userCartId) {
        DB::table('user_cart_data')->where('user_id','=',$userCartId)
                    ->delete();
    }

    /**
     * Method to fetch details of nearbY Stores on which cart Items are present
     *
     * @param string $postCode
     * @param decimal $lat
     * @param decimal $lng
     * @return array Details of nearBY Stores
     */
    public function getAvailableRetailers($postCode, $lat, $lng) {
        $response = array(
            'status'    => false,
            'message'   => trans('messages.empty_cart_message'),
            'data'      => array(),
        );
        try {
            $cartContent        = $this->content();
            $cartQuantity       = $this->count(true);
            $aProductId         = $aNearByAvailableStores = $aProductIdNew = array();
            if (!empty($cartQuantity)) {
                foreach ($cartContent as $cartRow) {
                    $aProductId[$cartRow['id']] = $cartRow['id'];
                    $qty = 0;
                    $alreadyAdded = FALSE;
                    foreach ($aProductIdNew as $key => $value) {
                        if ($value['id'] == $cartRow['id']) {
                            $qty = $value['qty'];
                            $aProductIdNew[$key]['qty'] = $qty + $cartRow['qty'];
                            $alreadyAdded = TRUE;
                            break;
                        }
                    }
                    if($alreadyAdded)
                        continue;
                    $aProductIdNew[] = array(
                        'id' => $cartRow['id'],
                        'qty' => $cartRow['qty'],
                    );
                }
                $orderModel                 = new SalesOrder();
                $aNearByAvailableStores     = $orderModel->getNearByStoresForProductsCheckout($aProductId, $postCode, $lat, $lng);
            }
            $this->_storeProductDetails = $aNearByAvailableStores;
            if (!empty($this->_storeProductDetails)) {
                $aBasketProducts    = $aProductIdNew;
                $aStoreDetails      = array();
                foreach ($aNearByAvailableStores as $key => $storeSingle) {
                    $aTempStore = array(
                        'id'        => $storeSingle['id'],
                        'distance'  => $storeSingle['distance']
                    );
                    foreach ($storeSingle['products'] as $prodSingle) {
                        $aTempStore['products'][] = $prodSingle;
                    }
                    $aStoreDetails[] = $aTempStore;
                }
                $commonModel            = new CommonHelper();
                $afullfillmentResponse  = $commonModel->getStoreFullFillment($this->userCartId, $aBasketProducts, $aStoreDetails);
                if ($afullfillmentResponse['status']) {
                    $aProductStoreMapping   = $afullfillmentResponse['data']['mapping'];
                    $response               = $this->updateProductStore($aProductStoreMapping);
                    $response['data']       = $aNearByAvailableStores;
                } else {
                    $response['message'] = $afullfillmentResponse['status'];
                }
            } else {
                $response['message']    = trans('messages.common_error');
                $errMsg                 = trans('messages.checkout_error_product_missing');
                CommonHelper::event($errMsg, CommonHelper::CHECKOUT_LOG_FILE, CommonHelper::DAILY);
            }
        } catch (Exception $ex) {
            $response['message']    = trans('messages.common_error');
            $errMsg                 = trans('messages.checkout_exception') . $ex->getMessage() . "|" . $ex->getFile() . "|" . $ex->getLine();
            CommonHelper::event($errMsg, CommonHelper::CHECKOUT_LOG_FILE, CommonHelper::DAILY);
        }
        return $response;
    }

    /**
     * Method To validate Coupon.
     * If valid applies to current Cart.
     * 
     * @param string $promoCode
     * @param int $userId
     * @return array
     */
    public function validateAndApplyCoupon($promoCode, $userId) {
        $response = array(
            'status'        => false,
            'message'       => '',
            'data'          => array('new_amount' => 0.00)
        );
        try {
            if (empty($this->appliedCoupon)) {
                $promocodedata = $this->getCouponModel()->validateCoupon($promoCode, $userId);
                if ($promocodedata['valid']) {
                    $this->applyCoupon($promocodedata['data']);
                    $response['message']    = trans('messages.coupon_apply_success');
                    $response['status']     =  true;
                    $newAmount = $this->subtotal(true);
                    $response['data']['new_amount'] = $newAmount;
                } else {
                    $response['message'] = $promocodedata['message'];
                }
            } else {
                $response['message'] = trans('messages.coupon_already_applied');
            }
        } catch (Exception $ex) {
            $response['message'] = trans('messages.coupon_apply_error');
            $errMsg = trans('messages.coupon_exception') . $ex->getMessage() . "|" . $ex->getFile() . "|" . $ex->getLine();
            CommonHelper::event($errMsg, CommonHelper::COUPON_LOG_FILE, CommonHelper::DAILY);
        }
        return $response;
    }

    /**
     * Method to prepare Order Object [Earlier only Cart Items were storing in DB]
     * 
     * @param type $cartItems
     * @return type
     */
    protected function _getCartOrderObject($cartItems) {
        $this->setDeliveryCharges();
        return array(
            'items'             => $cartItems,
            'coupon'            => $this->appliedCoupon,
            'delivery_postcode' => $this->deliveryPostcode,
            'delivery_charges'  => $this->deliveryCharges,
        );
    }

    /**
     * Method to check whether Coupon is applied or not
     *
     * @return boolean
     */
    public function hasCouponCode() {
        $hasCoupon = false;
        if (!empty($this->appliedCoupon)) {
            $hasCoupon = true;
        }
        return $hasCoupon;
    }

    /**
     * Method to get applied coupon on order.
     *
     * @return string
     */
    public function getCouponCode() {
        $coupon = null;
        if (!empty($this->appliedCoupon)) {
            $coupon = $this->appliedCoupon['coupon_code'];
        }
        return $coupon;
    }

    /**
     * Method to get applied coupon details on order.
     *
     * @return array
     */
    public function getCouponDetails() {
        $aCouponDetails = array();
        if (!empty($this->appliedCoupon)) {
            $aCouponDetails = $this->appliedCoupon;
        }
        return $aCouponDetails;
    }

    /**
     * Method to Get Revised AMount after applying different discounts
     *
     * @param decimal $amount Original Amount Of order
     * @return decimal $revisedAmount
     */
    public function getAmountAfterDiscount($amount) {
        $revisedAmount = $amount;
        if (!empty($this->appliedCoupon)) {
            $couponType     = $this->appliedCoupon['discount_type'];
            $couponAmount   = $this->appliedCoupon['discount_amount'];
            switch ($couponType) {
                case "P":
                    $discountAmount = ($couponAmount/100)*$amount;
                    $discountAmount = $amount - $discountAmount;
                    $revisedAmount  = $discountAmount;
                    break;
                
                case "F":
                    $revisedAmount = $amount - $couponAmount;
                    break;
                
                case "D" :
                    $this->_deliveryDiscount = TRUE;
                    $revisedAmount = $amount;
                    
                default:
                    $revisedAmount = $amount;
            }
        }
        if ($revisedAmount <= 0) {
            $revisedAmount = 0.00;
        }
        return $revisedAmount;
    }

    /**
     * Method to apply Coupon on current order.
     *
     * @param $couponData
     */
    public function applyCoupon($couponData) {
        $this->appliedCoupon = (array) $couponData;
        $cartCount = $this->count($totalItems = true);
        $this->saveUserCartData($this->userCartId, $this->cartCollection, $cartCount);
    }

    /**
     * Method to Get Revised AMount after applying different discounts
     *
     * @param decimal $amount Original Amount Of order
     * @return decimal $deliveryCharges
     */
    public function getConsolidatedDeliveryCharges() {
        $deliveryCharges = 0.00;
        foreach ($this->deliveryCharges as $key => $charges) {
            $deliveryCharges += $charges['value'];
        }
        if($this->_deliveryDiscount == TRUE){
            if(empty($this->appliedCoupon['discount_amount']) || $this->appliedCoupon['discount_amount'] == '0.00'){
                $deliveryCharges = 0.00;
            }else{
                $deliveryCharges -= $this->appliedCoupon['discount_amount'];
                if($deliveryCharges <= 0){
                    $deliveryCharges = 0.00;
                }
            }
        }
        return $deliveryCharges;
    }

    /**
     * Method to set Delivery Charges.
     *
     * @return void
     */
    public function setDeliveryCharges() {
        $total              = $this->subtotal($addDriverCharges = false, $applyDiscount = true);
        $aDeliveryCharges   = $this->calculateDeliveryCharges($total);
        $this->deliveryCharges = $aDeliveryCharges;
    }

    /**
     * Method to Get Delivery AMount after applying different condition checks
     *
     * @param decimal $amount Original Amount Of order
     * @return array $this->deliveryCharges Modified value of delivery Charges
     */
    public function calculateDeliveryCharges($amount) {
        $this->deliveryCharges['delivery_charge']['value'] = 0.00;
        $this->deliveryCharges['after_midnight_charge']['value'] = 0.00;
        $this->deliveryCharges['special_category_charge']['value'] = 0.00;
        $this->deliveryCharges['min_basket_charge']['value'] = 0.00;
        $configModel = new Configurations();
        
        $minBasketThreshold = $configModel->get(config('configurations.threshold_min_basket_key'));
        $driverChargeThreshold = $configModel->get(config('configurations.threshold_delivery_key'));
        if ($amount < $minBasketThreshold) {
            $minBasketCharge = $configModel->get(config('configurations.min_basket_price_key'));
            $this->deliveryCharges['min_basket_charge']['value'] = $this->formatPrice($minBasketCharge);
        }
        if ($amount < $driverChargeThreshold) {
            $deliveryCharge = $configModel->get(config('configurations.delivery_charge_key'));
            $this->deliveryCharges['delivery_charge']['value'] = $this->formatPrice($deliveryCharge);
        }
        $applyMidnightCharges = $this->_applyMidnightCharges();
        if ($applyMidnightCharges) {
            $midnightCharges = $configModel->get(config('configurations.after_midnight_key'));
            $this->deliveryCharges['after_midnight_charge']['value'] = $this->formatPrice($midnightCharges);
        }
        $hasSpecialCategory = $this->_hasSpecialCategory();
        if ($hasSpecialCategory) {
            $specialCategoryCharges = $configModel->get(config('configurations.special_category_key'));
            $this->deliveryCharges['special_category_charge']['value'] = $this->formatPrice($specialCategoryCharges);
        }
        return $this->deliveryCharges;
    }

    /**
     * Protected method to check whether to apply Midnight Charges or not
     *
     * @return boolean
     */
    protected function _applyMidnightCharges() {
        $currentTime = CommonHelper::getCurrentDateTime();
        $currentTime = $currentTime->getTimestamp();
        if ($currentTime > strtotime('12:00am') && $currentTime < strtotime('07:00am')) {
            return true;
        }
        return false;
    }

    /**
     * Protected method to check whether cart has any special category.
     * Extra delivery Charge is added for that category.
     *
     * @return boolean
     */
    protected function _hasSpecialCategory() {
        $cartCollection = $this->cartCollection;
        if (!empty($cartCollection)) {
            $aCartCollection = $cartCollection->toArray();
            foreach ($aCartCollection as $key => $aCart) {
                if ($aCart['specialCategory']){
                    return true;
                }
            }
        }
        return false;
    }

    /**
     * Method to delivery Charges of current cart Object
     *
     * @return array
     */
    public function getDeliveryCharges() {
        $this->setDeliveryCharges();
        return $this->deliveryCharges;
    }

    /**
     * Getter Method of Special Delivery Charge
     *
     * @return boolean
     */
    public function getSpecialDeliveryCharges() {
        return $this->formatPrice($this->deliveryCharges['special_category_charge']['value']);
    }

    /**
     * Getter Method of Min Basket Charge
     *
     * @return boolean
     */
    public function getMinBasketCharges() {
        return $this->formatPrice($this->deliveryCharges['min_basket_charge']['value']);
    }

    /**
     * Getter Method of Midnight Delivery Charge
     *
     * @return boolean
     */
    public function getMidnightDeliveryCharges() {
        return $this->formatPrice($this->deliveryCharges['after_midnight_charge']['value']);
    }

    /**
     * Getter Method of Driver Delivery Charge
     *
     * @return boolean
     */
    public function getDriverDeliveryCharges() {
        return $this->formatPrice($this->deliveryCharges['delivery_charge']['value']);
    }

    /**
     * Method to recalculate Delivery Charges on Checkout Page.
     *
     * @return void
     */
    public function recalculateDeliveryCharges() {
        $cartCount = $this->count($totalItems = true);
        $this->saveUserCartData($this->userCartId, $this->cartCollection, $cartCount);
    }

    /**
     * Setter Method for DeliveryPostcode.
     *
     * @param string $postCode
     *
     */
    protected function setDeliveryPostcode($postCode) {
        // i.e set only when First Time is added in cart.
        if (empty($this->deliveryPostcode)) {
            $this->deliveryPostcode = $postCode;
        }
    }

    /**
     * Getter Method for DeliveryPostcode.
     *
     * @return string
     *
     */
    public function getDeliveryPostcode() {
        if (!empty($this->deliveryPostcode)) {
            return $this->deliveryPostcode;
        } else {
            return array(
                'postcode'  => '',
                'lat'       => '',
                'lng'       => ''
            );
        }
    }

    /**
     * Method to add store id and also to do recalculation of charges
     * 
     * @param type $aData
     */
    public function updateProductStore($aData) {
        $response = array(
            'status'    => false,
            'message'   => trans('messages.empty_cart_message'),
        );
        try {
            $cartCollection = $this->cartCollection;
            if (!empty($cartCollection)) {
                /** This check is removed because in case we have multiple 
                 * produtcs added with differnt bundles then request & response 
                 * count will not match.*/
//                if (count($this->cartCollection) == count($aData)) {
                    foreach ($this->cartCollection as $key => $aCart) {
                        foreach ($aData as $apiData) {
                            if ($apiData['product_id'] == $aCart['id']) {
                                $aCart['store_id']      = $apiData['store_id'];
                                $storePrice             = $this->_storeProductDetails[$apiData['store_id']]['products'][$apiData['product_id']]['price'];
                                $storeName              = $this->_storeProductDetails[$apiData['store_id']]['name'];
                                $storeAddress           = $this->_storeProductDetails[$apiData['store_id']]['address'];
                                $storePostcode          = $this->_storeProductDetails[$apiData['store_id']]['postcode'];
                                $storePhone             = $this->_storeProductDetails[$apiData['store_id']]['store_phone'];
                                $storeEmail             = $this->_storeProductDetails[$apiData['store_id']]['store_email'];
                                $aCart['store_price']   = $storePrice;
                                $aCart['store_name']    = $storeName;
                                $aCart['store_address'] = $storeAddress;
                                $aCart['store_postcode']    = $storePostcode;
                                $aCart['store_phone']       = $storePhone;
                                $aCart['store_email']       = $storeEmail;
                                $aCart['productCommission']     = $this->_storeProductDetails[$apiData['store_id']]['products'][$apiData['product_id']]['productCommission'];
                                $aCart['vendorCommission']      = $this->_storeProductDetails[$apiData['store_id']]['products'][$apiData['product_id']]['vendorCommission'];
                                $aCart['store_distance']        = $this->_storeProductDetails[$apiData['store_id']]['distance'];
                                $cartCollection->put($key, $aCart);
                            }
                        }
                    }
                    $cartCount = $this->count($totalItems = true);
                    $this->saveUserCartData($this->userCartId, $this->cartCollection, $cartCount);
                    $response['message']    = trans('messages.success');
                    $response['status']     = true;
//                } else {
//                    $response['message']    = trans('messages.common_error');
//                    $errMsg                 = trans('messages.checkout_exception') . trans('messages.fullfillment_api_less_products');
//                    CommonHelper::event($errMsg, CommonHelper::CHECKOUT_LOG_FILE, CommonHelper::DAILY);
//                }
            }
        } catch (Exception $ex) {
            $response['message']    = trans('messages.common_error');
            $errMsg                 = trans('messages.checkout_exception') . $ex->getMessage() . "|" . $ex->getFile() . "|" . $ex->getLine();
            CommonHelper::event($errMsg, CommonHelper::CHECKOUT_LOG_FILE, CommonHelper::DAILY);
        }
        return $response;
    }


}