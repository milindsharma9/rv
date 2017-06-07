<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use MangoPay\MangoPayApi;
use Illuminate\Support\Facades\Config;
use App\SalesOrder;
use App\PaymentModel;

class PaymentController extends Controller {

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
     * @param \MangoPay\MangoPayApi $mangopay
     */
    public function __construct(MangoPayApi $mangopay) {
        $this->mangopay = $mangopay;
    }

    /**
     * Function to Instatntiate Store Model.
     * @return type
     */
    private function getPaymentModel() {
        if ($this->paymentModel == null) {
            $this->paymentModel = new PaymentModel($this->mangopay);
        }
        return $this->paymentModel;
    }

    /**
     * Function to display admin Dashboard.
     * @return type
     */
    public function getAdminDasboard() {
        $walletDetails = $this->getPaymentModel()->getWalletDetails();
        $walletBalance = isset($walletDetails->Balance->Amount) ? ($walletDetails->Balance->Amount / Config::get('appConstants.poundToPence')) : 0;
        $currencySymbol = \Config::get('appConstants.currency_sign');
//        $pendingStore = $this->getPaymentModel()->generatePendingPaymentData();
//        $releasedStore = $this->getPaymentModel()->getReleasePaymentInfo();
        return view('admin.payment.index', compact('walletBalance', 'currencySymbol'));
//                , 'pendingStore', 'releasedStore'));
    }
    
    /**
     * Function to get all store whose payement is pending for this month.
     * @return type
     */
    public function getPendingPayment(){
        return redirect()->route('admin.dashboard'); //removing release payment screen
        $pendingStore = $this->getPaymentModel()->generatePendingPaymentData();
        $currencySymbol = \Config::get('appConstants.currency_sign');
        return view('admin.payment.release', compact('currencySymbol', 'pendingStore'));
    }
    
    
    /**
     * Function to release this month payment.
     * @param type $id
     * @return type
     */
    public function release($id) {
        return redirect()->route('admin.dashboard'); //removing release payment screen
        $data = $this->getPaymentModel()->releaseStorePayment($id);
        if($data){
            \Session::flash('message', 'payment release successfully');
            return redirect('admin/releasepayment');
        }
        return redirect('admin/releasepayment')->withErrors(['message' => 'some error occured']);
    }
    
    /**
     * Function to show transaction history.
     * @param type $id
     * @param type $date
     * @return type
     */
    public function getTransactionHistory($id, $storePaymentId = NULL) {
        $order = new SalesOrder();
        $transHistory = $order->getTransactionDetails($id, $storePaymentId);
        $currencySymbol = \Config::get('appConstants.currency_sign');
        return view('admin.payment.history', compact('currencySymbol', 'transHistory'));
        
    }
    
    /**
     * Get payment histpry data.
     * @return type
     */
    public function getPaymentHistoryData() {
        $transHistory = $this->getPaymentModel()->getReleasePaymentInfo();
        return view('admin.payment.last-payment', compact('transHistory'));
    }
    
    /**
     * Function to dispaly store payment history.
     * @param type $id
     * @return type
     */
    public function getstorePaymentHistory($id) {
        $transHistory = $this->getPaymentModel()->getReleasePaymentInfo($id);
        return view('admin.payment.last-payment-history', compact('transHistory'));
    }

}
