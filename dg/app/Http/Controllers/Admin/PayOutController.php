<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Payout;

class PayOutController extends Controller
{
    //
    
    /**
     * 
     */
    public function getPayOutSummary() {
        $payOutModel    = new Payout();
        $payOutDetail   = $payOutModel->getPayOutSummary();
        return view('admin.payouts.summary', compact('payOutDetail'));
    }

    /**
     *
     */
    public function getUserPayOutDetails($vendorId) {
        $payOutModel = new Payout();
        $payOutDetail   = $payOutModel->getUserPayOutDetails($vendorId);
        //print_r($payOutDetail);
        //echo $vendorId;
        return view('admin.payouts.details', compact('payOutDetail'));
        
    }
}
