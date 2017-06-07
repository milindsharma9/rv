<?php

namespace App\Api\V1\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Api\V1\Response\Response;
use App\Api\V1\Model\CommonModel;
use Illuminate\Support\Facades\Validator;
use Exception;
use JWTAuth;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Support\Facades\Password;
use Illuminate\Mail\Message;
use App\User;
use App\Http\Helper\FileUpload;
use App\Api\V1\Model\CustomerModel;
use App\Cart;

/**
 * api/CustomerController 
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
class CustomerController extends ApiBaseController {

    const FILE_SUB_DIR = 'user';

    /*
      |--------------------------------------------------------------------------
      | Password Reset Controller
      |--------------------------------------------------------------------------
      |
      | This controller is responsible for handling password reset requests
      | and uses a simple trait to include this behavior. You're free to
      | explore this trait and override any methods you wish to tweak.
      |
     */

use ResetsPasswords;

    /**
     *
     * @var App\Product 
     */
    protected $_commonModel = NULL;

    /**
     *
     * @var App\Product 
     */
    protected $_userModel = NULL;

    /**
     *
     * @var App\PaymentModel 
     */
    protected $_paymentModel = NULL;

    /**
     *
     * @var App\Http\Helper\FileUpload 
     */
    protected $_fileUploader = NULL;

    /**
     * Function to Instatntiate FileUpload Model.
     * 
     * @package api/CustomerController
     * @return object App\Http\Helper\FileUpload
     */
    private function _getFileUploadModel() {
        if ($this->_fileUploader == NULL) {
            $this->_fileUploader = new FileUpload();
        }
        return $this->_fileUploader;
    }

    /**
     * Function to Instatntiate CommonModel Model.
     * 
     * @package api/CustomerController
     * @return object App\CommonModel Model
     */
    private function _getCommonModel() {
        if ($this->_commonModel == NULL) {
            $this->_commonModel = new CommonModel();
        }
        return $this->_commonModel;
    }

    /**
     * Function to Instatntiate User Model.
     * 
     * @package api/CustomerController
     * @return object App\User Model
     */
    private function _getUserModel() {
        if ($this->_userModel == NULL) {
            $this->_userModel = new User();
        }
        return $this->_userModel;
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
     * Method to Register User via API
     * 
     * @package api/CustomerController
     * @return Object $serviceResponse
     */
    public function register(Request $request) {
        $response = $this->responseGenerator;
        try {
            $rules = array(
                'email' => 'required|email|max:255|unique:users',
                'password' => 'required|min:6|confirmed',
            );
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                $response->setResponseStatus(FALSE);
                $response->setResponseMessage(trans('messages.validation_error'));
                $response->setMessages($validator->errors()->toArray());
                $response->setStatus(FALSE);
                $response->setMessage(trans('messages.validation_error'));
            } else {
                $registerResponse = $this->_getCommonModel()->register($request->all());
                if ($registerResponse['status']) {
                    $userDetails = $this->_getCommonModel()->getUserdetailsWithToken($request->all());
                    $this->_getUserData($userDetails);
                    if ($userDetails['status']) {
                        $this->handleUserCart($request, $userDetails);
                    }
                } else {
                    $response->setResponseStatus(FALSE);
                    $response->setResponseMessage($registerResponse['message']);
                    $this->handleAPIErrorMessages($registerResponse['debug_trace']);
                }
                $response->setStatus(TRUE);
                $response->setMessage(trans('messages.success'));
            }
        } catch (Exception $ex) {
            $message = $ex->getMessage();
            $response->setMessage($message);
            $response->setDebugTraceMessage($message);
        }
        $serviceResponse = $response->getServiceResponse();
        return $serviceResponse;
    }

    /**
     * Method to login via API
     *
     * @package api/CustomerController
     * @return Object $serviceResponse
     */
    public function doLogin(Request $request) {
        $response = $this->responseGenerator;
        try {
            $rules = array(
                'email' => 'required|email|max:255',
                'password'  => 'required|min:6',
                //'password' => 'required|min:4',
            );
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                $response->setResponseStatus(FALSE);
                $response->setResponseMessage(trans('messages.validation_error'));
                $response->setMessages($validator->errors()->toArray());
                $response->setStatus(FALSE);
                $response->setMessage(trans('messages.validation_error'));
            } else {
                $userDetails = $this->_getCommonModel()->getUserdetailsWithToken($request->all());
                $this->_getUserData($userDetails);
                if ($userDetails['status']) {
                    $this->handleUserCart($request, $userDetails);
                }
                $response->setStatus(TRUE);
                $response->setMessage(trans('messages.success'));
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
     * Method to logout in API
     * 
     * @package api/CustomerController
     * @param Request $request | required token
     * @return Object $serviceResponse
     */
    public function logout(Request $request) {
        $response = $this->responseGenerator;
        try {
            if (JWTAuth::invalidate(JWTAuth::getToken()) == 1) {
                $response->setResponseMessage(trans('messages.logout_success'));
                $response->setResponseStatus(TRUE);
            } else {
                $response->setResponseStatus(FALSE);
                $response->setResponseMessage(trans('messages.token_invalid'));
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
     * Send a reset link to the given user.
     *
     * @package api/CustomerController
     * @param  \Illuminate\Http\Request  $request
     * @return Object $serviceResponse
     */
    public function postEmail(Request $request) {
        $response = $this->responseGenerator;
        try {
            $validator = Validator::make($request->all(), ['email' => 'required|email']);
            if ($validator->fails()) {
                $response->setResponseStatus(FALSE);
                $response->setResponseMessage(trans('messages.validation_error'));
                $response->setMessages($validator->errors()->toArray());
                $response->setStatus(FALSE);
                $response->setMessage(trans('messages.validation_error'));
            } else {
                $eResponse = Password::sendResetLink($request->only('email'), function (Message $message) {
                            $message->subject($this->getEmailSubject());
                        });

                switch ($eResponse) {
                    case Password::RESET_LINK_SENT:
                        $response->setResponseMessage(trans('messages.email_success'));
                        $response->setResponseStatus(TRUE);
                        break;
                    case Password::INVALID_USER:
                        $response->setResponseStatus(FALSE);
                        $response->setResponseMessage(trans('messages.user_invalid'));
                        break;
                }
                $response->setStatus(TRUE);
                $response->setMessage(trans('messages.success'));
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
     * Api to edit customer profile.
     * 
     * @package api/CustomerController
     * @param Request $request
     * @return Object $serviceResponse
     */
    public function editProfile(Request $request) {
        $response = $this->responseGenerator;
        try {
            $rules = $this->_getUserModel()->getCustomerProfileValidationRules();
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                $response->setResponseStatus(FALSE);
                $response->setResponseMessage(trans('messages.validation_error'));
                $response->setMessages($validator->errors()->toArray());
                $response->setStatus(FALSE);
                $response->setMessage(trans('messages.validation_error'));
            } else {
                $userIdResponse = $this->_getCommonModel()->getUserIdByToken(JWTAuth::getToken());
                if ($userIdResponse['status']) {
                    $this->_getUserModel()->updateCustomerProfile($request->all(), $userIdResponse['id']);
                    $userDetails = $this->_getCommonModel()->getUserDetailsByToken(JWTAuth::getToken());
                    $this->_getUserData($userDetails, FALSE);
                    $response->setResponseMessage(trans('messages.user_update_success'));
                    $response->setResponseStatus(TRUE);
                } else {
                    $response->setResponseStatus(FALSE);
                    $response->setResponseMessage(trans('messages.user_invalid'));
                }
                $response->setStatus(TRUE);
                $response->setMessage(trans('messages.success'));
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
     * This method return's User Data.
     * 
     * @param Object $userDetails
     * @param Boolean $tokenRequired
     */
    public function _getUserData($userDetails, $tokenRequired = TRUE) {
        if ($userDetails['status']) {
            $data = array(
                'id'            => $userDetails['data']->id,
                'first_name'    => $userDetails['data']->first_name,
                'last_name'     => $userDetails['data']->last_name,
                'email'         => $userDetails['data']->email,
                'phone'         => $userDetails['data']->phone,
                'image'         => $userDetails['data']->image,
            );
            if ($tokenRequired) {
                $data = array_merge($data, ['api_token' => $userDetails['token']]);
            }
            $fileSubDir             = 'user';
            $data['image_base_url'] = $this->getBaseDirectoryForImages($fileSubDir, FALSE);
            $data['address'] = $this->getUserAddress($userDetails['data']->id);
            $this->responseGenerator->setResponseData($data);
            $this->responseGenerator->setResponseStatus(TRUE);
        } else {
            $this->responseGenerator->setResponseStatus(FALSE);
            $this->responseGenerator->setResponseMessage(trans('messages.user_invalid'));
        }
    }

    /**
     * Api to edit customer address.
     * 
     * @package api/CustomerController
     * @param Request $request
     * @return Object $serviceResponse
     */
    public function editAddress(Request $request, CartController $cartControllerInstance) {
        $response = $this->responseGenerator;
        try {
            $rules = $this->_getUserModel()->getValidationForAddress($request['pin']);
            $validator = Validator::make($request->all(), $rules, $this->_getUserModel()->validationMessages());
            if ($validator->fails()) {
                $response->setResponseStatus(FALSE);
                $response->setResponseMessage(trans('messages.validation_error'));
                $response->setMessages($validator->errors()->toArray());
                $response->setStatus(FALSE);
                $response->setMessage(trans('messages.validation_error'));
            } else {
                $userIdResponse = $this->_getCommonModel()->getUserIdByToken(JWTAuth::getToken());
                if ($userIdResponse['status']) {
                    $this->_getUserModel()->updateCustomerAddress($userIdResponse['id'], $request->all());
                    // Empty cart, if Postcode is not same as set during adding items in cart.
                    $cartPostcode       = $request->header('user-pincode', '');
                    if (!empty($cartPostcode)) {
                        $newPostcode = $request['pin'];
                        if ($newPostcode != $cartPostcode) {
                            $cartQuantity = $cartControllerInstance->getTotalQuantity();
                            if (!empty($cartQuantity)) {
                                $cartControllerInstance->destroyCart(); // Destroy Cart Content if Postcode Changes.
                            }
                        }
                    }
                    //
                    $response->setResponseMessage(trans('messages.user_update_success'));
                    $response->setResponseStatus(TRUE);
                } else {
                    $response->setResponseStatus(FALSE);
                    $response->setResponseMessage(trans('messages.user_invalid'));
                }
                $response->setStatus(TRUE);
                $response->setMessage(trans('messages.success'));
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
     * Api to upload customer image.
     * 
     * @package api/CustomerController
     * @param Request $request
     * @return Object $serviceResponse
     */
    public function uploadImage(Request $request) {
        $response = $this->responseGenerator;
        try {
            $rules = array(
                'image' => 'required'
            );
            $pos = strpos($request['image'], ';');
            if (empty($pos)) {
                $response->setResponseStatus(FALSE);
                $response->setResponseMessage(trans('messages.base64_image_validation'));
                $response->setMessages($validator->errors()->toArray());
                $response->setStatus(FALSE);
                $response->setMessage(trans('messages.validation_error'));
            } else {
                $type = explode(':', substr($request['image'], 0, $pos))[1];
                $extension = explode('/', $type);
                $validator = Validator::make($request->all(), $rules);
                if ($validator->fails()) {
                    $response->setResponseStatus(FALSE);
                    $response->setResponseMessage(trans('messages.validation_error'));
                    $response->setMessages($validator->errors()->toArray());
                    $response->setStatus(FALSE);
                    $response->setMessage(trans('messages.validation_error'));
                } else if (isset($extension[1]) && !in_array($extension[1], ['jpeg', 'jpg'])) {
                    $response->setResponseStatus(FALSE);
                    $response->setResponseMessage(trans('messages.user_image_validation'));
                    $response->setMessages($validator->errors()->toArray());
                    $response->setStatus(FALSE);
                    $response->setMessage(trans('messages.validation_error'));
                } else {
                    $pos = strpos($request['image'], ';');
                    $type = explode(':', substr($request['image'], 0, $pos))[1];
                    $extension = explode('/', $type);
                    $data = str_replace('data:' . $type . ';base64,', '', $request['image']);
                    $data = str_replace(' ', '+', $data);
                    $data = base64_decode($data);
                    $targetSubDir = self::FILE_SUB_DIR;
                    if (!file_exists(public_path('uploads/' . $targetSubDir))) {
                        if (!file_exists(public_path('uploads'))) {
                            mkdir(public_path('uploads'), 0777);
                        }
                        mkdir(public_path('uploads/' . $targetSubDir), 0777);
                        mkdir(public_path('uploads/' . $targetSubDir . '/thumb'), 0777);
                    }
                    $fileSavePath = 'uploads/' . $targetSubDir;
                    $fileThumbSavePath = 'uploads/' . $targetSubDir . '/thumb';
                    $filename = uniqid() . '.' . $extension[1];
                    file_put_contents(public_path($fileSavePath) . '/' . $filename, $data);
                    file_put_contents(public_path($fileThumbSavePath) . '/' . $filename, $data);
                    chmod(public_path($fileSavePath) . '/' . $filename, 0777);
                    chmod(public_path($fileThumbSavePath) . '/' . $filename, 0777);
                    $userIdResponse = $this->_getCommonModel()->getUserIdByToken(JWTAuth::getToken());
                    if ($userIdResponse['status']) {
                        $user = User::findOrFail($userIdResponse['id']);
                        $user->update(['image' => $filename]);
                        $fileSubDir                     = 'user';
                        $dataImage['image_base_url']    = $this->getBaseDirectoryForImages($fileSubDir, FALSE);
                        $dataImage['image']             = $filename;
                        $response->setResponseData($dataImage);
                        $response->setResponseMessage(trans('messages.user_update_success'));
                        $response->setResponseStatus(TRUE);
                    } else {
                        $response->setResponseStatus(FALSE);
                        $response->setResponseMessage(trans('messages.user_invalid'));
                    }
                }
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
     * Api for track Order Page
     * 
     * @package CustomerController
     * @param Request $request
     * @return Object $serviceResponse
     */
    public function ordertrack(Request $request) {
        $aInputRequest = $request->all();
        $response = $this->responseGenerator;
        try {
            $orderNumber = isset($aInputRequest['orderNumber']) ? $aInputRequest['orderNumber'] : '';
            $userId = isset($aInputRequest['userId']) ? $aInputRequest['userId'] : '';
            if (!empty($orderNumber) && !empty($userId)) {
                $apiModel = new CustomerModel();
                $data = $apiModel->trackorderpage($orderNumber);
                $response->setResponseData($data);
                if ($data['exist']) {
                    $response->setResponseMessage(trans('messages.success'));
                    $response->setResponseStatus(TRUE);
                    $response->setMessage(trans('messages.success'));
                    $response->setStatus(TRUE);
                } else {
                    $response->setResponseMessage(trans('messages.postcode_not_found'));
                }
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
     * Api to change password
     * 
     * @package CustomerController
     * @param Request $request
     * @return Object $serviceResponse
     *
     */
    public function changePassword(Request $request) {
        $aInputRequest = $request->all();
        $response = $this->responseGenerator;
        try {
            $userIdResponse = $this->_getCommonModel()->getUserIdByToken(JWTAuth::getToken());
            if ($userIdResponse['status']) {
                $userDetails = $this->_getCommonModel()->getUserDetailsByToken(JWTAuth::getToken());
                if ($userDetails['status']) {
                    $userEmail = isset($userDetails['data']->email) ? $userDetails['data']->email : '';
                    $userId = isset($userDetails['data']->id) ? $userDetails['data']->id : '';
                    $password = isset($aInputRequest['password']) ? $aInputRequest['password'] : '';
                    $confirmPassword = isset($aInputRequest['password_confirmation']) ? $aInputRequest['password_confirmation'] : '';
                    $request = new Request(array_merge($request->all(), ['email' => $userEmail]));
                    $userModel = new User();
                    $validRules = $userModel->getChangePasswordValidation();
                    $validator = Validator::make($request->all(), $validRules);
                    if ($validator->fails()) {
                        $response->setResponseStatus(FALSE);
                        $response->setResponseMessage(trans('messages.validation_error'));
                        $response->setMessages($validator->errors()->toArray());
                        $response->setStatus(FALSE);
                        $response->setMessage(trans('messages.validation_error'));
                    } else {
                        $userstatus = $userModel->updateUserPassword($userId, $userEmail, $confirmPassword);
                        $response->setResponseData($userstatus);
                        if ($userstatus['status']) {
                            $response->setResponseMessage(trans('messages.password_change_success'));
                            $response->setResponseStatus(TRUE);
                            $response->setMessage(trans('messages.success'));
                            $response->setStatus(TRUE);
                        } else {
                            $response->setResponseMessage(trans('messages.user_invalid'));
                        }
                    }
                } else {
                    $response->setResponseStatus(FALSE);
                    $response->setResponseMessage(trans('messages.user_invalid'));
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
     * API to get customer card details.
     * 
     * @package CustomerController
     * @param Request $request
     * @return Object $serviceResponse
     */
    public function getCustomerCard(Request $request) {
        $aInputRequest = $request->all();
        $response = $this->responseGenerator;
        try {
            $userId = isset($aInputRequest['userId']) ? $aInputRequest['userId'] : '';
            if (!empty($userId)) {
                $paymentDetails = $this->_getPaymentModel()->getUserCardDetails($userId);
                if ($paymentDetails['hasCard']) {
                    $aCard = json_decode($paymentDetails['cardDetails'], true);
                    $card['number'] = $aCard['CardProvider'] . " ending with " . substr($aCard['Alias'], -4);
                    $card['hasCard'] = TRUE;
                    $response->setResponseData($card);
                    $response->setResponseMessage(trans('messages.success'));
                    $response->setResponseStatus(TRUE);
                    $response->setMessage(trans('messages.success'));
                    $response->setStatus(TRUE);
                } else {
                    $card['number'] = trans('messages.card_not_found');
                    $card['hasCard'] = FALSE;
                    $response->setResponseData($card);
                    $response->setResponseMessage(trans('messages.success'));
                    $response->setResponseStatus(TRUE);
                    $response->setMessage(trans('messages.success'));
                    $response->setStatus(TRUE);
                }
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
     * Method to get User Address Data
     *
     * @param int $userId
     * @return array $aAddressDetails
     */
    public function getUserAddress($userId) {
        $aAddressDetails = array(
            'address' => '',
            'city'    => '',
            'state'   => '',
            'pin'     => '',
        );
        $userAddress = $this->_getUserModel()->getUserAddress($userId);
        if (isset($userAddress['userAddress'])) {
            $aAddressDetails = array_only($userAddress['userAddress']->toArray(), ['address', 'city', 'state', 'pin']);
        }
        return $aAddressDetails;
    }

    /**
     * Method to update / merge cart data on user log in
     *
     * @param object $user User Object
     * @return void
     *
     */
    public function handleUserCart(Request $request, $userDetails) {
        if ($userDetails['data']->fk_users_role == \Config::get('appConstants.user_role_id')) {
            $userCartId         = $userDetails['data']->id;
            $userCartAPIKey     = $request->header('alchemy-cart-auth', '');
            if (!empty($userCartAPIKey)) {
                $cartModel          = new Cart($userCartId);
                $cartDataResponse   = $cartModel->getUserCartData($userCartId);
                $cartContent        = $cartDataResponse['data'];
                $userCartPreviousContent = $cartModel->getUserCartData($userCartAPIKey);
                if ($userCartPreviousContent['existing']) {
                    $mergedCartContent = $cartContent->merge($userCartPreviousContent['data']);
                    $count             = 0;
                    foreach ($mergedCartContent as $row) {
                        $count += $row['qty'];
                    }
                    $cartModel->saveUserCartData($userCartId, $mergedCartContent, $count);
                    $cartModel->deleteUserCartData($userCartAPIKey);
                }
            }
        }
    }

    /**
     * Method to check Status of API Token
     * If Token is valid Resends User Data
     *
     * @return Object $serviceResponse
     */
    public function getUserDataByToken(Request $request) {
        $response = $this->responseGenerator;
        try {
            $userDetails = $this->_getCommonModel()->getUserDetailsByToken(JWTAuth::getToken());
            $this->_getUserData($userDetails, FALSE);
            $response->setResponseMessage(trans('messages.success'));
            $response->setResponseStatus(TRUE);
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

}
