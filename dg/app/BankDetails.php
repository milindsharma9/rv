<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Exception;
use DB;
use DateTime;
use App\Http\Helper\CommonHelper;

class BankDetails extends Model
{
   
    protected $table = "bank_account_details";
    //only allow the following items to be mass-assigned to our model
    //protected $fillable = ['name', 'image', 'description'];


    /**
     * Method to insert Update User bank Details
     *
     * @param int $userId
     * @param int $accoundId
     * return array $response
     *
     */
    public function saveUserBankDetails($userId, $accoundId) {
        $response = array(
            'status'    => false,
            'message'   => trans('messages.db_error'),
        );
        try {
            $userBankDetails    = $this->getUserBankDetails($userId);
            if (!$userBankDetails['hasAccount']) {
                $insertData = array(
                    'fk_user_id'        => $userId,
                    'userMbankAccId'    => $accoundId,
                    'created_at'        => CommonHelper::getCurrentDateTime(),
                    'updated_at'        => CommonHelper::getCurrentDateTime(),
                );
                DB::table('bank_account_details')->insert($insertData);
            } else {
                $updateData = array(
                    'userMbankAccId'    => $accoundId,
                    'updated_at'        => CommonHelper::getCurrentDateTime(),
                );
                $data = DB::table('bank_account_details')->where('fk_user_id','=',$userId)
                    ->update($updateData);
            }
            $response['status']     = true;
            $response['message']    = trans('messages.success');
        } catch (Exception $ex) {
            $response['message'] = $ex->getMessage();
            $errMsgLog  = trans('messages.exception') . __METHOD__. "|". $ex->getMessage() . "|" . $ex->getFile() . "|" . $ex->getLine();
            CommonHelper::event($errMsgLog, CommonHelper::PAYMENT_BANK_LOG_FILE, CommonHelper::DAILY);
        }
        return $response;
    }

    /**
     * Method to get user Bank Details
     *
     * @param int $userId
     * return array $response
     *
     */
    public function getUserBankDetails($userId) {
        $response = array(
            'hasAccount' => false,
            'details'    => array(),
        );
        $details = BankDetails::where('fk_user_id', $userId)->first();
        if (!empty($details)) {
            $details = $details->toArray();
            $response['hasAccount'] = true;
            $response['details']    = array(
                'userMbankAccId' => $details['userMbankAccId']
            );
        }
        return $response;
    }

    /**
     * Method to save Payout transaction details
     *
     * @param int $userId
     * @param int $walletId
     * @param int $userMangoBankAccountId
     * @param decimal $amount
     * @param object $responseData
     * return array $response
     *
     */
    public function savePayoutTransactionDetails($userId, $walletId, $userMangoBankAccountId, $amount
                            , $responseData) {
        $response = array(
            'status'    => false,
            'message'   => trans('messages.db_error'),
        );
        try {
            $status         = $responseData->Status;
            $transactionId  = $responseData->Id;
            $insertData = array(
                    'fk_user_id'     => $userId,
                    'wallet_id'      => $walletId,
                    'bank_account'   => $userMangoBankAccountId,
                    'amount'         => $amount,
                    'transaction_id' => $transactionId,
                    'status'         => $status,
                    'rawData'        => json_encode($responseData),
                    'created_at'     => CommonHelper::getCurrentDateTime(),
                    'updated_at'     => CommonHelper::getCurrentDateTime(),
            );
            DB::table('payout')->insert($insertData);
            $response['status']     = true;
            $response['message']    = trans('messages.success');
        } catch (Exception $ex) {
            $response['message']        = $ex->getMessage();
            $errMsgLog                  = trans('messages.exception') . __METHOD__. "|". $ex->getMessage() . "|" . $ex->getFile() . "|" . $ex->getLine();
            CommonHelper::event($errMsgLog, CommonHelper::PAYMENT_PAYOUT_LOG_FILE, CommonHelper::DAILY);
        }
        return $response;
        
    }
}
