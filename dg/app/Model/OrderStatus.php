<?php

namespace App\Model;

use Illuminate\Database\Eloquent\Model;
use Exception;
use DB;
use DateTime;
use App\Http\Helper\CommonHelper;
use App\SalesOrder;
//use App\Http\Helpers\Email;
use App\Http\Helper\EmailForce24;

class OrderStatus extends Model
{
    protected $table = "order_status";
    protected $orderStatusMeta = array();
    
    const ORDER_STATUS_COMPLETED    = 'Completed';
    const ORDER_STATUS_REJECTED     = 'Rejected';
    
    const ORDER_STATUS_CONFIRMED_ID = 1;
    const ORDER_STATUS_COLLECTED_ID = 2;
    const ORDER_STATUS_NEW_ID       = 4;
    const ORDER_STATUS_COMPLETED_ID = 3;
    const ORDER_STATUS_REJECTED_ID  = 5;
    const ORDER_STATUS_REFUNDED_ID  = 6;
    
    const ORDER_ITEM_CONFIRMED_ID = 1;
    const ORDER_ITEM_COLLECTED_ID = 2;
    
    protected $orderData;
    
    /**
     *
     * @var App\PaymentModel 
     */
    protected $_paymentModel = NULL;
    
    private $orderModel = null;

    private function getOrderModel() {
        if ($this->orderModel == null) {
            $this->orderModel = new SalesOrder();
        }
        return $this->orderModel;
    }

    /**
     * Function to Instatntiate Payment Model.
     * 
     * @return object App\Payment Model
     */
    private function _getPaymentModel() {
        if ($this->_paymentModel == NULL) {
            $this->_paymentModel = \App::make('\App\PaymentModel');
        }
        return $this->_paymentModel;
    }

    public function __construct() {
        $this->setStatusMeta();
    }

    /**
     * Method to set Order Status Table Data in Array
     */
    public function setStatusMeta() {
        $this->orderStatusMeta = array();
        $statuses =  DB::table('order_status')
            ->select('order_status.*')
            ->get();
        $statuses = json_decode(json_encode($statuses), true);
        foreach ($statuses as $status) {
            $this->orderStatusMeta[$status['id_order_status']] = $status;
        }
    }

    /**
     * Method to get allowed order status, from current state.
     *
     * @param int $orderStatusId
     * @param string $itemStatus Comma Seprated string of all item status
     * @return array $allowedStatus
     *
     */
    public function getAllowedManualStatusForOrder($orderStatusId, $itemStatus) {
        $allowedStatus  = array();
        $aItemStatus    = explode(",", $itemStatus);
        $currentStatusDetails = $this->orderStatusMeta[$orderStatusId];
        foreach ($this->orderStatusMeta as $orderStatus) {
            $validStatus = true;
            if ($orderStatus['is_manual'] && $orderStatus['lifecycle_order'] > $currentStatusDetails['lifecycle_order']) {
                if ($orderStatus['name'] == self::ORDER_STATUS_COMPLETED) {
                    foreach ($aItemStatus as $itemStatus) {
                        if ($itemStatus != self::ORDER_ITEM_COLLECTED_ID) {
                            $validStatus = false;
                            break;
                        }
                    }
                    if (!$validStatus) {
                        continue;
                    }
                }
                if ($orderStatus['name'] == self::ORDER_STATUS_REJECTED) {
                    foreach ($aItemStatus as $itemStatus) {
                        if ($itemStatus != self::ORDER_ITEM_CONFIRMED_ID) {
                            $validStatus = false;
                            break;
                        }
                    }
                    if (!$validStatus) {
                        continue;
                    }
                }
                $allowedStatus[$orderStatus['id_order_status']] = $orderStatus;
            }
        }
        return $allowedStatus;
    }

    /**
     * 
     * @param int $id
     * @param int $newStatusId
     * @return type
     */
    public function changeOrderStatus($id, $newStatusId, $isManualRequest = false) {
        $response = array(
            'status'    => false,
            'message'   => trans('messages.common_error'),
        );
        try {
            $orderDetails = $this->getOrderModel()->getSalesOrder($id);
            if (empty($orderDetails)) {
                return $response;
            }
            DB::beginTransaction();
            $this->orderData = $orderDetails[0];
            if ($isManualRequest) {
                if (isset($orderDetails[0]->allowed_operations[$newStatusId])) {
                    $oldStatusId    = $orderDetails[0]->order_status_id;
                    $response       = $this->_changeOrderStatus($id, $newStatusId, $oldStatusId);
                } else {
                    $response['message'] = trans('messages.order_status_change_invalid');
                }
            } else {
                $oldStatusId    = $orderDetails[0]->order_status_id;
                $response       = $this->_changeOrderStatus($id, $newStatusId, $oldStatusId);
            }
            DB::commit();
        } catch (Exception $ex) {
            DB::rollBack();
            $errMsg = trans('messages.order_exception') . $ex->getMessage() . $ex->getFile() . $ex->getLine();
            CommonHelper::event($errMsg, CommonHelper::ORDER_STATUS_LOG_FILE, CommonHelper::DAILY);
        }
        return $response;
    }

    /**
     * 
     * @param int $id
     * @param int $oldStatusId
     * @param int $newStatusId
     * @return array $response
     */
    protected function _changeOrderStatus($id, $newStatusId, $oldStatusId = null) {
        $response = array(
            'status'    => false,
            'message'   => trans('messages.common_error'),
        );
        switch ($newStatusId) {
            case self::ORDER_STATUS_COMPLETED_ID:
                $response = $this->_makeVendorPaymentAndChangeStatus($id, $newStatusId, $oldStatusId);
                break;
            
            case self::ORDER_STATUS_REJECTED_ID:
                $response = $this->_makeRefundAndChangeStatus($id, $newStatusId, $oldStatusId);
                break;
            
            default:
                $response = $this->_changeStatusComplete($id, $newStatusId, $oldStatusId);
                break;
                
        }
        return $response;
    }

    /**
     * Update Order Status
     * 
     * @package SalesOrder Model
     * @param int $id
     * @param int $oldStatusId Order Old Status
     * @param int $newStatusId Order New Status
     * @return boolean
     */
    protected function _updateOrderStatus($orderId, $newStatusId, $oldStatusId = null) {
        $update = DB::table('sales_order')
                ->where('id_sales_order', '=', $orderId);
        
        if(!empty($oldStatusId)) {
            $update = $update->where('fk_order_status_id', '=', $oldStatusId);
        } 
        $update = $update->update(['fk_order_status_id' => $newStatusId]);
        
        $this->logOrderStatusHistory($orderId, $newStatusId);
    }

    /**
     * Method called to call external API and make refund
     *
     * @param int $orderId
     * @param int $oldStatusId
     * @param int $newStatusId
     * @return array
     */
    protected function _makeRefundAndChangeStatus($orderId, $newStatusId, $oldStatusId = null) {
        $response = array(
            'status'    => false,
            'message'   => trans('messages.common_error'),
        );
        $orderTranasactionDetails = $this->getOrderModel()->getOrderTransactionDetails($orderId);
        if (!empty($orderTranasactionDetails)) {
            $payInId        = $orderTranasactionDetails->payInId;
            $orderAmount    = $orderTranasactionDetails->total;
            $authorId       = $orderTranasactionDetails->authorId;
            $refundResponse = $this->_getPaymentModel()->createPayInRefund($payInId, $orderAmount, $authorId);
            if ($refundResponse['status']) {
                $isRefundDone = false;
                if ($refundResponse['refund_status'] == \MangoPay\PayInStatus::Succeeded) {
                    $isRefundDone = true;
                    $newStatusId    = self::ORDER_STATUS_REFUNDED_ID;
                }
                $this->_updateOrderStatus($orderId, $newStatusId, $oldStatusId);
                $response['message'] = trans('messages.order_status_change_success');
                $response['status'] = $refundResponse['status'];
                $this->sendOrderRefundEmail($orderId, $isRefundDone);
            } else {
                $response['message'] = $refundResponse['message'];
            }
        } else  {
            $response['message'] = trans('messages.order_status_change_no_data');
        }
        return $response;
    }
    
    /**
     * Method called to call external API and make refund
     *
     * @param int $orderId
     * @param int $oldStatusId
     * @param int $newStatusId
     * @return array
     */
    protected function _makeVendorPaymentAndChangeStatus($orderId, $newStatusId, $oldStatusId = null) {
        $response = array(
            'status'    => false,
            'message'   => trans('messages.common_error'),
        );
        $vendorAmount = [];
        $adminAmount = $balanceAmount = 0;
        $orderTranasactionDetails = $this->getOrderModel()->getSalesOrderDetailsById($orderId);
        $orderTotal = $this->getOrderModel()->getOrderTransactionDetails($orderId);
        $subStoreDetailsObj = new \App\SubStoreDetails();
        if (!empty($orderTranasactionDetails)) {
            foreach ($orderTranasactionDetails as $key => $salesOrderItem) {
                if(!isset($vendorAmount[$salesOrderItem->fk_store_id])){
                    $vendorAmount[$salesOrderItem->fk_store_id] = 0;
                }
                $vendorAmount[$salesOrderItem->fk_store_id] += 
                        $salesOrderItem->store_price - ($salesOrderItem->store_price * ($salesOrderItem->vendor_commission/100));
                
            }
            $orderTotal = isset($orderTotal->total) ? $orderTotal->total : 0;
            //1) Transfer all the payemnt from customer wallet to admin Wallet.
            $adminReleasedSuccess = $this->_getPaymentModel()->releaseAdminPayment($orderId, $orderTotal);
            if ($adminReleasedSuccess) {
                foreach ($vendorAmount as $storeId => $amount) {
                    $parentId = $subStoreDetailsObj->getParentId($storeId);
                    if (!empty($parentId)) {
                        $balanceAmount += $amount;
                        $releasePayment = $this->_getPaymentModel()->releaseStorePayment($parentId, $orderId, $amount, $storeId);
                        if ($releasePayment) {
                            $this->_updateOrderStatus($orderId, $newStatusId, $oldStatusId);
                            $response['message'] = trans('messages.order_status_change_success');
                            $response['status'] = TRUE;
                        } else {
                            $response['message'] = trans('messages.admin_unsufficient_balance');
                        }
                    } else {
                        $response['message'] = trans('messages.admin_payment_failed');
                    }
                }
            } else {
                $response['message'] = trans('messages.order_status_change_no_data');
            }
            // @reason Logic of payment Change.
            //transfer admin payment to admin wallet.
//            $adminAmount = $orderTotal - $balanceAmount;
//            $this->_getPaymentModel()->releaseAdminPayment($orderId, $adminAmount);
        } else  {
            $response['message'] = trans('messages.order_status_change_no_data');
        }
        return $response;
    }


    /**
     * Change Order Status to Complete
     *
     * @param int $orderId
     * @param int $oldStatusId
     * @param int $newStatusId
     * @return array
     */
    protected function _changeStatusComplete($orderId, $newStatusId, $oldStatusId = null) {
        $response = array(
            'status'    => true,
            'message'   => trans('messages.order_status_change_success'),
        );
        $this->_updateOrderStatus($orderId, $newStatusId, $oldStatusId);
        return $response;
    }

    /**
     * 
     * @param int $orderId
     * @param boolean $isRefundDone
     */
    public function sendOrderRefundEmail($isRefundDone =  false) {
        try {
            $driverCharge   = config('appConstants.driver_charge');
            $currencySymbol = config('appConstants.currency_sign');
            $firstName      = $this->orderData->first_name;
            $lastName       = $this->orderData->last_name;
            //$name           = $firstName . ',' . $lastName;
            $email          = $this->orderData->email;
            if ($email == 'customer@customer.com') {
                $email = env('MANGO_LEGAL_EMAIL', '');
            }
            $refundTat      = config('appConstants.refund_tat');
            $orderNumber    = $this->orderData->orderId;
            $orderDate      = $this->orderData->created_at;
            $orderTotal     = $this->orderData->totalPrice;
            //$image          = url('alchemy/images'). '/logo.png';
            /*$data = [
                    'refunded'      => $isRefundDone,
                    'name'          => $name,
                    'ordernumber'   => $orderNumber,
                    'orderdate'     => $orderDate,
                    'logoimage'     => url('alchemy/images'). '/logo.png',
                    'carttotal'     => $orderTotal,
                    'currency'      => $currencySymbol,
                    'workingdays'   => $refundTat,
                    'tlink'         => config('appConstants.twitter'),
                    'timage'        => url('alchemy/images'). '/twitter.png',
                    'flink'         => config('appConstants.facebook'),
                    'fimage'        => url('alchemy/images'). '/facebook.png',
                    'ilink'         => config('appConstants.instagram'),
                    'iimage'        => url('alchemy/images'). '/instagram.png',
                    'plink'         => config('appConstants.pinterest'),
                    'pimage'        => url('alchemy/images'). '/pinterest.png',
                    'mlink'         => config('appConstants.mailto'),
                    'mimage'        => url('alchemy/images'). '/email.png',
                ];*/
            $data = [
                'firstName'     => $firstName,
                'lastName'      => $lastName,
                'email'         => $email,
                'refunded'      => $isRefundDone,
                'orderNumber'   => $orderNumber,
                'orderDate'     => $orderDate,
                'cartTotal'     => $orderTotal,
                'currency'      => $currencySymbol,
                'workingDays'   => $refundTat,
            ];
            //$mergeVars = array(array('name' => 'data', 'content' => $data));
            //Email::sendEmail($email, $mergeVars, 'Order Cancel');
            EmailForce24::sendEmailAPI($data, EmailForce24::ORDER_CANCEL);
        } catch (Exception $ex) {
            $errMsg = trans('messages.email_order_exception') . $ex->getMessage();
            CommonHelper::event($errMsg, CommonHelper::EMAIL_FAILURE_LOG_FILE, CommonHelper::DAILY);
        }
    }

    /**
     *
     * @param int $orderId
     * @param int $statusId
     */
    public function logOrderStatusHistory($orderId, $statusId) {
        $insertData          = array(
            'fk_sales_order_id'         => $orderId,
            'fk_sales_order_status_id'  => $statusId,
            'created_at'                => CommonHelper::getCurrentDateTime(),
        );
        DB::table('sales_order_status_history')->insert($insertData);
    }

    /**
     * 
     * @param int $id
     * @param int $newStatusId
     */
    public function changeOrderStatusInternal($id, $newStatusId) {
        $response = array(
            'status'    => false,
            'message'   => trans('messages.common_error'),
        );
        try {
            DB::beginTransaction();
            $response       = $this->_changeOrderStatus($id, $newStatusId);
            DB::commit();
        } catch (Exception $ex) {
            DB::rollBack();
            $errMsg = trans('messages.order_exception') . $ex->getMessage() . $ex->getFile() . $ex->getLine();
            CommonHelper::event($errMsg, CommonHelper::ORDER_STATUS_LOG_FILE, CommonHelper::DAILY);
        }
        return $response;
    }

    /**
     * Method Called from MangoPay IPN
     *
     * @param int $transactionId
     */
    public function updateCancelledOrderStatus($transactionId) {
        $orderDetails   = $this->getOrderModel()->getOrderIdFromTransacationId($transactionId);
        $orderId        = $orderDetails->id_sales_order;
        $newStatusId    = self::ORDER_STATUS_REFUNDED_ID;
        $this->changeOrderStatusInternal($orderId, $newStatusId);
    }
    
}
