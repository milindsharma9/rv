<?php

namespace App\Api\V1\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Api\V1\Model\CommonModel;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Validator;
use Exception;
use JWTAuth;
use App\Cart;
use App\Http\Helper\CommonHelper;
use App\User;
use App\PaymentModel;
use App\ValidPostcode;
use App\SalesOrder;

class CartController extends ApiBaseController {

    /**
     *
     * @var App\Api\V1\Model\CommonModel
     */
    protected $_commonModel = NULL;
    
     /**
     *
     * @var App\PaymentModel
     */
    protected $_paymentModel = NULL;

    /**
     *
     * @var mixed
     */
    private $userCartId = null;

    /**
     *
     * @var string
     */
    private $generatedUserCartId = null;

    /**
     *
     * @var App\Cart
     */
    private $cartModel = null;

    /**
     * Method to return CommonModel Instance
     */
    private function _getCommonModel() {
        if ($this->_commonModel == NULL) {
            $this->_commonModel = new CommonModel();
        }
        return $this->_commonModel;
    }

    /**
     * Method to return Cart Model Instance
     */
    private function getCartModel() {
        if ($this->cartModel == null) {
            $this->cartModel = new Cart($this->userCartId);
        }
        return $this->cartModel;
    }

    public function __construct(Request $request) {
        $this->setUserCartKey($request); // set unique key for current user from header values.
        parent::__construct($request);
    }
    
    /**
     * Function to Instatntiate Payment Model.
     * 
     * @package api/CustomerController
     * @return object App\Payment Model
     */
    private function _getPaymentModel() {
        if ($this->_paymentModel == NULL) {
            $this->_paymentModel = \App::make('\App\PaymentModel');
        }
        return $this->_paymentModel;
    }
    
    /**
     * Method to set Current User Unique Id
     *
     * @param object $request Illuminate\Http\Request
     * @return void
     *
     */
    public function setUserCartKey(Request $request) {
        $userId = '';
        if ($request->hasHeader('authorization')) {
            $userIdResponse = $this->_getCommonModel()->getUserIdByToken(JWTAuth::getToken());
            if ($userIdResponse['status']) {
                $userId = $userIdResponse['id'];
            }
        } else {
            if ($request->hasHeader('alchemy-cart-auth')) {
                // Check if data is present in headers. Not LoggedIn user revisit case.
                $cartAuthKey = $request->header('alchemy-cart-auth', '0');
                if (empty($cartAuthKey)) {
                    $userId = CommonHelper::generateUniqueIdForCart();  // For First Time user generated unique id and return in response
                    $this->generatedUserCartId   = $userId;
                } else {
                    $userId = $cartAuthKey;
                }
            } else {
                throw new Exception(trans('messages.api_cart_unauthorize_error'));
            }
        }
        $this->userCartId   = $userId;
    }

    /**
     * Method to add Item in cart.
     *
     * @param \Illuminate\Http\Request Request Object
     * @return array $response
     */
    public function add(Request $request)
    {
        $response = $this->responseGenerator;
        try {
            $aParam                 = $request['data'];
            if (!empty($aParam)) {
                $cartData     = $this->getCartModel()->add($aParam);
                $cartResponse = $this->prepareCartResponse($cartData);
                $response->setResponseData($cartResponse);
                $response->setResponseMessage(trans('messages.success'));
                $response->setResponseStatus(TRUE);
                $response->setStatus(TRUE);
                $response->setMessage(trans('messages.success'));
            } else {
                $response->setMessage(trans('messages.api_parameter_missing'));
            }
        } catch (Exception $ex) {
            $message = trans('messages.common_error');
            $response->setMessage($message);
            $debugTrace = $ex->getMessage() . "|" . $ex->getFile() . "|" . $ex->getLine();
            $this->handleAPIErrorMessages($debugTrace);
        }
        $serviceResponse = $response->getServiceResponse();
        return $serviceResponse;
    }

    /**
     * Method to update Item quantity in cart.
     *
     * @param \Illuminate\Http\Request Request Object
     * @return array $response
     */
    public function update(Request $request)
    {
        $response = $this->responseGenerator;
        try {
            $aParam                 = $request['data'];
            if (!empty($aParam)) {
                $rowid          = $aParam['id'];
                $isBundle       = $aParam['isBundle'];
                $qty            = $aParam['qty'];
                $cartData       = $this->getCartModel()->update($rowid, $qty, $isBundle);
                $cartResponse   = $this->prepareCartResponse($cartData);
                $response->setResponseData($cartResponse);
                $response->setResponseMessage(trans('messages.success'));
                $response->setResponseStatus(TRUE);
                $response->setStatus(TRUE);
                $response->setMessage(trans('messages.success'));
            } else {
                $response->setMessage(trans('messages.api_parameter_missing'));
            }
        } catch (Exception $ex) {
            $message = trans('messages.common_error');
            $response->setMessage($message);
            $debugTrace = $ex->getMessage() . "|" . $ex->getFile() . "|" . $ex->getLine();
            $this->handleAPIErrorMessages($debugTrace);
        }
        $serviceResponse = $response->getServiceResponse();
        return $serviceResponse;
    }

    /**
     * Method to remove Item from cart.
     *
     * @param \Illuminate\Http\Request Request Object
     * @return array $response
     */
    public function remove(Request $request)
    {
        $response = $this->responseGenerator;
        try {
            $rowid                  = $request['data'];
            if (!empty($rowid)) {
                $cartData     = $this->getCartModel()->remove($rowid);
                $cartResponse = $this->prepareCartResponse($cartData);
                $response->setResponseData($cartResponse);
                $response->setResponseMessage(trans('messages.success'));
                $response->setResponseStatus(TRUE);
                $response->setStatus(TRUE);
                $response->setMessage(trans('messages.success'));
            } else {
                $response->setMessage(trans('messages.api_parameter_missing'));
            }
        } catch (Exception $ex) {
            $message = trans('messages.common_error');
            $response->setMessage($message);
            $debugTrace = $ex->getMessage() . "|" . $ex->getFile() . "|" . $ex->getLine();
            $this->handleAPIErrorMessages($debugTrace);
        }
        $serviceResponse = $response->getServiceResponse();
        return $serviceResponse;
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
     * Method for preparing cart API Response Data
     * @param Object $cartData Cart Object Data Return by CartModel
     * @return array $cartResponse
     *
     */
    private function prepareCartResponse($cartData) {
        $cartResponse['cart_data']      = $cartData;
        $cartResponse['quantity']       = $this->getTotalQuantity();
        $cartResponse['total']          = $this->getTotal($addDriverCharges = true);
        if (!empty($this->generatedUserCartId)) {
            // If user is hitting first time set auth_key in reponse
            $cartResponse['cart_auth_key']      = $this->generatedUserCartId;
        }
        return $cartResponse;
    }

    /**
     * Method to render Order basket Page for customer
     *
     * @return resources/views/customer mixed
     */
    public function renderCart() {
        $response = $this->responseGenerator;
        try {
            $cartContent                        = $this->getCartContent();
            $cartViewData                       = $this->prepareCartView($cartContent);
            $driverCharge                       = \Config::get('appConstants.driver_charge');
            $currencySymbol                     = \Config::get('appConstants.currency_sign');
            $cartTotal                          = $this->getTotal($addDriverCharges = true);
            $cartResponse                       = $cartViewData;
            if (empty($cartViewData)) {
                $response->setResponseMessage(trans('messages.empty_cart_message'));
                $response->setResponseStatus(FALSE);
            } else {
                $cartResponse['driver_charge']      = $driverCharge;
                $cartResponse['cart_total']         = $cartTotal;
                $cartResponse['quantity']           = $this->getTotalQuantity();
                $response->setResponseData($cartResponse);
                $response->setResponseMessage(trans('messages.success'));
                $response->setResponseStatus(TRUE);
            }
            $response->setStatus(TRUE);
            $response->setMessage(trans('messages.success'));
        } catch (Exception $ex) {
            $message = trans('messages.common_error');
            $response->setMessage($message);
            $debugTrace = $ex->getMessage() . "|" . $ex->getFile() . "|" . $ex->getLine();
            $this->handleAPIErrorMessages($debugTrace);
        }
        $serviceResponse = $response->getServiceResponse();
        return $serviceResponse;
    }

    /**
     * Method to prepare View Data for cart page
     *
     * @param Collection Object $cartContent
     * @return array $aViewData
     */
    protected function prepareCartView($cartContent, $formatResponse = true) {
        $aViewData = $aViewDataResponse =  array();
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
        if ($formatResponse) {
            foreach ($aViewData as $cartData) {
                if (isset($cartData['bundleId'])) {
                    $aViewDataResponse['bundle'][] = $cartData;
                } else {
                    $aViewDataResponse['product'][] = $cartData;
                }
            }
        } else {
            $aViewDataResponse = $aViewData;
        }
        return $aViewDataResponse;
    }
    
     /**
     * Api to checkout cart.
     * 
     * @package CustomerController
     * @param Request $request
     * @return Object $serviceResponse
     *
     */
     public function checkout(Request $request) {
        $response = $this->responseGenerator;
        $postCode = 'user-pincode';
        try {
            $aRequestHeadersKeys = array_keys($request->header());
            $userCartDeliveryPostcode = $request->header($postCode);
            if (!in_array($postCode, $aRequestHeadersKeys)) {
                $response->setMessage(trans('messages.success'));
                $response->setStatus(TRUE);
                $response->setResponseStatus(FALSE);
                $response->setResponseMessage(trans('messages.postcode_not_found'));
                $serviceResponse = $response->getServiceResponse();
                return $serviceResponse;
            }
            $userIdResponse = $this->_getCommonModel()->getUserIdByToken(JWTAuth::getToken());
            if ($userIdResponse['status']) {
                $userId         = $userIdResponse['id'];
                $cartContent    = $data['cartContent'] = $this->getTotalQuantity();
                if (!empty($userId) && !empty($cartContent)) {
                    $data['currencySymbol'] = $currencySymbol = \Config::get('appConstants.currency_sign');
                    $data['cartTotal']      = $cartTotal = $this->getTotal($addDriverCharges = true);
                    $address                = $data['address'] = '';
                    $hasCard                = $hasAddress = $data['hasAddress'] = $data['hasCard'] = false;
                    $card                   = $data['card']   = '';
                    $pinServiceability      = $data['pinServiceability'] = false;
                    $postCodeMatch          = $data['postCodeMatch'] = false;
                    $data['showButton']     = TRUE;
                    $data['buttonMessage']  = '';
                    $userModel              = new User();
                    $userAddress            = $userModel->getUserSavedAddress($userId);
                    $paymentDetails         = $this->_getPaymentModel()->getUserCardDetails($userId);
                    $data['hasAddress']     = $hasAddress = $userAddress['hasAddress'];
                    $data['hasCard']        = $hasCard = $paymentDetails['hasCard'];
                    if ($hasAddress) {
                        $aAddress   = json_decode($userAddress['address'], true);
                        $address    = $data['address'] = $aAddress['address'] . "," .
                                $aAddress['city'] . "," . $aAddress['state'] . "," . $aAddress['pin'];
                        $postcodeModel  = new ValidPostcode();
                        $aValidPostCode = $postcodeModel->getValidPostcodes($aAddress ['pin']);
                        if (in_array($aAddress['pin'], $aValidPostCode)) {
                            $data['pinServiceability'] = $pinServiceability = true;
                            if ($aAddress['pin'] == $userCartDeliveryPostcode) {
                                $data['postCodeMatch'] = $postCodeMatch = true;
                            } else {
                                $data['showButton'] = FALSE;
                                $data['buttonMessage'] = trans('messages.postcode_not_same');
                            }
                        } else {
                            $data['showButton'] = FALSE;
                            $data['buttonMessage'] = trans('messages.postcode_error_not_serviceable');
                        }
                    } else {
                        $data['showButton'] = FALSE;
                        $data['buttonMessage'] = trans('messages.address_not_found');
                    }
                    if ($hasCard) {
                        $aCard = json_decode($paymentDetails['cardDetails'], true);
                        $card = $aCard['CardProvider'] . " ending with " . substr($aCard['Alias'], -4);
                        $data['card'] = $card;
                    } else {
                        $data['showButton'] = FALSE;
                        $data['buttonMessage'] = trans('messages.card_details_not_found');
                    }
                    $response->setResponseData($data);
                    $response->setResponseMessage(trans('messages.details_found'));
                    $response->setResponseStatus(TRUE);
                    $response->setMessage(trans('messages.success'));
                    $response->setStatus(TRUE);
                } else {
                    $response->setResponseStatus(FALSE);
                    $response->setResponseMessage(trans('messages.empty_cart_message'));
                }
            } else {
                $response->setResponseStatus(FALSE);
                $response->setResponseMessage(trans('messages.user_invalid'));
            }
        } catch (Exception $ex) {
            $message = trans('messages.common_error');
            $response->setMessage($message);
            $debugTrace = $ex->getMessage() . "|" . $ex->getFile() . "|" . $ex->getLine();
            $this->handleAPIErrorMessages($debugTrace);
        }
        $serviceResponse = $response->getServiceResponse();
        return $serviceResponse;
    }

    /**
     * Method to placeOrder
     *
     * @param \Illuminate\Http\Request Request Object
     * @return resources/views/customer mixed
     */
    public function placeOrder(Request $request) {
        $response = $this->responseGenerator;
        try {
            $cartQuantity   = $this->getTotalQuantity();
            if (empty($cartQuantity)) {
                $response->setMessage(trans('messages.empty_cart_message'));
                $serviceResponse = $response->getServiceResponse();
                return $serviceResponse;
            }
            $userDetails = $this->_getCommonModel()->getUserDetailsByToken(JWTAuth::getToken());
            if (!$userDetails['status']) {
                $response->setMessage(trans('messages.user_invalid'));
                $serviceResponse = $response->getServiceResponse();
                return $serviceResponse;
            }
            $orderModel     = new SalesOrder();
            $cartContent    = $this->getCartContent();
            $cartTotal      = $this->getTotal($addDriverCharges = true);
            $userId             = $userDetails['data']->id;
            $orderResponse  = $orderModel->placeOrder($cartContent, $cartTotal, $cartQuantity, $userId);
            if ($orderResponse['status']) {
                $paymentResponse    = $this->_getPaymentModel()->getUserPayment($cartTotal, $userId);
                if ($paymentResponse['status']) {
                    //update transaction
                    $orderModel->updateTransactionDetails(
                            $orderResponse['data']['orderId'],
                            $paymentResponse['data']['payInDAta']
                    );
                    //send order email
                    $aOrderDetails['email']         = $userDetails['data']->email;
                    $cartViewData                   = $this->prepareCartView($cartContent, $formatResponse = false);
                    $aOrderDetails['cart_total']    = $this->getTotal($addDriverCharges = true);
                    $aOrderDetails['first_name']    = $userDetails['data']->first_name;
                    $aOrderDetails['last_name']     = $userDetails['data']->last_name;
                    $orderModel->sendOrderEmail($cartViewData, $orderResponse['data']['orderNumber'], $aOrderDetails);
                    $this->destroyCart();
                    $response->setResponseStatus(TRUE);
                    $response->setResponseMessage(trans('messages.success'));
                    $aOrderDetails = array('orderNumber' => $orderResponse['data']['orderNumber']);
                    $response->setResponseData($aOrderDetails);
                } else {
                    $response->setResponseMessage($paymentResponse['message']);
                    $errMsg                 = trans('messages.order_payment_error'). $paymentResponse['data']['code'] . "|" . $paymentResponse['message'];
                    CommonHelper::event($errMsg, CommonHelper::ORDER_LOG_FILE, CommonHelper::DAILY);
                }
            } else {
                $response->setResponseMessage($orderResponse['message']);
            }
            $response->setMessage(trans('messages.success'));
            $response->setStatus(TRUE);
        } catch (Exception $ex) {
            $message    = trans('messages.common_error');
            $response->setMessage($message);
            $debugTrace = $ex->getMessage() . "|" . $ex->getFile() . "|" . $ex->getLine();
            $this->handleAPIErrorMessages($debugTrace);
            CommonHelper::event($debugTrace, CommonHelper::ORDER_LOG_FILE, CommonHelper::DAILY);
        }
        $serviceResponse = $response->getServiceResponse();
        return $serviceResponse;
    }

}
