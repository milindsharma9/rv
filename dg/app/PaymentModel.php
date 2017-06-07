<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Log;
use Illuminate\Http\Request;
use App\SessionModel;
use MangoPay\MangoPayApi;
use Config;
use App\User;
use App\UserMangopay;
use Illuminate\Support\Facades\Auth;
use App\SalesOrder;
use Illuminate\Support\Facades\DB;
use Exception;
use App\BankDetails;
use App\Http\Helper\CommonHelper;
use App\KycDetails;
use App\Model\OrderStatus;

class PaymentModel extends Model {

    protected $_masterUserId;
    protected $_masterWalletId;
    protected $_masterBankId;
    protected $_userModel;
    /**
     * @var \MangoPay\MangoPayApi
     */
    private $mangopay;

    public function __construct(MangoPayApi $mangoPay) {
        $this->mangopay = $mangoPay;
        //Log::info('PaymentModel:: Instantiated');
        $this->_masterUserId    = env('MANGOPAY_USER');
        $this->_masterWalletId  = env('MANGOPAY_WALLET');
        $this->_masterBankId    = env('MANGOPAY_BANK');
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
     * Create Mangopay User
     *
     * @param array $userData
     * @return object
     *
     */
    public function createMangoUser($userData) {
        try {
            $mangoUser = new \MangoPay\UserNatural();
            $mangoUser->PersonType = "NATURAL";
            $mangoUser->FirstName = !empty($userData['first_name'])?$userData['first_name']:'customer FirstName';
            $mangoUser->LastName = !empty($userData['last_name'])?$userData['last_name']:'customer Last Name';
            $mangoUser->Birthday = isset($userData['dob'])?$userData['dob']:(time() - date('Z'));
            $mangoUser->Nationality = \Config::get('appConstants.nationality');
            $mangoUser->CountryOfResidence = \Config::get('appConstants.residence');
            $mangoUser->Email = $userData['email'];
            //Send the request
            $mangoUser = $this->mangopay->Users->Create($mangoUser);
            Log::info('createMangoUser:: User Created Successfully');
            return $mangoUser;
        } catch (\MangoPay\Libraries\ResponseException $e) {
            Log::error('createMangoUser:: Error' . $e->getMessage());
            return (['status' => 'error', 'code' => $e->getCode(), 'message' => $e->getMessage(), 'detail' => $e->GetErrorDetails()]);
        }
    }

    /*public function createLegalUser($userData) {
        try {
            $legalUser = new \MangoPay\UserLegal();
            $legalUser->Name = "Diageo User";
            $legalUser->LegalPersonType = \MangoPay\LegalPersonType::Business;
            $legalUser->Email = $userData['email'];
            $legalUser->LegalRepresentativeFirstName = $userData['fname'];
            $legalUser->LegalRepresentativeLastName = $userData['lname'];
            $legalUser->LegalRepresentativeBirthday = isset($userData['dob'])?$userData['dob']:(time() - date('Z'));
            $legalUser->LegalRepresentativeNationality = \Config::get('appConstants.nationality');
            $legalUser->LegalRepresentativeCountryOfResidence = \Config::get('appConstants.residence');
            $legalUser = $this->mangopay->Users->Create($legalUser);
            Log::info('createLegalUser:: User Created Successfully');
            return $legalUser; // id => 12622609  //wallet Id => 12622671
        } catch (\MangoPay\Libraries\ResponseException $e) {
            Log::error('createLegalUser:: Error' . $e->getMessage());
            return (['status' => 'error', 'code' => $e->getCode(), 'message' => $e->getMessage(), 'detail' => $e->GetErrorDetails()]);
        }
    }*/

    /**
     * Create ManogoPay Legal user.
     *
     * @param array $userData
     * @return object
     *
     */
    public function createLegalUser($userData) {
        $response = array(
            'status'            => FALSE,
            'message'           => trans('messages.common_error'),
            'data'              => '',
            'mangopay_error'    => ''
        );
        try {
            $legalUser                                          = new \MangoPay\UserLegal();
            $legalUser->Name                                    = $userData['store_name'];
            //$legalUser->LegalPersonType                         = \MangoPay\LegalPersonType::Business;
            $legalUser->LegalPersonType                         = $userData['business_type'];

            $headQAddress1                                       = isset($userData['headquarters_address_1']) ? $userData['headquarters_address_1'] : "";
            $headQAddress2                                       = isset($userData['headquarters_address_2']) ? $userData['headquarters_address_2'] : "";
            $headQCity                                           = isset($userData['headquarters_city']) ? $userData['headquarters_city'] : "";
            $headQRegion                                         = isset($userData['headquarters_region']) ? $userData['headquarters_region'] : "";
            $headQPostcode                                       = isset($userData['headquarters_postcode']) ? $userData['headquarters_postcode'] : "";
            $headQCountry                                        = isset($userData['headquarters_country']) ? $userData['headquarters_country'] : "";

            // As these are optional fields and we donot have proper data to populate it, hence not passing this to mangopay.
            // Adding HardCode Country Value as suggested after discussion
            $headQCountry = config('appConstants.residence');
            
            if (!empty($headQAddress1)
                && (!empty($headQCity))
                && (!empty($headQRegion))
                && (!empty($headQPostcode))
                && (!empty($headQCountry))
            ) {
                $legalUser->HeadquartersAddress                     = new \MangoPay\Address();
                $legalUser->HeadquartersAddress->AddressLine1       = $headQAddress1;
                $legalUser->HeadquartersAddress->AddressLine2       = $headQAddress2;
                $legalUser->HeadquartersAddress->City               = $headQCity;
                $legalUser->HeadquartersAddress->Region             = $headQRegion;
                $legalUser->HeadquartersAddress->PostalCode         = $headQPostcode;
                $legalUser->HeadquartersAddress->Country            = $headQCountry;
            }
            //
            $legalRAddress1                                       = isset($userData['legal_representative_address_1']) ? $userData['legal_representative_address_1'] : "";
            $legalRAddress2                                       = isset($userData['legal_representative_address_2']) ? $userData['legal_representative_address_2'] : "";
            $legalRCity                                           = isset($userData['legal_representative_city']) ? $userData['legal_representative_city'] : "";
            $legalRRegion                                         = isset($userData['legal_representative_region']) ? $userData['legal_representative_region'] : "";
            $legalRPostcode                                       = isset($userData['legal_representative_postcode']) ? $userData['legal_representative_postcode'] : "";
            $legalRCountry                                        = isset($userData['legal_representative_country']) ? $userData['legal_representative_country'] : "";
            
            // As these are optional fields and we donot have proper data to populate it, hence not passing this to mangopay.
            // Adding HardCode Country Value as suggested after discussion
            $legalRCountry = config('appConstants.residence');
            
            // Address is optional for Legal User. But if we add address object all fields are neccessary
            if (!empty($legalRAddress1)
                && (!empty($legalRCity))
                && (!empty($legalRRegion))
                && (!empty($legalRPostcode))
                && (!empty($legalRCountry))
            ) {
                $legalUser->LegalRepresentativeAddress                     = new \MangoPay\Address();
                $legalUser->LegalRepresentativeAddress->AddressLine1       = $legalRAddress1;
                $legalUser->LegalRepresentativeAddress->AddressLine2       = $legalRAddress2;
                $legalUser->LegalRepresentativeAddress->City               = $legalRCity;
                $legalUser->LegalRepresentativeAddress->Region             = $legalRRegion;
                $legalUser->LegalRepresentativeAddress->PostalCode         = $legalRPostcode;
                $legalUser->LegalRepresentativeAddress->Country            = $legalRCountry;
            }
            //
            
            //
            $legalUser->Email                                   = $userData['legal_person_email'];
            $legalUser->LegalRepresentativeFirstName            = $userData['legal_representative_fname'];
            $legalUser->LegalRepresentativeLastName             = $userData['legal_representative_lname'];
            $dob = strtotime($userData['legal_representative_dob_dd'] ."-". $userData['legal_representative_dob_mm'] ."-". $userData['legal_representative_dob_yy']);
            //$dob                                                = isset($userData['legal_representative_dob']) ? strtotime($userData['legal_representative_dob']) : (time() - date('Z'));
            $legalUser->LegalRepresentativeBirthday             = $dob;
            $legalUser->LegalRepresentativeNationality          = $userData['legal_representative_nationality'];
            $legalUser->LegalRepresentativeCountryOfResidence   = $userData['legal_representative_country_residence'];
            $legalUser->LegalRepresentativeEmail                = $userData['legal_person_email'];
            $legalUser                                          = $this->mangopay->Users->Create($legalUser);
            $response['status'] = TRUE;
            $response['message']    = trans('messages.success');
            $response['data']   = $legalUser;
        } catch (\MangoPay\Libraries\ResponseException $e) {
            $response['message']                = $e->getMessage();
            $response['mangopay_error']         = $e->GetErrorDetails();
            $errMsgLog = trans('messages.exception_manogopay') . __METHOD__. "|". $response['message'];
            CommonHelper::event($errMsgLog, CommonHelper::PAYMENT_REGISTER_LOG_FILE, CommonHelper::DAILY);
            CommonHelper::event($e->GetErrorDetails(), CommonHelper::PAYMENT_REGISTER_LOG_FILE, CommonHelper::DAILY);
        }  catch (Exception $ex) {
            $response['message']        = $ex->getMessage();
            $errMsgLog                  = trans('messages.exception') . __METHOD__. "|". $ex->getMessage() . "|" . $ex->getFile() . "|" . $ex->getLine();
            CommonHelper::event($errMsgLog, CommonHelper::PAYMENT_REGISTER_LOG_FILE, CommonHelper::DAILY);
        }
        return $response;
    }

    /**
     * Register a card for transaction.
     *
     * @param int $userId
     * @param string $customData 'mode(WEB/API)|cardId'
     * @return Object
     *
     */
    public function registerCard($userId, $customData = 'WEB', $cardId = NULL) {
        try {
            $cardRegister = new \MangoPay\CardRegistration();
            $cardRegister->UserId = $userId;
            $cardRegister->Tag = $customData.'\\'.$cardId;
            $cardRegister->Currency = \Config::get('appConstants.currency');
//            $cardRegister->CardType = "CB_VISA_MASTERCARD"; //or alternatively MAESTRO or DINERS //Optional field so removed.
            $cardPreRegistration = $this->mangopay->CardRegistrations->Create($cardRegister);
            Log::info('registerCard:: Card Created Successfully');
            return $cardPreRegistration;
        } catch (\MangoPay\Libraries\ResponseException $e) {
            Log::error('registerCard:: Error' . $e->getMessage());
            return (['status' => 'error', 'code' => $e->getCode(), 'message' => $e->getMessage(), 'detail' => $e->GetErrorDetails()]);
        }
    }

    /**
     * Get mango pay user details
     *
     * @param int $userId
     * @return object
     *
     */
    public function getMangoUserById($userId) {
        try {
            return $this->mangopay->Users->Get($userId);
        } catch (\MangoPay\Libraries\ResponseException $e) {
            Log::error('getMangoUserById:: Error' . $e->getMessage());
            return (['status' => 'error', 'code' => $e->getCode(), 'message' => $e->getMessage(), 'detail' => $e->GetErrorDetails()]);
        }
    }

    /**
     * Create Temporary Wallet for a registered user.
     *
     * @param int $userId
     * @return array
     *
     */
    public function createWallet($userId) {
        try {// create temporary wallet for user
            $wallet = new \MangoPay\Wallet();
            $wallet->Owners = array($userId);
            $wallet->Currency = \Config::get('appConstants.currency');
            $wallet->Description = 'Wallet for ' + $userId;
            $createdWallet = $this->mangopay->Wallets->Create($wallet);
            Log::info('createWallet:: Wallet Created Successfully');
            return $createdWallet;
        } catch (\MangoPay\Libraries\ResponseException $e) {
            Log::error('createWallet:: Error' . $e->getMessage());
            return (['status' => 'error', 'code' => $e->getCode(), 'message' => $e->getMessage(), 'detail' => $e->GetErrorDetails()]);
        }
    }
    
    /**
     * Get Wallet details
     *
     * @param int $walletId
     * @return arrray
     *
     */
    public function getWalletDetails($walletId = NULL) {
        try {
            if(NULL == $walletId)
                $walletId = $this->_masterWalletId;
            $createdWallet = $this->mangopay->Wallets->Get($walletId);
            Log::info('getWalletDetails:: Wallet Created Successfully');
            return $createdWallet;
        } catch (\MangoPay\Libraries\ResponseException $e) {
            Log::error('getWalletDetails:: Error' . $e->getMessage());
            return (['status' => 'error', 'code' => $e->getCode(), 'message' => $e->getMessage(), 'detail' => $e->GetErrorDetails()]);
        }
    }

    /**
     * Create Pay in for user.
     *
     * @param int $walletId
     * @param int $userId
     * @param decimal $amount
     * @param string $cardType
     * @param int $cardId
     * @return array
     *
     */
    public function createPayIn($walletId, $userId, $amount, $cardType, $cardId) {
        $response = array(
            'status'    => FALSE,
            'message'   => trans('messages.common_error'),
            'data'      => '',
            'mangopay_error' => ''
        );
        // create pay-in CARD DIRECT
        try {          
//          if(NULL == $walletId)
//                $walletId = $this->_masterWalletId;
            $payIn                                      = new \MangoPay\PayIn();
            $payIn->CreditedUserId                      = $this->_masterUserId;
            $payIn->CreditedWalletId                    = $walletId;
            $payIn->AuthorId                            = $userId;
            $payIn->DebitedFunds                        = new \MangoPay\Money();
            $payIn->DebitedFunds->Amount                = $amount;
            $payIn->DebitedFunds->Currency              = \Config::get('appConstants.currency');
            $payIn->Fees                                = new \MangoPay\Money();
            $payIn->Fees->Amount                        = \Config::get('appConstants.fee');
            $payIn->Fees->Currency                      = \Config::get('appConstants.currency');
            // payment type as CARD
            $payIn->PaymentDetails                      = new \MangoPay\PayInPaymentDetailsCard();
            $payIn->PaymentDetails->CardType            = $cardType;
            // execution type as DIRECT
            $payIn->ExecutionDetails                        = new \MangoPay\PayInExecutionDetailsDirect();
            $payIn->ExecutionDetails->CardId                = $cardId;
            $payIn->ExecutionDetails->SecureModeReturnURL   = route('payment.response');
            $payIn->ExecutionDetails->SecureMode            = CommonHelper::getSecureModeValue($amount/100);
            $createdPayIn                                   = $this->mangopay->PayIns->Create($payIn);
            $response['status'] = TRUE;
            $response['data']   = $createdPayIn;
        } catch (\MangoPay\Libraries\ResponseException $e) {
            $response['message']                = $e->getMessage();
            $response['mangopay_error']         = $e->GetErrorDetails();
            $errMsgLog = trans('messages.exception_manogopay') . __METHOD__. "|". $response['message'];
            CommonHelper::event($errMsgLog, CommonHelper::PAYMENT_LOG_FILE, CommonHelper::DAILY);
            CommonHelper::event($e->GetErrorDetails(), CommonHelper::PAYMENT_LOG_FILE, CommonHelper::DAILY);
        }  catch (Exception $ex) {
            $response['message']        = $ex->getMessage();
            $errMsgLog                  = trans('messages.exception') . __METHOD__. "|". $ex->getMessage() . "|" . $ex->getFile() . "|" . $ex->getLine();
            CommonHelper::event($errMsgLog, CommonHelper::PAYMENT_LOG_FILE, CommonHelper::DAILY);
        }
        return $response;
    }
    
    /**
     * Method to retrieve payin details.
     * 
     * @param Int $payInId
     * @return array
     */
    public function getPayInDetails($payInId) {
        $response = array(
            'status'    => FALSE,
            'message'   => trans('messages.common_error'),
            'data'      => '',
            'mangopay_error' => ''
        );
        try {
            $payInDetails = $this->mangopay->PayIns->Get($payInId);
            $response['status'] = TRUE;
            $response['data']   = $payInDetails;
            $response['message'] = 'Pay In details Fetched Successfully';
        } catch (\MangoPay\Libraries\ResponseException $e) {
            $response['message']                = $e->getMessage();
            $response['mangopay_error']         = $e->GetErrorDetails();
            $errMsgLog = trans('messages.exception_manogopay') . __METHOD__. "|". $response['message'];
            CommonHelper::event($errMsgLog, CommonHelper::PAYMENT_LOG_FILE, CommonHelper::DAILY);
            CommonHelper::event($e->GetErrorDetails(), CommonHelper::PAYMENT_LOG_FILE, CommonHelper::DAILY);
        }
        return $response;
    }

    /**
     * Method to transfer within users wallets
     * Make API Hit to MangoPay Server
     * 
     * @param int $userId
     * @param int $walletId
     * @param decimal $amount
     * @param int $creditWalletId
     * @param int $creditUserId
     * @return array $response
     *
     */
    public function createTransfer($userId, $walletId, $amount, $creditWalletId = NULL, $creditUserId = NULL) {
        $response = array(
            'status'            => FALSE,
            'message'           => trans('messages.common_error'),
            'data'              => '',
            'mangopay_error'    => ''
        );
        try {
             if(NULL == $creditUserId)
                $creditUserId = $this->_masterUserId;
            if(NULL == $creditWalletId)
                $creditWalletId = $this->_masterWalletId;
            $transfer                           = new \MangoPay\Transfer();
            $transfer->AuthorId                 = $userId;
            $transfer->CreditedUserId           = $creditUserId;
            $transfer->DebitedFunds             = new \MangoPay\Money();
            $transfer->DebitedFunds->Currency   = \Config::get('appConstants.currency');
            $transfer->DebitedFunds->Amount     = $amount;
            $transfer->Fees                     = new \MangoPay\Money();
            $transfer->Fees->Currency           = \Config::get('appConstants.currency');
            $transfer->Fees->Amount             = \Config::get('appConstants.fee');
            $transfer->DebitedWalletId          = $walletId;
            $transfer->CreditedWalletId         = $creditWalletId;
            $transfer = $this->mangopay->Transfers->Create($transfer);
            $response['status'] = TRUE;
            $response['data']   = $transfer;
        } catch (\MangoPay\Libraries\ResponseException $e) {
            $response['message']                = $e->getMessage();
            $response['mangopay_error']         = $e->GetErrorDetails();
            $errMsgLog = trans('messages.exception_manogopay') . __METHOD__. "|". $response['message'];
            CommonHelper::event($errMsgLog, CommonHelper::PAYMENT_LOG_FILE, CommonHelper::DAILY);
            CommonHelper::event($e->GetErrorDetails(), CommonHelper::PAYMENT_LOG_FILE, CommonHelper::DAILY);
        } catch (Exception $ex) {
            $response['message']        = $ex->getMessage();
            $errMsgLog                  = trans('messages.exception') . __METHOD__. "|". $ex->getMessage() . "|" . $ex->getFile() . "|" . $ex->getLine();
            CommonHelper::event($errMsgLog, CommonHelper::PAYMENT_LOG_FILE, CommonHelper::DAILY);
        }
        return $response;
    }

    /**
     * Create Bank Account.
     * API hit to MangoPay
     * 
     * @param array $aParams parameters to create Bank Account
     * @return array $response
     * 
     */
    public function createBankAccount(array $aParams) {
        $response = array(
            'status' => FALSE,
            'message' => trans('messages.common_error'),
            'data' => '',
            'mangopay_error' => ''
        );
        try {
            // Extract User Params
            $userId             =  $aParams['userId'];
            $bankType           = $aParams['banktype'];
            $aBankDetailType    = config('appConstants.bank_detail_types');
            
            switch ($bankType) {
                case $aBankDetailType['IBAN']:
                    $response = $this->_createIBANBankAccount($aParams);
                    break;

                case $aBankDetailType['US']:
                    $response = $this->_createUSBankAccount($aParams);
                    break;

                case $aBankDetailType['GB']:
                    $response = $this->_createGBBankAccount($aParams);
                    break;

                case $aBankDetailType['CA']:
                    $response = $this->_createCABankAccount($aParams);
                    break;

                case $aBankDetailType['OTHER']:
                    $response = $this->_createOtherBankAccount($aParams);
                    break;

                default:
                    $response['message'] = trans('messages.invalid_bank_type');
            }
        } catch (Exception $ex) {
            $response['message']        = $ex->getMessage();
            $errMsgLog                  = trans('messages.exception') . __METHOD__. "|". $ex->getMessage() . "|" . $ex->getFile() . "|" . $ex->getLine();
            CommonHelper::event($errMsgLog, CommonHelper::PAYMENT_BANK_LOG_FILE, CommonHelper::DAILY);
        }
        return $response;
     }

    /**
     * Create Pay Out to bank
     *
     * @param decimal $amount
     * @param int $userId
     * @param int $walletId
     * @param int $banckAccountId
     * @return array $response
     *
     */
    protected function _payOut($amount, $userId, $walletId, $banckAccountId) {
        $response = array(
            'status'            => FALSE,
            'message'           => trans('messages.common_error'),
            'data'              => '',
            'mangopay_error'     => ''
        );
        try {
            $amount                                         = round($amount * Config::get('appConstants.poundToPence'),2);
            $payOut                                         = new \MangoPay\PayOut();
            $payOut->AuthorId                               = $userId;
            $payOut->DebitedWalletID                        = $walletId;
            $payOut->DebitedFunds                           = new \MangoPay\Money();
            $payOut->DebitedFunds->Currency                 = \Config::get('appConstants.currency');
            $payOut->DebitedFunds->Amount                   = $amount;
            $payOut->Fees                                   = new \MangoPay\Money();
            $payOut->Fees->Currency                         = \Config::get('appConstants.currency');
            $payOut->Fees->Amount                           = \Config::get('appConstants.fee');
            $payOut->PaymentType                            = \MangoPay\PayOutPaymentType::BankWire;
            $payOut->MeanOfPaymentDetails                   = new \MangoPay\PayOutPaymentDetailsBankWire();
            $payOut->MeanOfPaymentDetails->BankAccountId    = $banckAccountId;
            $payOut = $this->mangopay->PayOuts->Create($payOut);
            //Log::info('payOut:: payout Created Successfully');
            $response['status']     = TRUE;
            $response['message']    = trans('messages.success');
            $response['data']       = $payOut;
        } catch (\MangoPay\Libraries\ResponseException $e) {
            //Log::error('_payOut:: Error' . $e->getMessage());
            $response['message']        = $e->getMessage();
            $response['mangopay_error'] = $e->GetErrorDetails();
            $errMsgLog = trans('messages.exception_manogopay') . __METHOD__. "|". $response['message'];
            CommonHelper::event($errMsgLog, CommonHelper::PAYMENT_PAYOUT_LOG_FILE, CommonHelper::DAILY);
            CommonHelper::event($e->GetErrorDetails(), CommonHelper::PAYMENT_PAYOUT_LOG_FILE, CommonHelper::DAILY);
        } catch (Exception $ex) {
            $response['message']        = $ex->getMessage();
            $errMsgLog                  = trans('messages.exception') . __METHOD__. "|". $ex->getMessage() . "|" . $ex->getFile() . "|" . $ex->getLine();
            CommonHelper::event($errMsgLog, CommonHelper::PAYMENT_PAYOUT_LOG_FILE, CommonHelper::DAILY);
        }
        return $response;
    }
    
    /**
     * Fetch all the details of card on the basis of card Id.
     *
     * @param int $cardId
     * @return array
     *
     */
    public function getCardDetails($cardId) {
        try {
            $cardRegisterPut = $this->mangopay->Cards->Get($cardId);
            Log::info('getCardDetails:: Card Fetched Successfully');
            return $cardRegisterPut;
        } catch (\MangoPay\Libraries\ResponseException $e) {
            Log::error('registerCard:: Error' . $e->getMessage());
            return (['status' => 'error', 'code' => $e->getCode(), 'message' => $e->getMessage(), 'detail' => $e->GetErrorDetails()]);
        }
    }
    
    /**
     * Fetch card Details on basis of User Id
     * 
     * @param int $userId
     * @return array
     *
     */
    public function getUserCardDetails($userId) {
        $cardData       = $this->_getUserModel()->getUserCardId($userId);
        $cardDetails    = [];
        if ($cardData['hasCard'] == TRUE) {
            foreach ($cardData['cardData'] as $key => $value) {
               $cardDetails[] =  self::getCardDetails($value['mango_users_card_id']);
            }
            return ['hasCard' => TRUE, 'cardDetails' => json_encode($cardDetails), 
                    'cardData' => $cardData];
        } else {
            return ['hasCard' => FALSE, 'cardDetails' => json_encode([])];
        }
    }
    
    
    /**
     * Method to deduct Payment from User Card.
     * Hits two MangoPay API
     * 1) For tranfer amount from User Card to user wallet
     * 2) For transfer amount from user Wallet to Admin Wallet
     * 
     * @param decimal $amount Amount to be deducted
     * @param int $userId User Id
     * @return array $response
     *
     */
    public function getUserPayment($amount, $userId) {
        $response = array(
            'status'            => FALSE,
            'message'           => trans('messages.common_error'),
            'data'              => array(
                                        'transferData'      => '',
                                        'payInDAta'         => '',
                                        'code'              => '',
                                        'payStatus'         => '', 
                                    ),
            'mangopay_error'    => '',
        );
        try {
            $amount         = round($amount * Config::get('appConstants.poundToPence'),2); // multiply by 100 as recommended by Mangopay
            //$userId         = isset(Auth::user()['id']) ? Auth::user()['id'] : FALSE;
            $userData       = $this->_getUserModel()->getmangoUser($userId);
            $mangoUser      = $userData['mangoUser'];
            $userData       = $this->_getUserModel()->getmangoCardUser($userId);
            $mangoCardUser  = $userData['mangoCardUser'];
            $cardId         = 0;
            foreach ($mangoCardUser as $key => $value) {
                if($value['is_default'] == '1'){
                    $cardId = $value['mango_users_card_id'];
                    break;
                }
            }
            if( $cardId == 0){
                $response['message'] = 'Card Not Found. Please try again later.';
                return $response;
            }
            $walletId       = $this->_masterWalletId;
            $card           = self::getCardDetails($cardId);
            //create pay in
            $createdPayInResponse   = self::createPayIn($mangoUser['mango_users_wallet_id'], $mangoUser['mango_users_id'], $amount, $card->CardType, $card->Id);
            if ($createdPayInResponse['status']) {
                $createdPayIn           = $createdPayInResponse['data'];
                if ($createdPayIn->Status == \MangoPay\PayInStatus::Succeeded) {
                    $response['status'] = true;
                    $response['message'] = trans('messages.success');
                    $response['data'] = array(                      
                      'payInData' => $createdPayIn
                    );
                } else if ($createdPayIn->Status == \MangoPay\PayInStatus::Created) {
                    if (isset($createdPayIn->ExecutionDetails->SecureModeReturnURL) && isset($createdPayIn->ExecutionDetails->SecureModeRedirectURL)) {
                        $response['status'] = true;
                        $response['message'] = trans('messages.success');
                        $response['data'] = array(
                            'payInData' => $createdPayIn
                        );
                    }else{
                        $response['data'] = array(
                            'code'      => '0000000000',
                            'payStatus' => trans('messages.payment_error_3d_secure_not_allowed'),
                        );
                        $response['message'] = Config::get('errorConstants.' . $response['data']['code']);
                    }
                } else {
                    $response['data'] = array(
                            'code'      => $createdPayIn->ResultCode,
                            'payStatus' => $createdPayIn->Status
                        );
                    $response['message'] = Config::get('errorConstants.' . $response['data']['code']);
                }
            }
        } catch (Exception $ex) {
            $errMsgLog = trans('messages.exception') . __METHOD__. "|". $ex->getMessage() . "|" . $ex->getFile() . "|" . $ex->getLine();
            CommonHelper::event($errMsgLog, CommonHelper::PAYMENT_LOG_FILE, CommonHelper::DAILY);
            $response['data'] = array(
                        'code'      => $ex->getCode(),
                    );
            $response['message'] = $ex->getMessage();
        }
        CommonHelper::event($response, CommonHelper::PAYMENT_LOG_FILE, CommonHelper::DAILY);
        return $response;
    }
    
    /**
     * Methods return's child store's Id whose vendor has been paid.
     * 
     * @return Array
     */
    public function getChildStoreIdOfPaidStores() {
        $paidStoreId = $childStoreId = [];
        $paidStore = DB::table('store_payment AS sp')
                ->select('fk_store_id')
                ->whereRaw('YEAR(sp.updated_at) = YEAR(NOW())')
                ->whereRaw('MONTH(sp.updated_at) = MONTH(NOW()) - 1')
                ->get();
        $paidStoreArray = json_decode(json_encode($paidStore), TRUE);
        foreach ($paidStoreArray as $key => $value) {
                $paidStoreId [] = $value['fk_store_id'];
        }
        $salesOrderObj = new SalesOrder();
        foreach ($paidStoreId as $key => $value) {
            $childStoreId[] = $salesOrderObj->getStoreChildId($value);
        }
        return $childStoreId;
    }
    
    /**
     * Get pending store whose payemnt for current month is pending.
     * 
     * @param int $storeId
     * @return Array
     *
     */
    public function getPendingPayment($storeId = NULL) {
        $childStoreId = self::getChildStoreIdOfPaidStores();     
        $pendingStore = DB::table('sales_order_item AS SOI')
                ->addSelect('SD.store_name')
                ->addSelect('SD.fk_users_id AS store_user_id')
                ->addSelect('SD.fk_parent_id AS store_parent_id')
                ->addSelect('users.email')
                ->addSelect(DB::raw('SUM(SOI.store_price) AS totalPrice'))
                ->leftJoin('sub_store_details AS SD', 'SD.fk_users_id', '=', 'SOI.fk_store_id')
                ->join('users', 'users.id', '=', 'SOI.fk_store_id')
                ->whereRaw('YEAR(SOI.updated_at) = YEAR(NOW())')
                ->whereRaw('MONTH(SOI.updated_at) = MONTH(NOW()) -1 ')
                ->where('SOI.fk_order_status_id', '=', 2)
                ->where('SOI.fk_driver_id', '!=', 1)
                ->where('SOI.fk_store_id', '!=', 1)
                ->whereNotIn('SOI.fk_store_id',$childStoreId)
                ->groupBy('SOI.fk_store_id');
        if(NULL != $storeId)
            $pendingStore = $pendingStore->whereIn('SOI.fk_store_id', $storeId);
        $pendingStore = $pendingStore->get();
        return $pendingStore;
    }
    
    /**
     * Method will generate data for pending payment vendors.
     * 
     * @param Int $storeId
     * @return Array
     */
    public function generatePendingPaymentData($storeId = NULL) {
        $pendingStore = self::getPendingPayment($storeId);
        $result = [];
        foreach ($pendingStore as $key => $storeDetails) {
            if($storeDetails->store_parent_id == 0) {
                $result[$storeDetails->store_user_id]['email'] = $storeDetails->email;
                $result[$storeDetails->store_user_id]['store_name'] = $storeDetails->store_name;
                $result[$storeDetails->store_user_id]['price'] = $storeDetails->totalPrice;
                $result[$storeDetails->store_user_id]['totalPrice'] = $storeDetails->totalPrice;
                $result[$storeDetails->store_user_id]['id'] = $storeDetails->store_user_id;
            } else {
                $result[$storeDetails->store_parent_id]['store_details'][] = [
                    'store_id' => $storeDetails->store_user_id,
                    'email' => $storeDetails->email,
                    'price' => $storeDetails->totalPrice];
                $result[$storeDetails->store_parent_id]['totalPrice'] += $storeDetails->totalPrice;
            }
        }
        return $result;
    }
    
    /**
     * 
     * Function to get store details whose payemnt is released this month
     * 
     * @param int $storeId
     * @param boolean $archived
     * 
     * @return array
     */
    public function getReleasePaymentInfo($storeId = NULL) {
        $releasedStore = DB::table('store_payment AS SP')
                ->addSelect('SD.store_name')
                ->addSelect('SP.fk_order_id AS storeOrderId')
                ->addSelect('SO.order_id AS orderId')
                ->addSelect('users.email')
                ->addSelect(DB::raw('SUM(SP.amount) AS totalPrice'))
                ->addSelect(DB::raw("DATE_FORMAT(SP.updated_at,'%d %b,%Y') AS date"))
                ->addSelect(DB::raw("SP.id AS payment_id"))
                ->addSelect(DB::raw("DATE_FORMAT(SP.updated_at,'%r') AS time"))
                ->addSelect(DB::raw("MONTH(SP.updated_at) AS month"))
                ->leftJoin('sub_store_details AS SD', 'SD.fk_users_id', '=', 'SP.fk_store_id')
                ->leftJoin('sales_order AS SO', 'SO.id_sales_order', '=', 'SP.fk_order_id')
                ->join('users', 'users.id', '=', 'SP.fk_store_id');
        if (NULL != $storeId){
            $releasedStore = $releasedStore->addSelect('SP.fk_store_id AS id')
                    ->where('SP.parent_store_id', '=', $storeId)
                    ->groupBy('SP.fk_order_id');
        }else{
            $releasedStore = $releasedStore
                    ->addSelect('SP.parent_store_id AS id')
                    ->groupBy('SP.parent_store_id');
        }
        $releasedStore = $releasedStore->orderBy('SP.updated_at', 'DESC')->get();
        return $releasedStore;
    }

    /**
     * Get mango user details
     * 
     * @param type $userId
     * @return mixed
     * 
     */
    public function getMangoUser($userId) {
        $mangoUserData = $this->_getUserModel()->getmangoUser($userId);
        if ($mangoUserData['mangoUser'] == NULL) {
            //create new natural user,
            $mangoUser = self::createMangoUser($mangoUserData);
            if (!is_object($mangoUser) && $mangoUser['status'] == 'error') {
                return redirect()->back()->withErrors($mangoUser['message']);
            }
            $mangoUserId = $mangoUser->Id;
            // create wallet for that user.
            $walletDetails = self::createWallet($mangoUserId);
            $walletId = $walletDetails->Id;
            UserMangopay::create([
                'fk_users_id' => $userId,
                'mango_users_id' => $mangoUserId,
                'mango_users_wallet_id' => $walletId]);
        }
        return $this->_getUserModel()->getmangoUser($userId);
    }
    
    /**
     * Function to release store payment
     * 
     * @param int $storeId
     * @return boolean
     *
     */
//    Outdated
//    public function releaseStorePayment($storeId) {
//        $salesOrderObj = new SalesOrder();
//        $storeChild = $salesOrderObj->getStoreChildId($storeId);
//        $storeDetails = self::generatePendingPaymentData($storeChild);
//        $userDetails = $this->getMangoUser($storeId);
//        $mangoUserId = $userDetails['mangoUser']['mango_users_id'];
//        $walletId = $userDetails['mangoUser']['mango_users_wallet_id'];
//        if(isset($storeDetails[$storeId]['totalPrice'])){
//            $amount = round($storeDetails[$storeId]['totalPrice'] * Config::get('appConstants.poundToPence'),2);
//            $transferResponse = $this->createTransfer($this->_masterUserId, $this->_masterWalletId, $amount, $walletId, $mangoUserId);
//            if ($transferResponse['status']) {
//                $transfer = $transferResponse['data'];
//                if($transfer->Status == \MangoPay\PayInStatus::Succeeded) {
//                    $updateData = [
//                        'fk_store_id' => $storeId, 
//                        'fk_order_id' => 'orderId',
//                        'payeeWalletId' => $walletId, 
//                        'payerMWalletId' => $this->_masterWalletId,
//                        'amount' => $amount, 'status' => $transfer->Status,
//                        'rawData' =>  json_encode($transfer)];
//                    //update Table.
//                    return $this->updateStorePayment($updateData);
//                }
//            }
//            return FALSE;
//        }else{
//            return FALSE;
//        }
//    }
    
    /**
     * Function to release store payment
     * 
     * @param int $storeId
     * @param int $orderId
     * @param int $payment
     * @return boolean
     *
     */
    public function releaseStorePayment($parentId, $orderId, $payment, $storeId) {
        $amount = round($payment * Config::get('appConstants.poundToPence'), 2);
        //check if payment is already done.
        $checkPayment = DB::table('store_payment AS SP')
                ->where('fk_store_id','=', $storeId)
                ->where('fk_order_id', '=', $orderId)
                ->where('amount' , '=', $amount)->get();

        if(!empty($checkPayment)){
            return FALSE;
        }

        $userStoreDetails = $this->getMangoUser($parentId);
        $mangoUserId = $userStoreDetails['mangoUser']['mango_users_id'];
        $walletId = $userStoreDetails['mangoUser']['mango_users_wallet_id'];
        
//      Obselete Logic is now changed.
//        $consumerData = DB::table('sales_order AS SO')
//                ->select('fk_users_id', 'total')
//                ->where('id_sales_order','=', $orderId)
//                ->first();
//        $userId = $consumerData->fk_users_id;
//        if(!isset($consumerData->fk_users_id)){
//            return FALSE;
//        }
//        $consumerDetails = $this->getMangoUser($userId);
//        $consumerMangoUserId = $consumerDetails['mangoUser']['mango_users_id'];
//        $consumerWalletId = $consumerDetails['mangoUser']['mango_users_wallet_id'];
//        $transferResponse = $this->createTransfer($consumerMangoUserId, $consumerWalletId, $amount, $walletId, $mangoUserId);
        $transferResponse = $this->createTransfer($this->_masterUserId, $this->_masterWalletId, $amount, $walletId, $mangoUserId);
        if ($transferResponse['status']) {
            $transfer = $transferResponse['data'];
            if ($transfer->Status == \MangoPay\PayInStatus::Succeeded) {
            $updateData = [
                    'parent_store_id' => $parentId,
                    'fk_store_id' => $storeId,
                    'fk_order_id' => $orderId,
                    'payeeWalletId' => $walletId,
                    'payerMWalletId' => $this->_masterWalletId,
                    'amount' => $amount, 'status' => $transfer->Status,
                    'rawData' => json_encode($transfer)];
                //update Table.
                return $this->updateStorePayment($updateData);
            }else {
                return FALSE;
            }
        }
        return FALSE;
    }
    
    /**
     * Function to release admin payment
     * @param Int $orderId
     * @param float $amount
     * @return boolean
     */
    public function releaseAdminPayment($orderId, $amount) {
        $checkAdminPayment = DB::table('admin_payment AS ap')
                ->where('fk_order_id', '=', $orderId)
                ->where('amount', '=', $amount)
                ->where('status', '=', \MangoPay\PayInStatus::Succeeded)
                ->first();
        if(isset($checkAdminPayment) && isset($checkAdminPayment->id)){
            return TRUE;
        }
        $consumerData = DB::table('sales_order AS SO')
                ->select('fk_users_id', 'total')
                ->where('id_sales_order', '=', $orderId)
                ->first();
        if (!isset($consumerData->fk_users_id)) {
            return FALSE;
        }
        $userId = $consumerData->fk_users_id;
        $consumerDetails = $this->getMangoUser($userId);
        $consumerMangoUserId = $consumerDetails['mangoUser']['mango_users_id'];
        $consumerWalletId = $consumerDetails['mangoUser']['mango_users_wallet_id'];
        //transfer to admin wallet
        $adminAmount = round($amount * Config::get('appConstants.poundToPence'), 2);
        if ($adminAmount != 0.00) {
            $transferResponse = $this->createTransfer($consumerMangoUserId, $consumerWalletId, $adminAmount, $this->_masterWalletId, $this->_masterUserId);
            if ($transferResponse['status']) {
                $transfer = $transferResponse['data'];
                if ($transfer->Status == \MangoPay\PayInStatus::Succeeded) {
                    $updateData = [
                        'fk_order_id' => $orderId,
                        'payeeWalletId' => $this->_masterWalletId,
                        'payerMWalletId' => $consumerWalletId,
                        'amount' => $amount, 
                        'status' => $transfer->Status,
                        'rawData' => json_encode($transfer)];
                    //update Table.
                    return $this->updateAdminPayment($updateData);
                }
            }
            return FALSE;
        }
        return FALSE;
    }

    /**
     * Function to update payment status
     *
     * @param array $data
     * @return int $paymentId
     *
     */
    protected function updateStorePayment($data) {
        $insertData          = array(
            'parent_store_id'=> $data['parent_store_id'],
            'fk_order_id'    => $data['fk_order_id'],
            'fk_store_id'    => $data['fk_store_id'],
            'payeeWalletId'  => $data['payeeWalletId'],
            'payerMWalletId' => $data['payerMWalletId'],
            'amount'         => round($data['amount']/ Config::get('appConstants.poundToPence'),2),
            'status'         => $data['status'],
            'rawData'        => $data['rawData'],
            'created_at'     => CommonHelper::getCurrentDateTime(),
            'updated_at'     => CommonHelper::getCurrentDateTime()
        );
        $paymentId = DB::table('store_payment')->insertGetId($insertData);
        return $paymentId;
    }
    
    /**
     * Function to update payment status
     *
     * @param array $data
     * @return int $paymentId
     *
     */
    protected function updateAdminPayment($data) {
        $insertData          = array(
            'fk_order_id'    => $data['fk_order_id'],
            'payeeWalletId'  => $data['payeeWalletId'],
            'payerMWalletId' => $data['payerMWalletId'],
            'amount'         => round($data['amount']/ Config::get('appConstants.poundToPence'),2),
            'status'         => $data['status'],
            'rawData'        => $data['rawData'],
            'created_at'     => CommonHelper::getCurrentDateTime(),
            'updated_at'     => CommonHelper::getCurrentDateTime()
        );
        $paymentId = DB::table('admin_payment')->insertGetId($insertData);
        return $paymentId;
    }

    /**
     * Wrapper Method to create bank account of user
     * 
     * @param int $userId
     * @param array $aParam
     * @return arrray $response
     *
     */
    public function createUserBankAccount($userId, $aParam) {
        $response = array(
            'status'    => FALSE,
            'message'   => trans('messages.common_error'),
        );
        try {
            $userMangoId        = $this->_masterUserId;
            $userRole           = isset(Auth::user()['fk_users_role']) ? Auth::user()['fk_users_role'] : FALSE;
            if ($userRole != \Config::get('appConstants.admin_role_id')) {
                $userData           = $this->_getUserModel()->getmangoUser($userId);
                $userMangoDetails   = $userData['mangoUser'];
                $userMangoId        = $userMangoDetails['mango_users_id'];
            }
            $aParam['userId']   = $userMangoId;
            $apiResponse        = $this->createBankAccount($aParam);
            if ($apiResponse['status']) {
                $bankDetailModel        = new BankDetails();
                $accoundId              = $apiResponse['data']->Id;
                $saveDbResponse         = $bankDetailModel->saveUserBankDetails($userId, $accoundId);
                $response['status']     = $saveDbResponse['status'];
                $response['message']    = $saveDbResponse['message'];
            } else {
                $response['message'] = $apiResponse['message'];
            }
        } catch (Exception $ex) {
            $response['message'] = $ex->getMessage()."|".$ex->getFile()."|".$ex->getLine();
        }
        return $response;
    }

    /**
     * Fetch bank Account Deatils of user.
     * Makes MongoPay API Hit
     * 
     * @param int $userId
     * @param int $bankAccountId
     * @return array $response
     * 
     */
    public function GetBankAccount($userId, $bankAccountId) {
        $response = array(
            'status'            => FALSE,
            'message'           => trans('messages.common_error'),
            'data'              => array(
                'owner_name'        => "",
                'account_number'    => "",
                'type'              => "",
                ),
            'mangopay_error'    => ''
        );
        try {
            $userMangoId        = $this->_masterUserId;
            $userRole           = isset(Auth::user()['fk_users_role']) ? Auth::user()['fk_users_role'] : FALSE;
            if ($userRole != \Config::get('appConstants.admin_role_id')) {
                $userData           = $this->_getUserModel()->getmangoUser($userId);
                $userMangoDetails   = $userData['mangoUser'];
                $userMangoId        = $userMangoDetails['mango_users_id'];
            }
            $bankAccount    = $this->mangopay->Users->GetBankAccount($userMangoId, $bankAccountId);
            $bankDetails    = array(
                'owner_name'        => $bankAccount->OwnerName,
                'account_number'    => isset($bankAccount->Details->AccountNumber) ? $bankAccount->Details->AccountNumber : $bankAccount->Details->IBAN,
                'type'              => $bankAccount->Type,
            );
            $response['data']       = $bankDetails;
            $response['message']    = trans('messages.success');
            $response['status']     = TRUE;
        } catch (\MangoPay\Libraries\ResponseException $e) {
            $response['message']        = $e->getMessage();
            $response['mangopay_error'] = $e->GetErrorDetails();
            $errMsgLog = trans('messages.exception_manogopay') . __METHOD__. "|". $response['message'];
            CommonHelper::event($errMsgLog, CommonHelper::PAYMENT_BANK_LOG_FILE, CommonHelper::DAILY);
            CommonHelper::event($e->GetErrorDetails(), CommonHelper::PAYMENT_BANK_LOG_FILE, CommonHelper::DAILY);
        } catch (Exception $ex) {
            $response['message']        = $ex->getMessage();
            $errMsgLog                  = trans('messages.exception') . __METHOD__. "|". $ex->getMessage() . "|" . $ex->getFile() . "|" . $ex->getLine();
            CommonHelper::event($errMsgLog, CommonHelper::PAYMENT_BANK_LOG_FILE, CommonHelper::DAILY);
        }
        return $response;
    }

    /**
     * Fetch wallet details on the basis of userId
     * Makes MongoPay API Hit
     * 
     * @param int $userId
     * @return array $response
     * 
     */
    public function getWalletDetail($userId, $fetchUserDetails = true) {
        $response = array(
            'status'            => FALSE,
            'message'           => trans('messages.common_error'),
            'data'              => '',
            'mangopay_error'    => ''
        );
        try {
            $walletId           = $this->_masterWalletId;
            //$userRole           = isset(Auth::user()['fk_users_role']) ? Auth::user()['fk_users_role'] : FALSE;
            if ($fetchUserDetails) {
                $userData           = $this->_getUserModel()->getmangoUser($userId);
                $userMangoDetails   = $userData['mangoUser'];
                $walletId           = $userMangoDetails['mango_users_wallet_id'];
            }
            $walletDetails  = $this->mangopay->Wallets->Get($walletId);
            $response['data']       = $walletDetails;
            $response['message']    = trans('messages.success');
            $response['status']     = TRUE;
            Log::info('getWalletDetails:: Success');
        } catch (\MangoPay\Libraries\ResponseException $e) {
            $response['message']        = $e->getMessage();
            $response['mangopay_error'] = $e->GetErrorDetails();
            $errMsgLog = trans('messages.exception_manogopay') . __METHOD__. "|". $response['message'];
            CommonHelper::event($errMsgLog, CommonHelper::PAYMENT_LOG_FILE, CommonHelper::DAILY);
            CommonHelper::event($e->GetErrorDetails(), CommonHelper::PAYMENT_LOG_FILE, CommonHelper::DAILY);
        } catch (Exception $ex) {
            $response['message']        = $ex->getMessage();
            $errMsgLog                  = trans('messages.exception') . __METHOD__. "|". $ex->getMessage() . "|" . $ex->getFile() . "|" . $ex->getLine();
            CommonHelper::event($errMsgLog, CommonHelper::PAYMENT_LOG_FILE, CommonHelper::DAILY);
        }
        return $response;
    }

    /**
     * Method to transfer amount from wallet to bank account.
     * 
     * @param decimal $amount
     * @return array $response
     * 
     */
    public function payout($amount, $userId, $fetchUserDetails = true) {
        $response = array(
            'status'            => FALSE,
            'message'           => trans('messages.common_error'),
            'data'              => '',
            'mangopay_error'    => ''
        );
        try {
            $bankDetailModel    = new BankDetails();
            //$userId             = isset(Auth::user()->id) ? Auth::user()->id: NULL;
            $userBankDetails    = $bankDetailModel->getUserBankDetails($userId);
            if ($userBankDetails['hasAccount']) {
                $userMangoBankAccountId = $userBankDetails['details']['userMbankAccId'];
                $walletId           = $this->_masterWalletId;
                $userMangoId        = $this->_masterUserId;
                //$userRole           = isset(Auth::user()['fk_users_role']) ? Auth::user()['fk_users_role'] : FALSE;
                if ($fetchUserDetails) {
                    $userData           = $this->_getUserModel()->getmangoUser($userId);
                    $userMangoDetails   = $userData['mangoUser'];
                    $walletId           = $userMangoDetails['mango_users_wallet_id'];
                    $userMangoId        = $userMangoDetails['mango_users_id'];
                }
                $payOutResponse             = $this->_payOut($amount, $userMangoId, $walletId, $userMangoBankAccountId);
                if ($payOutResponse['status']) {
                    $transactionId = $payOutResponse['data']->Id;
                    $response = $bankDetailModel->savePayoutTransactionDetails($userId, $walletId, $userMangoBankAccountId, $amount
                            , $payOutResponse['data']);
                    $response['data'] = array(
                        'amount' => $amount,
                        'transaction_id' => $transactionId,
                        'raw_response' => $payOutResponse['data']->ResultMessage,
                    );
                } else {
                    $response = $payOutResponse;
                }
            } else {
                $response['message']        = "No bank details found";
            }
        } catch (Exception $ex) {
            $response['message']        = $ex->getMessage();
            $errMsgLog                  = trans('messages.exception') . __METHOD__. "|". $ex->getMessage() . "|" . $ex->getFile() . "|" . $ex->getLine();
            CommonHelper::event($errMsgLog, CommonHelper::PAYMENT_PAYOUT_LOG_FILE, CommonHelper::DAILY);
        }
        return $response;
    }

    /**
     * Create Bank Account type IBAN.
     * API hit to MangoPay
     *
     * @param array $aParams parameters to create Bank Account
     * @return array $response
     *
     */
    protected function _createIBANBankAccount($aParams) {
        $response = array(
            'status'                => FALSE,
            'message'               => trans('messages.common_error'),
            'data'                  => '',
            'mangopay_error'        => ''
        );
        try {
            // Extract User Params
            $userId                                    = $aParams['userId'];
            $bankAccount                               = new \MangoPay\BankAccount();
            $bankAccount->Tag                          = "IBAN";
            $bankAccount->OwnerAddress                 = new \MangoPay\Address();
            $bankAccount->OwnerAddress->AddressLine1   = $aParams['owner_add'];
            $bankAccount->OwnerAddress->AddressLine2   = '';
            $bankAccount->OwnerAddress->City           = $aParams['owner_City'];
            $bankAccount->OwnerAddress->Region         = $aParams['owner_Country'];
            $bankAccount->OwnerAddress->PostalCode     = $aParams['owner_PostalCode'];
            $bankAccount->OwnerAddress->Country        = $aParams['owner_Country'];
            $bankAccount->OwnerName                    = $aParams['owner_name'];
            $bankAccount->Details                      = new \MangoPay\BankAccountDetailsIBAN();
            $bankAccount->Details->IBAN                = $aParams['IBAN'];
            $bankAccount->Details->BIC                 = $aParams['bic'];
            $bankAccountResponse                        = $this->mangopay->Users->CreateBankAccount($userId, $bankAccount);
            $response['status']     = TRUE;
            $response['message']    = trans('messages.success');
            $response['data']       = $bankAccountResponse;
        } catch (\MangoPay\Libraries\ResponseException $e) {
            $response['message']        = $e->getMessage();
            $response['mangopay_error'] = $e->GetErrorDetails();
            $errMsgLog = trans('messages.exception_manogopay') . __METHOD__. "|". $response['message'];
            CommonHelper::event($errMsgLog, CommonHelper::PAYMENT_BANK_LOG_FILE, CommonHelper::DAILY);
            CommonHelper::event($e->GetErrorDetails(), CommonHelper::PAYMENT_BANK_LOG_FILE, CommonHelper::DAILY);
        } catch (Exception $ex) {
            $response['message']        = $ex->getMessage();
            $errMsgLog                  = trans('messages.exception') . __METHOD__. "|". $ex->getMessage() . "|" . $ex->getFile() . "|" . $ex->getLine();
            CommonHelper::event($errMsgLog, CommonHelper::PAYMENT_BANK_LOG_FILE, CommonHelper::DAILY);
        }
        return $response;
    }

    /**
     * Create Bank Account type US.
     * API hit to MangoPay
     *
     * @param array $aParams parameters to create Bank Account
     * @return array $response
     *
     */
    protected function _createUSBankAccount($aParams) {
        $response = array(
            'status'                => FALSE,
            'message'               => trans('messages.common_error'),
            'data'                  => '',
            'mangopay_error'        => ''
        );
        try {
            // Extract User Params
            $userId                                     = $aParams['userId'];
            $bankAccount                               = new \MangoPay\BankAccount();
            $bankAccount->Tag                          = "US";
            $bankAccount->OwnerAddress                 = new \MangoPay\Address();
            $bankAccount->OwnerAddress->AddressLine1   = $aParams['owner_add'];
            $bankAccount->OwnerAddress->AddressLine2   = '';
            $bankAccount->OwnerAddress->City           = $aParams['owner_City'];
            $bankAccount->OwnerAddress->Region         = $aParams['owner_Country'];
            $bankAccount->OwnerAddress->PostalCode     = $aParams['owner_PostalCode'];
            $bankAccount->OwnerAddress->Country        = $aParams['owner_Country'];
            $bankAccount->OwnerName                    = $aParams['owner_name'];
            $bankAccount->Details                      = new \MangoPay\BankAccountDetailsUS();
            $bankAccount->Details->AccountNumber       = $aParams['AccountNumber'];
            $bankAccount->Details->ABA                 = $aParams['ABA'];
            $bankAccount->Details->DepositAccountType  = $aParams['DepositAccountType'];
            $bankAccountResponse                       = $this->mangopay->Users->CreateBankAccount($userId, $bankAccount);
            //Send the request
            $bankAccountResponse                        = $this->mangopay->Users->CreateBankAccount($userId, $bankAccount);

            $response['status']     = TRUE;
            $response['message']    = trans('messages.success');
            $response['data']       = $bankAccountResponse;
        } catch (\MangoPay\Libraries\ResponseException $e) {
            $response['message']        = $e->getMessage();
            $response['mangopay_error'] = $e->GetErrorDetails();
            $errMsgLog = trans('messages.exception_manogopay') . __METHOD__. "|". $response['message'];
            CommonHelper::event($errMsgLog, CommonHelper::PAYMENT_BANK_LOG_FILE, CommonHelper::DAILY);
            CommonHelper::event($e->GetErrorDetails(), CommonHelper::PAYMENT_BANK_LOG_FILE, CommonHelper::DAILY);
        } catch (Exception $ex) {
            $response['message']        = $ex->getMessage();
            $errMsgLog                  = trans('messages.exception') . __METHOD__. "|". $ex->getMessage() . "|" . $ex->getFile() . "|" . $ex->getLine();
            CommonHelper::event($errMsgLog, CommonHelper::PAYMENT_BANK_LOG_FILE, CommonHelper::DAILY);
        }
        return $response;
    }

    /**
     * Create Bank Account type GB.
     * API hit to MangoPay
     *
     * @param array $aParams parameters to create Bank Account
     * @return array $response
     *
     */
    protected function _createGBBankAccount($aParams) {
        $response = array(
            'status'                => FALSE,
            'message'               => trans('messages.common_error'),
            'data'                  => '',
            'mangopay_error'        => ''
        );
        try {
            // Extract User Params
            
            $userId                                    = $aParams['userId'];
            $bankAccount                               = new \MangoPay\BankAccount();
            $bankAccount->Tag                          = "GB";
            $bankAccount->OwnerAddress                 = new \MangoPay\Address();
            $bankAccount->OwnerAddress->AddressLine1   = $aParams['owner_add'];
            $bankAccount->OwnerAddress->AddressLine2   = '';
            $bankAccount->OwnerAddress->City           = $aParams['owner_City'];
            $bankAccount->OwnerAddress->Region         = $aParams['owner_Country'];
            $bankAccount->OwnerAddress->PostalCode     = $aParams['owner_PostalCode'];
            $bankAccount->OwnerAddress->Country        = $aParams['owner_Country'];
            $bankAccount->OwnerName                    = $aParams['owner_name'];
            $bankAccount->CreationDate                 = time();
            $bankAccount->Details                      = new \MangoPay\BankAccountDetailsGB();
            $bankAccount->Details->SortCode            = $aParams['SortCode'];
            $bankAccount->Details->AccountNumber       = $aParams['AccountNumber'];
            $bankAccountResponse                       = $this->mangopay->Users->CreateBankAccount($userId, $bankAccount);
            
            $response['status']     = TRUE;
            $response['message']    = trans('messages.success');
            $response['data']       = $bankAccountResponse;
        } catch (\MangoPay\Libraries\ResponseException $e) {
            $response['message']        = $e->getMessage();
            $response['mangopay_error'] = $e->GetErrorDetails();
            
            $errMsgLog = trans('messages.exception_manogopay') . __METHOD__. "|". $response['message'];
            CommonHelper::event($errMsgLog, CommonHelper::PAYMENT_BANK_LOG_FILE, CommonHelper::DAILY);
            CommonHelper::event($e->GetErrorDetails(), CommonHelper::PAYMENT_BANK_LOG_FILE, CommonHelper::DAILY);
        } catch (Exception $ex) {
            $response['message']        = $ex->getMessage();
            $errMsgLog                  = trans('messages.exception') . __METHOD__. "|". $ex->getMessage() . "|" . $ex->getFile() . "|" . $ex->getLine();
            CommonHelper::event($errMsgLog, CommonHelper::PAYMENT_BANK_LOG_FILE, CommonHelper::DAILY);
        }
        return $response;
    }

    /**
     * Create Bank Account type CA.
     * API hit to MangoPay
     *
     * @param array $aParams parameters to create Bank Account
     * @return array $response
     *
     */
    protected function _createCABankAccount($aParams) {
        $response = array(
            'status'                => FALSE,
            'message'               => trans('messages.common_error'),
            'data'                  => '',
            'mangopay_error'        => ''
        );
        try {
            // Extract User Params
            $userId                                     = $aParams['userId'];
            $bankAccount                               = new \MangoPay\BankAccount();
            $bankAccount->Tag                          = "CA";
            $bankAccount->OwnerAddress                 = new \MangoPay\Address();
            $bankAccount->OwnerAddress->AddressLine1   = $aParams['owner_add'];
            $bankAccount->OwnerAddress->AddressLine2   = '';
            $bankAccount->OwnerAddress->City           = $aParams['owner_City'];
            $bankAccount->OwnerAddress->Region         = $aParams['owner_Country'];
            $bankAccount->OwnerAddress->PostalCode     = $aParams['owner_PostalCode'];
            $bankAccount->OwnerAddress->Country        = $aParams['owner_Country'];
            $bankAccount->OwnerName                    = $aParams['owner_name'];
            $bankAccount->CreationDate                 = time();
            $bankAccount->Details                      = new \MangoPay\BankAccountDetailsCA();
            $bankAccount->Details->BranchCode          = $aParams['BranchCode'];
            $bankAccount->Details->InstitutionNumber   = $aParams['InstitutionNumber'];
            $bankAccount->Details->AccountNumber       = $aParams['AccountNumber'];
            $bankAccount->Details->BankName            = $aParams['BankName'];
            //Send the request
            $bankAccountResponse                        = $this->mangopay->Users->CreateBankAccount($userId, $bankAccount);

            $response['status']     = TRUE;
            $response['message']    = trans('messages.success');
            $response['data']       = $bankAccountResponse;
        } catch (\MangoPay\Libraries\ResponseException $e) {
            $response['message']        = $e->getMessage();
            $response['mangopay_error'] = $e->GetErrorDetails();
            $errMsgLog = trans('messages.exception_manogopay') . __METHOD__. "|". $response['message'];
            CommonHelper::event($errMsgLog, CommonHelper::PAYMENT_BANK_LOG_FILE, CommonHelper::DAILY);
            CommonHelper::event($e->GetErrorDetails(), CommonHelper::PAYMENT_BANK_LOG_FILE, CommonHelper::DAILY);
        } catch (Exception $ex) {
            $response['message']        = $ex->getMessage();
            $errMsgLog                  = trans('messages.exception') . __METHOD__. "|". $ex->getMessage() . "|" . $ex->getFile() . "|" . $ex->getLine();
            CommonHelper::event($errMsgLog, CommonHelper::PAYMENT_BANK_LOG_FILE, CommonHelper::DAILY);
        }
        return $response;
    }

    /**
     * Create Bank Account type Others.
     * API hit to MangoPay
     *
     * @param array $aParams parameters to create Bank Account
     * @return array $response
     *
     */
    protected function _createOtherBankAccount($aParams) {
        $response = array(
            'status'                => FALSE,
            'message'               => trans('messages.common_error'),
            'data'                  => '',
            'mangopay_error'        => ''
        );
        try {
            // Extract User Params
            $userId                                     = $aParams['userId'];
            $bankAccount                                = new \MangoPay\BankAccount();
            $bankAccount->Type                          = $aParams['banktype'];
            $bankAccount->UserId                        = $userId;
            $bankAccount->OwnerAddress                  = new \MangoPay\Address();
            $bankAccount->OwnerAddress->AddressLine1    = $aParams['owner_add'];
            $bankAccount->OwnerAddress->AddressLine2    = '';
            $bankAccount->OwnerAddress->City            = $aParams['owner_City'];
            $bankAccount->OwnerAddress->Region          = $aParams['owner_Country'];
            $bankAccount->OwnerAddress->PostalCode      = $aParams['owner_PostalCode'];
            $bankAccount->OwnerAddress->Country         = $aParams['owner_Country'];
            $bankAccount->BIC                           = $aParams['bic'];
            $bankAccount->AccountNumber                 = $aParams['AccountNumber'];

            $bankAccount->Details                       = new \MangoPay\BankAccountDetailsOTHER();
            $bankAccount->Details->BIC                  = $aParams['bic'];
            $bankAccount->Details->Type                 = $aParams['banktype'];
            $bankAccount->Details->Country              = $aParams['owner_Country'];
            $bankAccount->Details->AccountNumber        = $aParams['AccountNumber'];
            $bankAccount->OwnerName                     = $aParams['owner_name'];
            $bankAccount->CreationDate                  = time();
            //Send the request
            $bankAccountResponse                        = $this->mangopay->Users->CreateBankAccount($userId, $bankAccount);

            $response['status']     = TRUE;
            $response['message']    = trans('messages.success');
            $response['data']       = $bankAccountResponse;
        } catch (\MangoPay\Libraries\ResponseException $e) {
            $response['message']        = $e->getMessage();
            $response['mangopay_error'] = $e->GetErrorDetails();
            $errMsgLog = trans('messages.exception_manogopay') . __METHOD__. "|". $response['message'];
            CommonHelper::event($errMsgLog, CommonHelper::PAYMENT_BANK_LOG_FILE, CommonHelper::DAILY);
            CommonHelper::event($e->GetErrorDetails(), CommonHelper::PAYMENT_BANK_LOG_FILE, CommonHelper::DAILY);
        } catch (Exception $ex) {
            $response['message']        = $ex->getMessage();
            $errMsgLog                  = trans('messages.exception') . __METHOD__. "|". $ex->getMessage() . "|" . $ex->getFile() . "|" . $ex->getLine();
            CommonHelper::event($errMsgLog, CommonHelper::PAYMENT_BANK_LOG_FILE, CommonHelper::DAILY);
        }
        return $response;
    }

    /**
     * Method to get Legal User.
     * If not present it will create one and also user wallet.
     * 
     * @param int $userId
     * @param array $aUserDetails
     * @return array $response
     */
    public function getMangoUserLegal($userId, $aUserDetails = array()) {
        $response = array(
            'status'            => FALSE,
            'message'           => trans('messages.common_error'),
            'data'              => '',
        );
        try {
            $mangoUserData  = $this->_getUserModel()->getmangoUser($userId);
            if ($mangoUserData['mangoUser'] == NULL) {
                //create new natural user,
                $mangoUserLegal = self::createLegalUser($aUserDetails);
                if ($mangoUserLegal['status']) {
                    $mangoUserId    = $mangoUserLegal['data']->Id;
                    // create wallet for that user.
                    $walletDetails  = self::createWallet($mangoUserId);
                    $walletId       = $walletDetails->Id;
                    UserMangopay::create([
                        'fk_users_id' => $userId,
                        'mango_users_id' => $mangoUserId,
                        'mango_users_wallet_id' => $walletId]
                    );
                    $mangoUserData = $this->_getUserModel()->getmangoUser($userId);
                    $response['status'] =  true;
                    $response['data']   =  $mangoUserData;
                } else {
                    $response['message'] =  $mangoUserLegal['mangopay_error'];
                    $response['status'] =  $mangoUserLegal['status'];
                }
            } else {
                $response['status'] =  true;
                $response['data']   =  $mangoUserData;
            }
        } catch (Exception $ex) {
            $response['message']        = $ex->getMessage();
            $errMsgLog                  = trans('messages.exception') . __METHOD__. "|". $ex->getMessage() . "|" . $ex->getFile() . "|" . $ex->getLine();
            CommonHelper::event($errMsgLog, CommonHelper::PAYMENT_REGISTER_LOG_FILE, CommonHelper::DAILY);
        }
        return $response;
    }

    /**
     * Method to upload KYC documents for 1st time after registration
     *
     * @param int $storeId
     * @param int $userMangoPayId
     * @param array $aRequestData
     * @return array $response
     */
    public function uploadUserKycDocuments($storeId, $userMangoPayId, $aRequestData) {
        $response = array(
            'status'            => FALSE,
            'message'           => trans('messages.common_error'),
            'data'              => '',
        );
        try {
            $error = false;
            $aImages                = config('mangopay.kyc_users_image');
            $requiredImagesForUser  = isset($aImages[$aRequestData['business_type']]) ? $aImages[$aRequestData['business_type']] : array() ;
            foreach ($requiredImagesForUser as $requiredImage => $requiredImageDetails) {
                $imageName      = isset($aRequestData[$requiredImage]) ? $aRequestData[$requiredImage] : "" ;
                $documentResult = $this->uploadUserKycDocument($storeId, $userMangoPayId, $imageName, $requiredImageDetails);
                if (!$documentResult['status']) {
                    $error = true;
                    $response['message'] = $documentResult['message'];
                    break;
                }
            }
            if (!$error) {
                $response['status']     = true;
                $response['message']    = trans('messages.success');
            }
        } catch (Exception $ex) {
            $response['message'] = $ex->getMessage();
        }
        return $response;
    }

    /**
     * Method To Uplod Document to MangoPay
     *
     * @param int $storeId
     * @param int $userMangoPayId
     * @param string $imageName
     * @param array $requiredImageDetails
     * @return array $response
     */
    public function uploadUserKycDocument($storeId, $userMangoPayId, $imageName, $requiredImageDetails) {
        $response = array(
            'status'            => FALSE,
            'message'           => trans('messages.common_error'),
            'data'              => '',
        );
        try {
            $kycDocument = new \MangoPay\KycDocument();
            //$kycDocument->Tag = "custom meta";
            $docType        = $requiredImageDetails['label_mangopay'];
            $kycDocument->Type = $docType;

            $kycResult      = $this->mangopay->Users->CreateKycDocument($userMangoPayId, $kycDocument);
            $kYCDocumentId  = $kycResult->Id;

            // Create Page & Upload
            $productImageServerPath     = public_path()."/uploads/kyc/".$imageName;
            //$productImageServerPath     = $imageName;
            $this->mangopay->Users->CreateKycPageFromFile($userMangoPayId, $kYCDocumentId, $productImageServerPath);
            
            $aKYCStatusAll = config('mangopay.kyc_document_status');
            // Change status of document
            $kycDocument            = new \MangoPay\KycDocument();
            $docStatus              = $aKYCStatusAll['VALIDATION_ASKED'];
            $kycDocument->Status    = $docStatus;
            $kycDocument->Id        = $kYCDocumentId;
            $kycStatusResult        = $this->mangopay->Users->UpdateKycDocument($userMangoPayId, $kycDocument);
            KycDetails::create([
                        'fk_users_id'   => $storeId,
                        'document_id'   => $kYCDocumentId,
                        'type'          => $docType,
                        'image'         => $imageName,
                        'status'        => $docStatus,
                ]
            );
            $response['status']     = TRUE;
            $response['message']    = trans('messages.success');
       } catch (\MangoPay\Libraries\ResponseException $e) {
            $response['message']        = $e->getMessage();
            $response['mangopay_error'] = $e->GetErrorDetails();
            $errMsgLog = trans('messages.exception_manogopay') . __METHOD__. "|". $response['message'];
            CommonHelper::event($errMsgLog, CommonHelper::PAYMENT_KYC_LOG_FILE, CommonHelper::DAILY);
            CommonHelper::event($e->GetErrorDetails(), CommonHelper::PAYMENT_KYC_LOG_FILE, CommonHelper::DAILY);
        } catch (Exception $ex) {
            $response['message']        = $ex->getMessage();
            $errMsgLog                  = trans('messages.exception') . __METHOD__. "|". $ex->getMessage() . "|" . $ex->getFile() . "|" . $ex->getLine();
            CommonHelper::event($errMsgLog, CommonHelper::PAYMENT_KYC_LOG_FILE, CommonHelper::DAILY);
        }
        return $response;
    }

    /**
     * 
     * @param int $userMangoId
     * @param int $kYCDocumentId
     * @return array $response
     *
     */
    public function getKycDetails($userMangoId, $kYCDocumentId) {
        $response = array(
            'status'            => FALSE,
            'message'           => trans('messages.common_error'),
            'data'              => '',
            'mangopay_error'    => ''
        );
        try {
            //$userMangoId    = 13907999;
            //$kYCDocumentId  = 14710383;
            $kycDocument            = $this->mangopay->Users->GetKycDocument($userMangoId, $kYCDocumentId);
            $response['status']     = TRUE;
            $response['message']    = trans('messages.success');
            $response['data']       = $kycDocument;
            $docStatus              = $kycDocument->Status;
            $docType                = $kycDocument->Type;
            
            $aKYCStatusAll = config('mangopay.kyc_document_status');
            if ($docStatus != $aKYCStatusAll['VALIDATION_ASKED']) {
                // Update Status of Document to REFUSED || VALIDATED
                KycDetails::where('type', $docType)
                    ->where('document_id', $kYCDocumentId)
                    ->update(['status' => $docStatus]);
            }
        } catch (\MangoPay\Libraries\ResponseException $e) {
            $response['message']        = $e->getMessage();
            $response['mangopay_error'] = $e->GetErrorDetails();
            $errMsgLog = trans('messages.exception_manogopay') . __METHOD__. "|". $response['message'];
            CommonHelper::event($errMsgLog, CommonHelper::PAYMENT_KYC_LOG_FILE, CommonHelper::DAILY);
            CommonHelper::event($e->GetErrorDetails(), CommonHelper::PAYMENT_KYC_LOG_FILE, CommonHelper::DAILY);
        } catch (Exception $ex) {
            $response['message']        = $ex->getMessage();
            $errMsgLog                  = trans('messages.exception') . __METHOD__. "|". $ex->getMessage() . "|" . $ex->getFile() . "|" . $ex->getLine();
            CommonHelper::event($errMsgLog, CommonHelper::PAYMENT_KYC_LOG_FILE, CommonHelper::DAILY);
        }
        return $response;
    }
    
    /**
     * Method to update customer card for mangopay.
     * 
     * @param type $userId
     * @param type $modeCreated "WEB/API"
     * @return type
     */
    public function updateCustomerCard($userId, $modeCreated, $cardId = NULL) {
        $response = array(
            'status' => FALSE,
            'message' => trans('messages.common_error'),
        );
        try {
            User::findorfail($userId);
            $mangoUserData = $this->_getUserModel()->getmangoUser($userId);
            if ($mangoUserData['mangoUser'] == NULL) {
                //create new natural user,
                $mangoUser = $this->createMangoUser($mangoUserData);
                if (!is_object($mangoUser) && $mangoUser['status'] == 'error') {
                    $response['message'] = $mangoUser['message'];
                    return $response;
                }
                $mangoUserId = $mangoUser->Id;
                // create wallet for that user.
                $walletDetails = $this->createWallet($mangoUserId);
                $walletId = $walletDetails->Id;
                UserMangopay::create([
                    'fk_users_id' => $userId,
                    'mango_users_id' => $mangoUserId,
                    'mango_users_wallet_id' => $walletId]);
                $response['userData'] = $this->_getUserModel()->getCardAddress($userId);
            } else {
                $mangoUserId = $mangoUserData['mangoUser']['mango_users_id'];
                $walletId = $mangoUserData['mangoUser']['mango_users_wallet_id'];
                $response['userData'] = ['id' => $userId, 'cardAddress' => (object) ['address' => '', 'city' => '', 'state' => '', 'pin' => '']]; //$user->getCardAddress($userId);
            }
            $response['status'] = TRUE;
            $response['message'] = 'Details fetched successfully';
            $cardRegistrationDetails = $this->registerCard($mangoUserId, $modeCreated, $cardId);
            $response['cardRegistrationDetails'] = $cardRegistrationDetails;
            $response['returnUrl'] = route('payment.validateCard', ['id' => $userId, 'cardId' => $cardId]);
        } catch (Exception $ex) {
            $response['message'] = $ex->getMessage();
            $errMsgLog = trans('messages.exception') . __METHOD__ . "|" . $ex->getMessage() . "|" . $ex->getFile() . "|" . $ex->getLine();
            CommonHelper::event($errMsgLog, CommonHelper::PAYMENT_KYC_LOG_FILE, CommonHelper::DAILY);
        }
        return $response;
    }

    /**
     * 
     * @param type $payInId
     * @param type $amount
     * @param type $authorId
     * @param type $currency
     * @return type
     */
    public function createPayInRefund($payInId, $amount, $authorId = null, $currency = null) {
        $response = array(
            'status'                => FALSE,
            'message'               => trans('messages.common_error'),
            'data'                  => '',
            'mangopay_error'        => ''
        );
        try {
            $amount = round($amount * Config::get('appConstants.poundToPence'),2);
            // Extract User Params
            $refund = new \MangoPay\Refund();
            $refund->Tag = "alchemy_customer_refund_".$payInId;
            $refund->AuthorId = $authorId;
            // We can send amount but as of now our business model we need to refund all amount so no need of following.
            /*$refund->DebitedFunds = new \MangoPay\Money();
            $refund->DebitedFunds->Currency = config::get('appConstants.currency');
            $refund->DebitedFunds->Amount = $amount;
            $refund->Fees = new \MangoPay\Money();
            $refund->Fees->Currency = config::get('appConstants.currency');
            $refund->Fees->Amount = $amount;*/
            //$Result = $Api->PayIns->CreateRefund($PayInId, $refund);
            $refundResults = $this->mangopay->PayIns->CreateRefund($payInId, $refund);
            if ($refundResults->Status != \MangoPay\PayInStatus::Failed) {
                $this->insertUpdateRefundDetails($refundResults);
                $response['status']     = TRUE;
                $response['message']    = trans('messages.success');
                if ($refundResults->Status == \MangoPay\PayInStatus::Succeeded) {
                    $response['refund_status']    = \MangoPay\PayInStatus::Succeeded;
                }
                if ($refundResults->Status == \MangoPay\PayInStatus::Created) {
                    $response['refund_status']    = \MangoPay\PayInStatus::Created;
                }
            } else  {
                $response['status']     = FALSE;
                $response['message']    = $refundResults->ResultMessage;
            }
            $response['data']       = $refundResults;
        } catch (\MangoPay\Libraries\ResponseException $e) {
            $response['message']        = $e->getMessage();
            $response['mangopay_error'] = $e->GetErrorDetails();
            $errMsgLog = trans('messages.exception_manogopay') . __METHOD__. "|". $response['message'];
            CommonHelper::event($errMsgLog, CommonHelper::PAYMENT_REFUND_LOG_FILE, CommonHelper::DAILY);
            CommonHelper::event($e->GetErrorDetails(), CommonHelper::PAYMENT_REFUND_LOG_FILE, CommonHelper::DAILY);
        } catch (Exception $ex) {
            $response['message']        = $ex->getMessage();
            $errMsgLog                  = trans('messages.exception') . __METHOD__. "|". $ex->getMessage() . "|" . $ex->getFile() . "|" . $ex->getLine();
            CommonHelper::event($errMsgLog, CommonHelper::PAYMENT_REFUND_LOG_FILE, CommonHelper::DAILY);
        }
        return $response;
    }
    
    /**
     * Function to insert payment status
     *
     * @param array $data
     * @return int $paymentId
     *
     */
    protected function insertUpdateRefundDetails($mangoPayResponse) {
        $refundId               = $mangoPayResponse->Id;
        $refundStatus           = $mangoPayResponse->Status;
        $refundMessage          = $mangoPayResponse->ResultMessage;
        $inititalTransactionId  = $mangoPayResponse->InitialTransactionId;
        $refundExists = DB::table('refund_details')
            ->select('refund_id')
            ->where('intital_transaction_id', '=', $inititalTransactionId)
            ->where('refund_status', '=', $refundStatus)
            ->first();
        if (empty($refundExists)) {
            $insertData          = array(
                'refund_id'                 => $refundId,
                'intital_transaction_id'    => $inititalTransactionId,
                'refund_message'            => $refundMessage,
                'refund_status'             => $refundStatus,
                'rawData'                   => json_encode($mangoPayResponse),
                'created_at'                => CommonHelper::getCurrentDateTime(),
            );
            $paymentId = DB::table('refund_details')->insertGetId($insertData);
        }
    }
    
    /**
     * 
     * @param int $refundId
     * @param int $kYCDocumentId
     * @return array $response
     *
     */
    public function getRefundDetails($refundId) {
        $response = array(
            'status'            => FALSE,
            'message'           => trans('messages.common_error'),
            'data'              => '',
            'mangopay_error'    => ''
        );
        try {
            $refundResponse = $this->mangopay->Refunds->Get($refundId);
            $this->insertUpdateRefundDetails($refundResponse);
            $inititalTransactionId  = $refundResponse->InitialTransactionId;
            $orderStatusModel       =  new OrderStatus();
            $orderStatusModel->updateCancelledOrderStatus($inititalTransactionId);
            $response['status']     = TRUE;
            $response['message']    = trans('messages.success');
            $response['data']       = $refundResponse;
        } catch (\MangoPay\Libraries\ResponseException $e) {
            $response['message']        = $e->getMessage();
            $response['mangopay_error'] = $e->GetErrorDetails();
            $errMsgLog = trans('messages.exception_manogopay') . __METHOD__. "|". $response['message'];
            CommonHelper::event($errMsgLog, CommonHelper::PAYMENT_REFUND_LOG_FILE, CommonHelper::DAILY);
            CommonHelper::event($e->GetErrorDetails(), CommonHelper::PAYMENT_REFUND_LOG_FILE, CommonHelper::DAILY);
        } catch (Exception $ex) {
            $response['message']        = $ex->getMessage();
            $errMsgLog                  = trans('messages.exception') . __METHOD__. "|". $ex->getMessage() . "|" . $ex->getFile() . "|" . $ex->getLine();
            CommonHelper::event($errMsgLog, CommonHelper::PAYMENT_REFUND_LOG_FILE, CommonHelper::DAILY);
        }
        return $response;
    }

}
