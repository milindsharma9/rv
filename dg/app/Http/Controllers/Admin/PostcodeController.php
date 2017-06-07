<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\Redirect;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Product;
use App\ValidPostcode;
use Exception;
use App\Http\Helper\CommonHelper;
use App\Http\Controllers\CartController;

use DB;
use Auth;
use App\EventLog;

class PostcodeController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index() {
        //$validPostcodes = ValidPostcode::all();
        $validPostcodes = ValidPostcode::orderby('postcode', 'asc')->paginate(50);
        return view('admin.postcode.index', compact('validPostcodes'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create() {
        return view('admin.postcode.create');
    }

    public function store(Request $request) {
        DB::enableQueryLog();
        try {
            $rules = array(
                'postcode' => 'required',
            );
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return Redirect::to('/admin/postcode/create')->withInput()->withErrors($validator);
            }
            $aInputRequest  = $request->all();
            $postCode       = $aInputRequest['postcode'];
            $latLngResponse = $this->getLatLngForPostcode($postCode);
            $aInputRequest  = array_merge($aInputRequest, $latLngResponse);
            ValidPostcode::create($aInputRequest);
            
            /* Insert data to the log table for postcode add */
            $logData = array(
                'users_id'          => Auth::user()->id,
                'operation_type'    => EventLog::EVENT_POSTCODE_ADD,
                'al_event'          => serialize(DB::getQueryLog()),
            );
            EventLog::logEvent($logData);
            /* End */
            
            return redirect()->route('admin.postcode.index');
        } catch (Exception $ex) {
            return Redirect::to('/admin/postcode/create')->withInput()->withErrors($ex->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id) {
        $validPostcodes         = ValidPostcode::find($id);
        return view('admin.postcode.edit', compact('validPostcodes'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update($id, Request $request) {
        DB::enableQueryLog();
        try {
            $validPostcodes = ValidPostcode::findOrFail($id);
            $rules = array(
                'postcode' => 'required',
            );
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return redirect()
                    ->back()
                    ->withInput($request->all())
                    ->withErrors($validator);
            }
            $aInputRequest  = $request->all();
            $postCode       = $aInputRequest['postcode'];
            $latLngResponse = $this->getLatLngForPostcode($postCode);
            $aInputRequest = array_merge($aInputRequest, $latLngResponse);
            $validPostcodes->update($aInputRequest);
            
            /* Insert data to the log table for postcode update */
            $logData = array(
                'users_id'          => Auth::user()->id,
                'operation_type'    => EventLog::EVENT_POSTCODE_UPDATE,
                'al_event'          => serialize(DB::getQueryLog()),
            );
            EventLog::logEvent($logData);
            /* End */
            
            return redirect()->route('admin.postcode.index');
        } catch (Exception $ex) {
            return redirect()->back()->withInput($request->all())->withErrors($ex->getMessage());
        }
    }

    public function destroy($id) {
        DB::enableQueryLog();
        ValidPostcode::destroy($id);
        /* Insert data to the log table for postcode delete */
            $logData = array(
                'users_id'          => Auth::user()->id,
                'operation_type'    => EventLog::EVENT_POSTCODE_DELETE,
                'al_event'          => serialize(DB::getQueryLog()),
            );
            EventLog::logEvent($logData);
            /* End */
        \Session::flash('message', 'You have successfully deleted PostCode');
        return Redirect::route('admin.postcode.index');
    }

    /**
     * Mass delete function from index page
     * @param Request $request
     *
     * @return mixed
     */
    public function massDelete(Request $request) {
        DB::enableQueryLog();
        if ($request->get('toDelete') != 'mass') {
            $toDelete = json_decode($request->get('toDelete'));
            ValidPostcode::destroy($toDelete);
            
            /* Insert data to the log table for postcode mass delete */
            $logData = array(
                'users_id'          => Auth::user()->id,
                'operation_type'    => EventLog::EVENT_POSTCODE_MASS_DELETE,
                'al_event'          => serialize(DB::getQueryLog()),
            );
            EventLog::logEvent($logData);
            /* End */
            
        } else {
            ValidPostcode::whereNotNull('id')->delete();
        }
        return redirect()->route('admin.postcode.index');
    }

    public function getValidPostcodes(Request $request) {
        $searchParam    = $request->input('term');
        $postcodeModel  = new ValidPostcode();
        return $postcodeModel->getValidPostcodes($searchParam, $exactSearch = false);
    }

    /*
     * Method to Validate Postcode on Landing Page
     */
    public function validatePostcode(Request $request, CartController $cartControllerInstance) {
        $postcode = $request['postcode'];
        $postcodeModel = new ValidPostcode();
        $postCodeDetails = $postcodeModel->getPostcodeDetail($postcode);
        $userLandingPostcodeSessionKey = \Config::get('appConstants.user_landing_postcode_session_key');
        $userLandingLatSessionKey = \Config::get('appConstants.user_landing_lat_session_key');
        $userLandingLngSessionKey = \Config::get('appConstants.user_landing_lng_session_key');
        $returnVar = array(
            'isServiceable' => false,
            'postcode' => $postcode,
        );
        if (!empty($postCodeDetails)) {
            $postCode = $postCodeDetails['postcode'];
            $lat = $postCodeDetails['lat'];
            $lng = $postCodeDetails['lng'];            
            $nearByStores = CommonHelper::getNearByStoresForOrder($postCode, $lat, $lng);
            if (!empty($nearByStores)) {
                $productModel = new Product();
                $products = $productModel->getStoreProductCount($nearByStores);
                if (!empty($products)) {
                    ValidPostcode::logPostcode($postCode, $isSuccess = 1);
                    $cartControllerInstance->comparePostcodeAndResetCart($postCode);
                    session()->put($userLandingPostcodeSessionKey, $postCode);
                    session()->put($userLandingLatSessionKey, $lat);
                    session()->put($userLandingLngSessionKey, $lng);
                    return Redirect::route('home.index');
                }
            }
            ValidPostcode::logPostcode($postCode, $isSuccess = 0);
            session()->forget($userLandingPostcodeSessionKey);
            session()->forget($userLandingLatSessionKey);
            session()->forget($userLandingLngSessionKey);
            return redirect()->back()->with('postcode_msg', trans('messages.postcode_error_no_products'))->with($returnVar);
        } else {
            ValidPostcode::logPostcode($postcode, $isSuccess = 0);
            session()->forget($userLandingPostcodeSessionKey);
            session()->forget($userLandingLatSessionKey);
            session()->forget($userLandingLngSessionKey);
            return redirect()->back()->with($returnVar);
        }
    }

    /**
     * Method to get Lat Lng of postcode
     * Call Common Function which makes API Hit to fetch LAT LNG
     *
     * @param string $postCode Postcode
     *
     */
    public function getLatLngForPostcode($postCode) {
        $data = array(
            'lat' => '',
            'lng' => '',
        );
        $commonModel    = new CommonHelper();
        $latlngResponse = $commonModel->getLatLngFromPostCode($postCode);
        if ($latlngResponse['status']) {
            $data['lat'] = $latlngResponse['data']['lat'];
            $data['lng'] = $latlngResponse['data']['lng'];
        }
        return $data;
    }

    /**
     * Method to download postcode log file
     *
     */
    public function download() {
        $filename   =   "postcode_log.csv";
        $file_path  =   public_path().'/files/'.$filename;
        header('Content-Type: text/csv; charset=utf-8');
        header('Content-Disposition: attachment; filename='.$filename);
        readfile($file_path);
   }
}
