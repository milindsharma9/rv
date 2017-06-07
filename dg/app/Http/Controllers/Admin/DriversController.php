<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\User;
use Validator;
use Illuminate\Support\Facades\Redirect;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\DB;

use App\Http\Controllers\Traits\FileUploadTrait;
use App\Http\Controllers\Controller;
use Auth;
use App\EventLog;

class DriversController extends Controller {
    const FILE_SUB_DIR = 'driver';

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index() {
        $drivers = User::where('fk_users_role' , '=', '4')->get();
        $fileSubDir = self::FILE_SUB_DIR;
        return view('admin.drivers.index', compact('drivers', 'fileSubDir'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create() {
        $driverFormRoute = 'admin.drivers.store';
        return view('admin.drivers.create', compact('driverFormRoute'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request) {
        DB::enableQueryLog();
        $userModel      = new User();
        $rules          = $userModel->getDriverValidationRules();
        $messages       = $userModel->getDriverValidationMessages();
        $validator      = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        }
        $vehicleType        = $request['vehicle'];
        $vehicleQuestions   = config('rider_configurations.'.$vehicleType.'_question');
        $data               = $request->all();
        foreach ($vehicleQuestions as $qId => $question) {
            if ($qId == 8)
                continue;
            if (!isset($data['question_'.$qId])) {
                return redirect()->back()->withInput()->withErrors('Please answer for all required questions.');
            }
        }
        $data['activated']  = 1;
        $data['password']   = '';
        $driverRegistration = $userModel->saveDriverRegistrationData($data);
        if ($driverRegistration['status']) {
            /* Insert data to the log table for vendor add */
            $logData = array(
                'users_id'          => Auth::user()->id,
                'operation_type'    => EventLog::EVENT_DRIVER_ADD,
                'al_event'          => serialize(DB::getQueryLog()),
            );
            EventLog::logEvent($logData);
            /* End */
            //return redirect()->back()->with('status', $driverRegistration['message']);
            return redirect()->route('admin.drivers.index');
        } else {
            return redirect()->back()->withInput()->withErrors($driverRegistration['message']);
        }
        return redirect()->route('admin.drivers.index');
        
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id) {
        $drivers = User::find($id);
        return view('admin.drivers.index', compact('drivers'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id) {
        $drivers = User::find($id);
        if (empty($drivers)) {
            return redirect()->route('admin.drivers.index');
        }
        $userModel          = new User();
        $driver             = $userModel->getDriverDetialsEdit($id);
        $driverFormRoute    = 'admin.drivers.store';
        $aQuestionResponse  = array();
        // Prepare data to show checkbox selected
        $aQuestionId        = explode(",", $driver['fk_question_id']);
        $aResponse          = explode(",", $driver['question_response']);
        foreach ($aQuestionId as $qIndex => $qid) {
            $aQuestionResponse[$qid] = $aResponse[$qIndex];
        }
       
        $driver['responses']        = $aQuestionResponse;
        $driverAvalability          = $userModel->getDriverAvalability($id);
        $driver['availability']     = $driverAvalability;
        $aDay       = explode(",", $driverAvalability['day']);
        $aTimeId    = explode(",", $driverAvalability['time_id']);
        $aDayTimeSelValue = array();
        foreach ($aDay as $index => $dayName) {
            $aDayTimeSelValue[$dayName."_".$aTimeId[$index]] = $dayName."_".$aTimeId[$index];
        }
        $driver['availability_select']  = $aDayTimeSelValue;
        return view('admin.drivers.edit', compact('driver', 'driverFormRoute'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update($id, Request $request) {
        $drivers = User::find($id);
        if (empty($drivers)) {
            return redirect()->route('admin.drivers.index');
        }
        DB::enableQueryLog();
        $userModel      = new User();
        $rules          = $userModel->getDriverValidationRules();
        unset($rules['email']);
        $rules['password'] = 'min:6|confirmed';
        $messages       = $userModel->getDriverValidationMessages();
        $validator      = Validator::make($request->all(), $rules, $messages);
        if ($validator->fails()) {
            return redirect()->back()->withInput()->withErrors($validator);
        }
        $vehicleType        = $request['vehicle'];
        $vehicleQuestions   = config('rider_configurations.'.$vehicleType.'_question');
        $data               = $request->all();
        foreach ($vehicleQuestions as $qId => $question) {
            if ($qId == 8)
                continue;
            if (!isset($data['question_'.$qId])) {
                return redirect()->back()->withInput()->withErrors('Please answer for all required questions.');
            }
        }
        $request->merge(['activated' => $request->get('activated', '0')]);
        $request->merge(['is_right_to_work' => $request->get('is_right_to_work', '0')]);
        $data               = $request->all();
        $driverRegistration = $userModel->updateDriverDetails($data, $id);
        /* Insert data to the log table for Driver update */
        $logData = array(
            'users_id'          => Auth::user()->id,
            'operation_type'    => EventLog::EVENT_DRIVER_UPDATE,
            'al_event'          => serialize(DB::getQueryLog()),
        );
        EventLog::logEvent($logData);
        /* End */
        return redirect()->route('admin.drivers.index');
        
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id) {
        DB::enableQueryLog();
        User::destroy($id);
        /* Insert data to the log table for Driver update */
            $logData = array(
                'users_id'          => Auth::user()->id,
                'operation_type'    => EventLog::EVENT_DRIVER_DELETE,
                'al_event'          => serialize(DB::getQueryLog()),
            );
            EventLog::logEvent($logData);
            /* End */
        return Redirect::route('admin.drivers.index');
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
            User::destroy($toDelete);
            /* Insert data to the log table for Driver update */
            $logData = array(
                'users_id'          => Auth::user()->id,
                'operation_type'    => EventLog::EVENT_DRIVER_MASS_DELETE,
                'al_event'          => serialize(DB::getQueryLog()),
            );
            EventLog::logEvent($logData);
            /* End */
            
        } else {
            User::whereNotNull('id')->delete();
        }
        return redirect()->route('admin.drivers.index');
    }

}
