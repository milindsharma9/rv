<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\SalesOrder;
use App\Http\Controllers\Controller;
use App\Model\OrderStatus;
use DB;
use Auth;
use App\EventLog;

class OrdersController extends Controller {

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index() {
        $orderModel = new SalesOrder();
        $orders     = $orderModel->getSalesOrder();
        return view('admin.orders.index', compact('orders'));
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id) {
        $orderModel = new SalesOrder();
        $orders     = $orderModel->getSalesOrder($id);
        return view('admin.orders.index', compact('orders'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id, $newStatusId) {
        DB::enableQueryLog();
        $orderStatusModel = new OrderStatus();
        $response = $orderStatusModel->changeOrderStatus($id, $newStatusId, $isManualRequest = true);
        /* Insert data to the log table for postcode add */
        $logData = array(
            'users_id' => Auth::user()->id,
            'operation_type' => EventLog::EVENT_ORDER_STATUS_CHANGE,
            'al_event' => serialize(DB::getQueryLog()),
        );
        EventLog::logEvent($logData);
        /* End */
        \Session::flash('message', $response['message']);
        return redirect()->route('admin.orders.index');
    }

    /**
     * Show Item Details of order.
     * Near By stores & Collected Product Information.
     *
     * @param  string  $orderNumber
     * @return Response
     */
    public function showOrderDetail($orderNumber, Request $request) {
        $orderModel     = new SalesOrder();
        $orderDetails   = $orderModel->getOrderDetailsForAdmin($orderNumber);
        if (!empty($orderDetails)) {
            return view('admin.orders.detail', compact('orderDetails'));
        } else {
            return view('errors.404');
        }
    }

}
