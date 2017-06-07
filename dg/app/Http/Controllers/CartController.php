<?php

namespace App\Http\Controllers;

use App\Http\Requests;
use Illuminate\Http\Request;

use App\Cart;
use Exception;
use Illuminate\Support\Facades\Config;
use App\SalesOrder;
use App\User;
use App\PaymentModel;
use Illuminate\Support\Facades\Auth;
use App\ValidPostcode;
//use App\Http\Helpers\Email;
use App\Http\Helper\CommonHelper;
use View;

class CartController extends Controller
{
    /**
     * @var \MangoPay\MangoPayApi
     */
    private $mangopay;

    /**
     *
     * @var type 
     */
    private $paymentModel = null;
    
    /**
     *
     * @var type 
     */
    private $_salesOrderModel = null;

    /**
     *
     * @var mixed
     */
    private $userCartId = null;

    /**
     * 
     * @param \MangoPay\MangoPayApi $mangopay
     */
    public function __construct(\MangoPay\MangoPayApi $mangopay) {
        $this->mangopay = $mangopay;
        parent::__construct();
    }

    private $cartModel = null;

    private function getCartModel() {
        if ($this->cartModel == null) {
            $this->setUserCartKey();
            $this->cartModel = new Cart($this->userCartId);
        }
        return $this->cartModel;
    } 
    
    private function _getSalesOrderModel() {
        if ($this->_salesOrderModel == null) {
            $this->_salesOrderModel = new SalesOrder();
        }
        return $this->_salesOrderModel;
    } 
            
    /**
     * Method to add Item in cart.
     *
     * @param \Illuminate\Http\Request Request Object
     * @return array $response
     */
    public function add(Request $request)
    {
        $response = array(
            'status'    => false,
            'message'   => '',
            'data'      => '',
        );
        try {
            $aParam                 = json_decode($request['data'], true);
            // Set Delivery Postcode in Cart Object
            $userDeliveryPostcodeDetails    = CommonHelper::getUserCartDeliveryPostcodeDetails();
            $userCartDeliveryPostcode       = $userDeliveryPostcodeDetails['postcode'];
            $userCartDeliveryLat            = $userDeliveryPostcodeDetails['lat'];
            $userCartDeliveryLng            = $userDeliveryPostcodeDetails['lng'];
            foreach ($aParam as &$prodDetails) {
                $prodDetails['options']['delivery_postcode']  = array(
                    'postcode'  => $userCartDeliveryPostcode,
                    'lat'       => $userCartDeliveryLat,
                    'lng'       => $userCartDeliveryLng
                );
            }
            $cartResponse           = $this->getCartModel()->add($aParam);
            $response['data']       = $cartResponse;
            $response['quantity']   = $this->getTotalQuantity();
            $response['total']      = $this->getTotal();
            $response['status']     = true;
        } catch (Exception $ex) {
            $response['message'] = $ex->getMessage();
        }
        return $response;
    }

    /**
     * Method to update Item quantity in cart.
     *
     * @param \Illuminate\Http\Request Request Object
     * @return array $response
     */
    public function update(Request $request)
    {
        $response = array(
            'status'    => false,
            'message'   => '',
            'data'      => '',
        );
        try {
            $aParam                         = json_decode($request['data'], true);
            $rowid                          = $aParam['id'];
            $isBundle                       = $aParam['isBundle'];
            $qty                            = $aParam['qty'];
            $cartResponse                   = $this->getCartModel()->update($rowid, $qty, $isBundle);
            $response['data']               = $cartResponse;
            $response['quantity']           = $this->getTotalQuantity();
            $response['total']              = $this->getTotal($addDriverCharges = true);
            $view                           = $this->prepareCheckoutProductData();
            $response['html_content'] = $view->render();
            $response['status']     = true;
        } catch (Exception $ex) {
            $response['message'] = $ex->getMessage();
        }
        return $response;
    }

    /**
     * Method to remove Item from cart.
     *
     * @param \Illuminate\Http\Request Request Object
     * @return array $response
     */
    public function remove(Request $request)
    {
        $response = array(
            'status'    => false,
            'message'   => '',
            'data'      => '',
        );
        try {
            $rowid                  = $request['data'];
            $cartResponse           = $this->getCartModel()->remove($rowid);
            $response['data']       = $cartResponse;
            $response['quantity']   = $this->getTotalQuantity();
            $response['total']      = $this->getTotal($addDriverCharges = true);
            $response['status']     = true;
        } catch (Exception $ex) {
            $response['message'] = $ex->getMessage();
        }
        return $response;
    }

    /**
     * Method to get Cart Data
     *
     * @return Collection Object $cartContent
     */
    public function getCartContent()
    {
        $cartContent = $this->getCartModel()->content();
        return $cartContent;
    }

    /**
     * Method to get item Quantity in cart
     *
     * @return int $quantity
     */
    public function getTotalQuantity()
    {
        $quantity = $this->getCartModel()->count(true);
        return $quantity;
    }

    /**
     * Method to get Cart Total
     *
     * @param boolean $addDriverCharges decides Whether to add Driver Charges or not.
     * @return int $quantity
     */
    public function getTotal($addDriverCharges = false)
    {
        $cartTotal = $this->getCartModel()->subtotal($addDriverCharges);
        return $cartTotal;
    }
    
    /**
     * Method to get remove all items from Cart
     *
     * @return Collection Object $cartContent
     */
    public function destroyCart()
    {
        $cartResponse = $this->getCartModel()->destroy();
        return $cartResponse;
    }

    /**
     * Method to render Order basket Page for customer
     *
     * @return resources/views/customer mixed
     */
    public function renderCart() {
        $cartContent        = $this->getCartContent();
        $cartViewData       = $this->prepareCartView($cartContent);
        $currencySymbol     = \Config::get('appConstants.currency_sign');
        $cartTotal          = $this->getTotal($addDriverCharges = true);
        $charges            = $this->getCartModel()->getDeliveryCharges();
        $appliedCoupon      = $this->getCartModel()->getCouponDetails();
        return view('customer.order-basket', compact('cartViewData', 'charges', 'currencySymbol', 'cartTotal', 'appliedCoupon'));
    }

    /**
     * Method to prepare View Data for cart page
     *
     * @param Collection Object $cartContent
     * @return array $aViewData
     */
    protected function prepareCartView($cartContent) {
        $aViewData = array();
        foreach ($cartContent as $cartKey => $cartItem) {
            $aCartKey = explode("_", $cartKey);
            $bundleId = $aCartKey[0];
            $productId = $aCartKey[1];
            if ($bundleId == 0) {
                $aViewData['product_'.$productId] = $cartItem;
            } else {
                if (!isset($aViewData['bundle_'.$bundleId])) {
                    $bundleQty = $cartItem['qty'] / $cartItem['bundleDefaultProductQuantity'];
                    $cartItem['bundleQty'] = $bundleQty;
                    $cartItem['bundleTotalPrice'] = $bundleQty * $cartItem['bundleDefaultTotalPrice'];
                    $cartItem['bundleProducts'] = array($cartItem);
                    $aViewData['bundle_'.$bundleId] = $cartItem;
                } else {
                    array_push($aViewData['bundle_'.$bundleId]['bundleProducts'], $cartItem);
                }
            }
        }
        return $aViewData;
    }

    /**
     * Method to render Checkout Page
     *
     * @return resources/views/customer mixed
     */
    public function checkout() {
        $cartContent        = $this->getTotalQuantity();
        $currencySymbol     = \Config::get('appConstants.currency_sign');
        $cartTotal          = $this->getTotal($addDriverCharges = true);
        $address            = $mapAddress = '';
        $hasCard            = $hasAddress = false;
        $card               = [];
        $userId             = isset(Auth::user()->id) ? Auth::user()->id: NULL;
        $pinServiceability  = false;
        $postCodeMatch      = false;
        $canOrder           = TRUE;
        $orderMessage       = '';
        $url                = 'customer.cart';
        $availableRetailer  = $cartViewData = $userAddress = $charges = array();
        if (!empty($cartContent)) {
            //$this->getCartModel()->recalculateDeliveryCharges(); @todo fulfillmen change Fix this
            $userDeliveryPostcodeDetails    = $this->getDeliveryPostcodeDetails();
            $userCartDeliveryPostcode       = $userDeliveryPostcodeDetails['postcode'];
            $userCartDeliveryLat            = $userDeliveryPostcodeDetails['lat'];
            $userCartDeliveryLng            = $userDeliveryPostcodeDetails['lng'];
            $availableRetailer              = $this->getCartModel()->getAvailableRetailers($userCartDeliveryPostcode,
                    $userCartDeliveryLat, $userCartDeliveryLng);
            $cartTotal          = $this->getTotal($addDriverCharges = true);
            $userModel          = new User();
            $userAddress        = $userModel->getUserSavedAddress($userId);
            $paymentDetails     = $this->getPaymentModel()->getUserCardDetails($userId);
            $hasAddress         = $userAddress['hasAddress'];
            $hasCard            = $paymentDetails['hasCard'];
            if ($hasAddress) {
                $aAddress       = json_decode($userAddress['address'], true);
                $address        = $aAddress['address'] . ", " . $aAddress['city']. ", " . $aAddress['state']. ", " . $aAddress['pin'];
                $mapAddress     = $address;
                $postcodeModel  = new ValidPostcode();
                
                $postCodeDetails    = $postcodeModel->getPostcodeDetail($aAddress['pin']);
                if (!empty($postCodeDetails)) {
                    $pinServiceability  = TRUE;
                }else{
                    $canOrder           = FALSE;
                    $orderMessage       = 'Change Pincode';
                    $url                = 'customer.address';
                }

                if ($aAddress['pin'] == $userCartDeliveryPostcode) {
                    $postCodeMatch      = TRUE;
                }else{
                    $canOrder           = FALSE;
                    $orderMessage       = 'Change Pincode';
                    $url                = 'customer.address';
                }
            }else{
                    $canOrder           = FALSE;
                    $orderMessage       = 'Complete Address';
                    $url                = 'customer.address';
            }
            if ($hasCard) {
                $aCard = json_decode($paymentDetails['cardDetails'], TRUE);
                foreach ($aCard as $key => $value) {
                    foreach ($paymentDetails['cardData']['cardData'] as $cardCount => $mangoCard) {
                        if ($mangoCard['mango_users_card_id'] == $value['Id'] && $mangoCard['is_default'] == '1') {
                            $card['cardDetails']    = $value['CardProvider'] . " ending with " . substr($value['Alias'], -4);
                            $card['id']             = $value['Id'];
                            $cartContent            = $card;
                            break;
                        }
                    }
                }
            }else{
                $canOrder           = FALSE;
                $orderMessage       = 'Enter Payment Details';
                $url                = 'customer.payment';
            }
            $cartData           = $this->getCartContent();
            $cartViewData       = $this->prepareCartView($cartData);
            $charges            = $this->getCartModel()->getDeliveryCharges();
            $appliedCoupon      = $this->getCartModel()->getCouponDetails();
        } else {
            session()->put("checkout_login", 1);
        }
        //@321 Hotfix
        session()->put("checkout_login", 1);
        return view('customer.one-step-checkout',
            compact(
                'cartTotal', 'currencySymbol', 'hasCard', 'hasAddress','userId',
                'address', 'card', 'mapAddress', 'cartContent', 'pinServiceability', 'postCodeMatch',
                'availableRetailer', 'cartViewData', 'userAddress', 'charges', 'appliedCoupon',
                'canOrder', 'orderMessage', 'url'
            )
        );
    }
    
    /**
     * Function to Instatntiate Payment Model.
     *
     * @return Object Payment Model object
     */
    private function getPaymentModel() {
        if ($this->paymentModel == null) {
            $this->paymentModel = new PaymentModel($this->mangopay);
        }
        return $this->paymentModel;
    }
    
    /**
     * Method return order details after placing the user order.
     * 
     * @return type
     */
    public function placeUserOrder(Request $request = NULL) {
        try {
            $cartContent = $this->getCartContent();
            $cartTotal = $this->getTotal($addDriverCharges = true);
            $cartQuantity = $this->getTotalQuantity();
            if (empty($cartQuantity) || ($request != NULL && !$request->isMethod('post'))) {
                return redirect()->route('customer.cart');
            }
            $userId = isset(Auth::user()['id']) ? Auth::user()['id'] : FALSE;
            return $this->_getSalesOrderModel()->placeOrder($cartContent, $cartTotal, $cartQuantity, $userId);
        } catch (Exception $ex) {
            $errMsg                             = trans('messages.order_exception') . $ex->getMessage() . "|" . $ex->getFile() . "|" . $ex->getLine();
            CommonHelper::event($errMsg, CommonHelper::ORDER_LOG_FILE, CommonHelper::DAILY);
        }
    }
    
    /**
     * Method send order confirmation email to user.
     * 
     * @param type $orderNumber
     */
    public function sendOrderEmail($orderNumber, $aEmailData) {
        try {
            $aOrderDetails['email'] = isset(Auth::user()->email) ? Auth::user()->email : '';
            $cartViewData = $this->prepareCartView($this->getCartContent());
            $aOrderDetails['cart_total'] = $this->getTotal($addDriverCharges = true);
            $aOrderDetails['first_name'] = isset(Auth::user()->first_name) ? Auth::user()->first_name : '';
            $aOrderDetails['last_name'] = isset(Auth::user()->last_name) ? Auth::user()->last_name : '';
            $aOrderDetails['phone'] = isset(Auth::user()->phone) ? Auth::user()->phone : '';
            $charges = $this->getCartModel()->getDeliveryCharges();
            foreach ($charges as $key => $val) {
                if (empty($val['value']) || $val['value'] == '0.00') {
                    continue;
                }
                $aOrderDetails['charges'][] = $val;
            }
            $aOrderDetails['appliedcoupon'] = $this->getCartModel()->getCouponDetails();
            if (!empty($aOrderDetails['appliedcoupon'])) {
                $aOrderDetails['appliedcoupon']['discount_amount'] = CommonHelper::getDiscountSymbol($aOrderDetails['appliedcoupon']['discount_type'], $aOrderDetails['appliedcoupon']['discount_amount']);
            }

            $availableRetailer = array();
            // @todo remove this variable and fetch store name from $cartViewData
            $aOrderDetails['available_store'] = $availableRetailer;
            
            //
            $aOrderDetails['AddressLine1']  = $aEmailData['orderAddress']['address'];
            $aOrderDetails['Town']          = $aEmailData['orderAddress']['city'];
            $aOrderDetails['Postcode']      = $aEmailData['orderAddress']['pin'];
            $aOrderDetails['Country']       = $aEmailData['orderAddress']['state'];
            $aEstimatedDeliveryDateTime     = explode(" ", $aEmailData['orderEstimatedDeliveryDateTime']);
            $aOrderDetails['OrderDate']     = $aEstimatedDeliveryDateTime[0];
            $aOrderDetails['OrderTime']     = $aEstimatedDeliveryDateTime[1];
            
            $cartRawContent                 = $this->getCartContent();
            $this->_getSalesOrderModel()->sendOrderEmail($cartViewData, $orderNumber, $aOrderDetails, $cartRawContent);
        } catch (Exception $ex) {
            $errMsg                             = trans('messages.order_exception') . $ex->getMessage() . "|" . $ex->getFile() . "|" . $ex->getLine();
            CommonHelper::event($errMsg, CommonHelper::ORDER_LOG_FILE, CommonHelper::DAILY);
        }
    }
    
    /**
     * Method is called when payment is redirected from 3d secure. 
     * 
     * @param Request $request
     * @return view
     */
    public function response(Request $request) {
        $response = array(
            'status' => false,
            'message' => '',
            'data' => array('orderNumber' => '')
        );
        try {
            if($request->get('transactionId')) {
                $payInId        = $request->get('transactionId');
                $cartTotal      = $this->getTotal($addDriverCharges = true);
                $createdPayIn   = $this->getPaymentModel()->getPayInDetails($payInId);
                if ($createdPayIn['status']) {
                    if ($createdPayIn['data']->Status == \MangoPay\PayInStatus::Succeeded) {
                        $orderResponse = $this->placeUserOrder();
                        //update transaction
                        $this->_getSalesOrderModel()->updateTransactionDetails(
                                $orderResponse['data']['orderId'], $createdPayIn['data']
                        );
                        $response['status']     = true;
                        $response['data']       = array('orderNumber' => $orderResponse['data']['orderNumber']);
                        $aEmailData = array(
                            'orderAddress'  => $orderResponse['data']['orderAddress'],
                            'orderEstimatedDeliveryDateTime'     => $orderResponse['data']['orderEstimatedDeliveryDateTime'],
                        );
                        $this->sendOrderEmail($orderResponse['data']['orderNumber'], $aEmailData);
                        $this->destroyCart();
                    } else {
                        $response['message']    = $createdPayIn['data']->ResultMessage;
                        $errMsg                 = trans('messages.order_payment_error'). $createdPayIn['data']->ResultCode . "|" . $response['message'];
                        CommonHelper::event($errMsg, CommonHelper::ORDER_LOG_FILE, CommonHelper::DAILY);
                    }
                } else {
                    $response['message']        = $createdPayIn['message'];
                    $errMsg                     = $response['mangopay_error'];
                    CommonHelper::event($errMsg, CommonHelper::ORDER_LOG_FILE, CommonHelper::DAILY);
                }
            } 
        } catch (Exception $ex) {
            $response['message']                = $ex->getMessage();
            $errMsg                             = trans('messages.order_exception') . $ex->getMessage() . "|" . $ex->getFile() . "|" . $ex->getLine();
            CommonHelper::event($errMsg, CommonHelper::ORDER_LOG_FILE, CommonHelper::DAILY);
        }
        $status         = $response['status'];
        $message        = $response['message'];
        $orderNumber    = $response['data']['orderNumber'];
        return view('customer.transaction-complete', compact(
                        'orderNumber', 'cartTotal', 'status', 'message'
                )
        );
    }

    /**
     * Method to placeOrder
     *
     * @param \Illuminate\Http\Request Request Object
     * @return resources/views/customer mixed
     */
    public function placeOrder(Request $request) {
        $response = array(
            'status' => false,
            'message' => '',
            'data' => array('orderNumber' => '')
        );
        $cartTotal = 0;
        try {
            $cartTotal = $this->getTotal($addDriverCharges = true);
            $userId             = isset(Auth::user()['id']) ? Auth::user()['id'] : FALSE;
            $orderResponse = $this->placeUserOrder($request);
            if ($orderResponse['status']) {
                $paymentResponse    = $this->getPaymentModel()->getUserPayment($cartTotal, $userId);
                if ($paymentResponse['status']) {
                    if(isset($paymentResponse['data']['payInData']->ExecutionDetails->SecureModeRedirectURL)){
                        $url = (string)$paymentResponse['data']['payInData']->ExecutionDetails->SecureModeRedirectURL;
                        header('Location: '. $url);
                        die();
                    }
                    //update transaction
                    $this->_getSalesOrderModel()->updateTransactionDetails(
                            $orderResponse['data']['orderId'],                            
                            $paymentResponse['data']['payInData']
                    );
                    $response['status'] = true;
                    $response['data'] = array('orderNumber' => $orderResponse['data']['orderNumber']);
                    $aEmailData = array(
                        'orderAddress'  => $orderResponse['data']['orderAddress'],
                        'orderEstimatedDeliveryDateTime'     => $orderResponse['data']['orderEstimatedDeliveryDateTime'],
                    );
                    $this->sendOrderEmail($orderResponse['data']['orderNumber'], $aEmailData);
                    $this->destroyCart();
                } else {
                    $response['message']    = $paymentResponse['message'];
                    $errMsg                 = trans('messages.order_payment_error'). $paymentResponse['data']['code'] . "|" . $response['message'];
                    CommonHelper::event($errMsg, CommonHelper::ORDER_LOG_FILE, CommonHelper::DAILY);
                }
            } else {
                $response['message'] = $orderResponse['message'];
            }
        } catch (Exception $ex) {
            $response['message'] = $ex->getMessage();
            $errMsg = trans('messages.order_exception'). $ex->getMessage()."|". $ex->getFile()."|".$ex->getLine();
            CommonHelper::event($errMsg, CommonHelper::ORDER_LOG_FILE, CommonHelper::DAILY);
        }
        $status = $response['status'];
        $message = $response['message'];
        $orderNumber = $response['data']['orderNumber'];
        return view('customer.transaction-complete', compact(
                        'orderNumber' , 'cartTotal', 'status', 'message'
                )
        );
    }
    
    
    /**
     * Method to apply promocode to avail discount
     *
     * @return void
     *
     */
    public function applyPromocode(Request $request) {
        $response = array(
            'status'    => false,
            'message'   => '',
            'data'      => array('new_amount' => 0.00)
        );
        try {
            $userId        = isset(Auth::user()['id']) ? Auth::user()['id'] : FALSE;
            $promoCode     = $request['promo_code'];
            $response      = $this->getCartModel()->validateAndApplyCoupon($promoCode, $userId);
            $view          = $this->prepareCheckoutProductData();
            $response['html_content'] = $view->render();
        } catch (Exception $ex) {
            $response['message'] = $ex->getMessage();
            $errMsg = trans('messages.coupon_exception') . $ex->getMessage() . "|" . $ex->getFile() . "|" . $ex->getLine();
            CommonHelper::event($errMsg, CommonHelper::COUPON_LOG_FILE, CommonHelper::DAILY);
        }
        return $response;
    }

    /**
     * Method to set Current User Unique Id
     *
     * @return void
     *
     */
    public function setUserCartKey() {
        if (Auth::user()) {
            $userId = Auth::user()->id; // If user is logged in, use Id from users table.
        } else {
            $userCartSessionKey = \Config::get('appConstants.user_cart_unique_session_key');
            // Check if data is present in session. Not LoggedIn user revisit case.
            $userId = session()->get($userCartSessionKey, NULL);
            if (empty($userId)) {
                $userId = CommonHelper::generateUniqueIdForCart(); // For First Time user generated unique id and set in session
                session()->put($userCartSessionKey, $userId);
            }
        }
        $this->userCartId   = $userId;
    }

    /**
     * Method to get delivery Postcode
     *
     * @return array $postCode Delivery Postcode Of Current Cart Object
     */
    public function getDeliveryPostcodeDetails()
    {
        $aPostCodeDetails = $this->getCartModel()->getDeliveryPostcode();
        return $aPostCodeDetails;
    }

    public function comparePostcodeAndResetCart($newPostCode) {
        $userDeliveryPostcodeDetails    = $this->getDeliveryPostcodeDetails();
        $cartPostcode                   = $userDeliveryPostcodeDetails['postcode'];
        if (!empty($cartPostcode)) {
            if ($newPostCode != $cartPostcode) {
                $cartQuantity = $this->getTotalQuantity();
                if (!empty($cartQuantity)) {
                    $this->destroyCart(); // Destroy Cart Content if Postcode Changes.
                }
            }
        }
    }
    
    /**
     * 
     * Method clear the cart data.
     * 
     * @return customer checkout 
     */
    public function clearCart() {
        $this->destroyCart();
        return redirect()->route('customer.checkout');
    }
    
    
    /**
     * Function to prepare checkout-product-data blade.
     * 
     * @return View
     */
    public function prepareCheckoutProductData() {
        $charges = $this->getCartModel()->getDeliveryCharges();

        $userDeliveryPostcodeDetails = $this->getDeliveryPostcodeDetails();
        $userCartDeliveryPostcode = $userDeliveryPostcodeDetails['postcode'];
        $userCartDeliveryLat = $userDeliveryPostcodeDetails['lat'];
        $userCartDeliveryLng = $userDeliveryPostcodeDetails['lng'];
        $this->getCartModel()->getAvailableRetailers($userCartDeliveryPostcode, $userCartDeliveryLat, $userCartDeliveryLng);

        $cartContent = $this->getCartContent();
        $cartViewData = $this->prepareCartView($cartContent);
        $appliedCoupon = $this->getCartModel()->getCouponDetails();

        $view = View::make('customer.partials.checkout-product-data', 
    ['charges' => $charges, 'cartViewData' => $cartViewData, 'appliedCoupon' => $appliedCoupon]);
        return $view;
    }

}
