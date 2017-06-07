<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Validator;
use App\ContactUs;
use DB;
use Auth;
use App\EventLog;


class ContactusController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index() {
        $contact = ContactUs::orderBy('id', 'DESC')->get();
        return view('admin.contact.index', compact('contact'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id) {
        $contact         = ContactUs::find($id);
        return view('admin.contact.edit', compact('contact'));
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
            $contact = ContactUs::findOrFail($id);
            $aInputRequest  = $request->all();
            $contact->update($aInputRequest);
            
            /* Insert data to the log table for contact page update */
            $logData = array(
                'users_id'          => Auth::user()->id,
                'operation_type'    => EventLog::EVENT_CONTACTUS_UPDATE,
                'al_event'          => serialize(DB::getQueryLog()),
            );
            EventLog::logEvent($logData);
            /* End */
            
            return redirect()->route('admin.contact.index');
        } catch (Exception $ex) {
            return redirect()->back()->withInput($request->all())->withErrors($ex->getMessage());
        }
    }
}
