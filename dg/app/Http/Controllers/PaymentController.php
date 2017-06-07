<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Auth;
use App\SessionModel;
use App\PaymentModel;
use App\User;
use App\UserMangopay;
use App\CardAddress;
use App\MangopayCard;
use Validator;
use Illuminate\Support\Facades\Log;

/**
 * PaymentController 
 * 
 * PHP version 5.6
 * 
 * @category  Laravel 5.2
 * @package   PaymentController
 * @copyright 2016 
 * @license   http://52.50.219.163/
 * @link      http://52.50.219.163/
 * 
 */
class PaymentController extends Controller {

    /**
     * @var \MangoPay\MangoPayApi
     */
    private $mangopay;

    /**
     *
     * @var App\PaymentModel 
     */
    private $paymentModel = null;

    
    protected $_userModel;
    
    /**
     * 
     * @param \MangoPay\MangoPayApi $mangopay
     */
    public function __construct(\MangoPay\MangoPayApi $mangopay) {
        $this->mangopay = $mangopay;
    }

    /**
     * Function to Instatntiate Payment Model.
     * @return object App\PaymentModel
     */
    private function getPaymentModel() {
        if ($this->paymentModel == null) {
            $this->paymentModel = new PaymentModel($this->mangopay);
        }
        return $this->paymentModel;
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
     * Validate Card.
     * 
     * @package PaymentController
     * @param Request $request
     */
    public function validateCard(Request $request, $userId, $cardIdExisting = NULL) {
        try {
            if(isset($request['error']) && isset($request['errorCode'])){
                return redirect()->route('card.response',['response' => TRUE, 'status' => 'error','message' => 'Some Error Occured!! Please try again later', 'code' => $request['errorCode']]);
            }
            $cardId = $this->_getUserModel()->getUserCardRegistrationField($userId, 'cardId');
            $cardRegister = $this->mangopay->CardRegistrations->Get($cardId->cardId); // card Registration id
            $cardRegister->RegistrationData = isset($request['data']) ? 'data=' . $request['data'] : 'errorCode=' . $request['errorCode'];
            $updatedCardRegister = $this->mangopay->CardRegistrations->Update($cardRegister);
            
            if ($updatedCardRegister->Status != \MangoPay\CardRegistrationStatus::Validated || !isset($updatedCardRegister->CardId)){
                $this->_getUserModel()->updateUserCardStatus($userId, $cardId->cardId, 'ERROR');
                return redirect()->route('card.response',['response' => TRUE, 'status' => 'error','message' => 'Some Error Occured!! Please try again later', 'code' => $updatedCardRegister->Status.'cardId'.$updatedCardRegister->CardId]);
            }
            $this->_getUserModel()->updateUserCardStatus($userId, $cardId->cardId, 'VALIDATED');
            $cardAddressId = $this->_getUserModel()->getUserCardRegistrationField($userId, 'cardAddressId');
            $request['mango_users_card_id'] = $updatedCardRegister->CardId;
            $request['fk_card_addres_id'] = $cardAddressId->cardAddressId;
            $this->_getUserModel()->saveMangopayUserDetails($userId, $request, $cardIdExisting);
            if ($updatedCardRegister->Tag == 'API') {
                return redirect()->route('card.success', ['response' => TRUE, 'status' => 'success', 'message' => 'Card Added successfully']);
            }
            $url = 'customer.checkout';
            if (session()->has('urlPayment')) {
                session()->forget('urlPayment');
                $url = 'customer.dashboard';
            }
            return redirect()->route($url);
        } catch (\MangoPay\Libraries\ResponseException $e) {
            Log::error('createPayIn:: Error' . $e->getMessage());
            return redirect()->route('card.response', ['response' => TRUE, 'status' => 'error', 'code' => $e->getCode(), 'message' => $e->getMessage(), 'detail' => $e->GetErrorDetails()]);
        }
    }
    
    /**
     * Function to render error view.
     * 
     * 
     * @param Request $request
     * @return view
     */
    public function getErrorResponse(Request $request) {
        $data = $request->all();
        return view('customer.payment-error', compact('data'));
    }

    /**
     * Edit Payment page
     * 
     * @package PaymentController
     * @return Object
     */
    public function editPayment(Request $request) {
        try {
            $cardId = '';
            if(isset($request['cardId'])){
                $cardId = $request['cardId'];
            }
            $url = explode("/",\Illuminate\Support\Facades\URL::previous());
            if(end($url) == 'paymentDetails' || end($url) == 'dashboard'){
                session()->put("urlPayment", 1);
            }
            $userId = isset(Auth::user()['id']) ? Auth::user()['id'] : FALSE;
            $response = $this->getPaymentModel()->updateCustomerCard($userId, 'WEB', $cardId);
            if($response['status'] == FALSE){
                return redirect()->back()->withErrors($response['message']);
            }
            $cardRegistrationDetails = $response['cardRegistrationDetails'];
            $userData = $response['userData'];
            $returnUrl = $response['returnUrl'];
            $this->_getUserModel()->updateUserCardStatus($userId, $cardRegistrationDetails->Id, 'CREATED');
            return view('customer.payment-edit', compact('userData', 'cardRegistrationDetails', 'returnUrl', 'cardId'));
        } catch (Exception $ex) {
            Log::error('error in editPayment' . $ex->getMessage());
        }
    }

    /**
     * Save customer Payment Address in database.
     * 
     * @package PaymentController
     * @param Request $request
     * @return array
     */
    public function savePayment(Request $request) {
        try {
            if ($request->isMethod('post')) {
                $validator = Validator::make($request->all(), $this->_getUserModel()->getCardAddressValidation());
                if ($validator->fails()) {
                    return response()->json(['status' => 'error', 'error' => $validator->errors()->all()]);
                }
                $data = $this->_getUserModel()->saveUserCardAddress($request);
                if ($data) {
                    return $data;
                }
                return response()->json(['status' => 'error', 'error' => $validator]);
            }
            return response()->json(['status' => 'error']);
        } catch (Exception $ex) {
            Log::error('error in savePayment' . $ex->getMessage());
        }
    }

    /**
     * One time activity to create new admin user id & wallet Id.
     */
    public function createLegalUserAdmin() {
        $aLegalUserType = config('mangopay.legal_user_type');
        $fname  = env('MANGO_LEGAL_FNAME', '');
        $lname  = env('MANGO_LEGAL_LNAME', '');
        $email  = env('MANGO_LEGAL_EMAIL', '');
        if (empty($email)
            || empty($email)
            || empty($email)
        ) {
            die('Required parameter missing');
        }
        $userData = ['legal_person_email' => $email
            , 'legal_person_name' => $fname
            , 'legal_representative_fname' => $fname
            , 'legal_representative_lname' => $lname
            , 'legal_representative_nationality' => 'GB'
            , 'legal_representative_country_residence' => 'GB'
            , 'legal_person_email' => $email
            , 'business_type' => $aLegalUserType['BUSINESS']];
        $aUserDetails = $this->getPaymentModel()->createLegalUser($userData);
        if ($aUserDetails['status']) {
            $userId = $aUserDetails['data'];
            $userWallet = $this->getPaymentModel()->createWallet($userId->Id);
            if ($userWallet->Id) {
                print_r($userId);
                print_r($userWallet);
                echo "MANGOPAY USER ID : ".$userId->Id.PHP_EOL;
                echo "MANGOPAY USER WALLLET ID : ".$userWallet->Id.PHP_EOL;
                die;
            }
        } else {
            print_r($aUserDetails); die;
        }
        
        
    }
    
    /**
     * Function to add a new card from API.
     * 
     * @param int $userId
     * @return view
     */
    public function addPayment($userId) {
        try {
            $response = $this->getPaymentModel()->updateCustomerCard($userId, 'API');
            if($response['status'] == FALSE || !isset($response['cardRegistrationDetails'])){
                return response()->json(['status' => 'error', 'error' => $response['message']]);
            }
            $cardRegistrationDetails = $response['cardRegistrationDetails'];
            $userData = $response['userData'];
            $returnUrl = $response['returnUrl'];
            $noHeader = 'true';
            $this->_getUserModel()->updateUserCardStatus($userId, $cardRegistrationDetails->Id, 'CREATED');
            return view('customer.payment-edit', compact('userData', 'cardRegistrationDetails', 'returnUrl', 'noHeader'));
        } catch (Exception $ex) {
            Log::error('error in editPayment' . $ex->getMessage());
        }
    }

    /**
     * Method called from MangoPay Webhook
     * When Refund status is changed.
     *
     */
    public function captureRefundStatus(Request $request) {
        $refundId = $request['RessourceId'];
        $response = $this->getPaymentModel()->getRefundDetails($refundId);
    }
}
