<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\User;
use Validator;
use App\StoreModel;
use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\Controller;
use DB;
use Auth;
use App\SubStoreDetails;
use App\Product;
use Log;
use Exception;
use App\EventLog;

class VendorsController extends Controller {

    /**
     *
     * @var App\StoreModel 
     */
    private $storeModel = NULL;
    
    /**
     * Function to Instatntiate Store Model.
     * 
     * @package VendorsController
     * @return object App\StoreModel Model
     */
    private function getStoreModel() {
        if ($this->storeModel == NULL) {
            $this->storeModel = new StoreModel();
        }
        return $this->storeModel;
    }
    
    
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index() {
        $vendors = User::getSystemVendors();
        return view('admin.vendors.index', compact('vendors'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id) {
        try {
            $vendors = User::getSystemVendorsForEdit($id);
            $subStoreModel      = new SubStoreDetails();
            $storeSubStores     = $subStoreModel->getStoreSubStores($id, $includeParent = true, $getInactive = true);
            $aSubStores = array();
            foreach ($storeSubStores as $subStoreDetails) {
                $aSubStores[$subStoreDetails['fk_users_id']] = $subStoreDetails['store_name'];
            }
            return view('admin.vendors.edit', compact('vendors', 'aSubStores'));
        } catch (Exception $ex) {
            return redirect()->route('admin.vendors.index')->withErrors(trans('admin/users.not_exists'));
        }
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update($id, Request $request, \App\Http\Controllers\StoreController $storeControllerInstance) {
        DB::enableQueryLog();
        try {
            $vendors = User::findOrFail($id);
            $rules = array(
                'first_name' => 'required|max:255',
                'last_name' => 'required|max:255',
                'email'     => 'required|email|max:255|unique:users,email,'.$id,
                'store_name' => 'required',
                'phone' => 'numeric|digits_between:8,25',
                'password'  => 'min:6|confirmed',
            );
            $message = [
                'last_name.required'   => 'The surname field is required.'
            ];
            $storeModel         = $this->getStoreModel();
            $validationRules    = $storeModel->getValidationRulesForStoreAddress();
            $validator = Validator::make($request->all(), 
                    array_merge($rules,$validationRules), 
                    array_merge($storeModel->validationMessagesForAddress(),$message));
            if (isset($request['store_status'])) {
                $status = 1;
                $request['store_status'] = 1;
            } else {
                $status = 0;
            }
            if (!strpos($vendors->email, config('appConstants.vendor_store_default_email_suffix'))) {
                $request->merge(['activated' => $request->get('activated', '0')]);
            } else {
                $request->merge(['activated' => 1]);
            }
            if ($validator->fails()) {
                return redirect()->back()->withInput()->withErrors($validator);
            }
            if (empty($request->get('password'))) {
                $vendors->update($request->only('first_name', 'last_name', 'email', 'phone', 'activated'));
            } else {
                $aInputRequest = $request->only('first_name', 'last_name', 'email', 'phone', 'password', 'activated');
                $aInputRequest['password'] = bcrypt($aInputRequest['password']);
                $vendors->update($aInputRequest);
            }
            \App\SubStoreDetails::where('fk_users_id', $id)
                        ->update($request->only('store_name'));
            $this->updateStoreStatus(array($id), $status);
            $storeModel->saveStoreAddress($request->all(), $id);
            
            //
            if ($request['activated'] == 1
                && !strpos($vendors->email, config('appConstants.vendor_store_default_email_suffix'))
            ) {
                $userMangoPay = $storeControllerInstance->createVendorLegalAccount($id);
            }
            //
            /* Insert data to the log table for vendor update */
            $logData = array(
                'users_id'          => Auth::user()->id,
                'operation_type'    => EventLog::EVENT_VENDOR_UPDATE,
                'al_event'          => serialize(DB::getQueryLog()),
            );
            EventLog::logEvent($logData);
            /* End */
            return redirect()->route('admin.vendors.index')->with('status', "Vendors details updated successfully");
        } catch (Exception $ex) {
            Log::error('error in VendorController/update' . $ex->getMessage());
            return view('errors.500');
        }
    }

    /**
     * Mass Update function to update status from vendor index page
     * @param Request $request
     *
     * @return mixed
     */
    public function massUpdate(Request $request) {
        $toUpdate = json_decode($request->get('toUpdate'));
        if ($request->get('type') == 'activate') {
            $status = 1;
        } else {
            $status = 0;
        }
        try {
            if (!empty($toUpdate)) {
                $this->updateStoreStatus($toUpdate, $status);
            }
        } catch (Exception $ex) {
            Log::error(__METHOD__ . $ex->getMessage());
        }
        return redirect()->back();
    }

    /**
     * function to update store status
     * @param $idsArr, $status
     *     
     */
    function updateStoreStatus($idsArr, $status) {
        DB::enableQueryLog();
        $updateData = array(
            'store_status' => $status,
        );
        DB::table('sub_store_details')->whereIn('fk_users_id', $idsArr)
                ->update($updateData);
        /* Insert data to the log table for vendor status */
            $logData = array(
                'users_id'          => Auth::user()->id,
                'operation_type'    => EventLog::EVENT_UPDATE_STORE_STATUS,
                'al_event'          => serialize(DB::getQueryLog()),
            );
            EventLog::logEvent($logData);
            /* End */
        // Clears cache as near by store as are set In cache day Wise. Check StoreAddress->getNearbyStores
        Cache::flush();
    }

    /**
     * 
     * @param int $storeId
     * @return object View
     */
    public function manageVendorProducts($storeId) {
        $storeModel         = new StoreModel();
        $storeProducts      = $storeModel->getStoreProducts($storeId);
        $allProducts        = Product::orderBy('description', 'asc')->paginate(100);
        return view('admin.vendors.manage_products', compact('storeId', 'storeProducts', 'allProducts'));
    }

    /**
     * Method to save Store's product By Admin
     * 
     * @param Request $request
     * @return Array
     */
    public function saveProduct(Request $request) {
        $aResponse = array(
            'status'    => FALSE,
            'message'   => 'Empty Products'
        );
        try {
            $prodId         = $request->get('prodId');
            $isAdd          = $request->get('add');
            $storeId        = $request->get('storeId');
            $storeModel     = new StoreModel();
            $aResponse      = $storeModel->saveStoreProducts($prodId, $isAdd, $storeId);
        } catch (Exception $ex) {
            Log::error(__METHOD__ . $ex->getMessage());
        }
        return $aResponse;
    }
    
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function showVendorStores($storeId) {
        $vendors = User::getVendorStoresEdit($storeId);
        return view('admin.vendors.stores', compact('vendors'));
    }

}
