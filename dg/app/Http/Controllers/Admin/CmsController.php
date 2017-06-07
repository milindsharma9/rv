<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Validator;
use App\Cms;

use DB;
use Auth;
use App\EventLog;


class CmsController extends Controller
{
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index() {
        $cms = Cms::all();
        return view('admin.cms.index', compact('cms'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create() {
        return view('admin.cms.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request) {
        DB::enableQueryLog();
        try {
            $userType   = $request->get('user_type');
            $rules      = array(
                'description'   => 'required',
                'user_type'     => 'required',
                'title'         => 'required|unique:cms,title,NULL,id,user_type,'.$userType,
                'url_path'      => 'required|unique:cms,url_path,NULL,id,user_type,'.$userType,
            );
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return redirect()
                    ->back()
                    ->withInput($request->all())
                    ->withErrors($validator);
            }
            $request->merge(['published' => $request->get('published', '0')]);
            $aInputRequest  = $request->all();
            CMS::create($aInputRequest);
            /* Insert data to the log table for cms page update */
            $logData = array(
                'users_id'          => Auth::user()->id,
                'operation_type'    => EventLog::EVENT_CMS_ADD,
                'al_event'          => serialize(DB::getQueryLog()),
            );
            EventLog::logEvent($logData);
            /* End */
            Cms::flushCmsPageCache();
            return redirect()->route('admin.cms.index');
        } catch (Exception $ex) {
            return redirect()->back()->withInput($request->all())->withErrors($ex->getMessage());
        }
    }
    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id) {
        $cms         = Cms::find($id);
        return view('admin.cms.edit', compact('cms'));
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
            $userType = $request->get('user_type');
            $cms = Cms::findOrFail($id);
            $rules = array(
                'description'   => 'required',
                'user_type'     => 'required',
                //'url_path'      => 'required|unique:cms,url_path,'.$userType.',user_type',
                'url_path'      => 'required|unique:cms,url_path,'.$id.',id,user_type,'.$userType,
            );
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return redirect()
                    ->back()
                    ->withInput($request->all())
                    ->withErrors($validator);
            }
            $request->merge(['published' => $request->get('published', '0')]);
            $aInputRequest  = $request->all();
            $cms->update($aInputRequest);
            
            /* Insert data to the log table for cms page update */
            $logData = array(
                'users_id'          => Auth::user()->id,
                'operation_type'    => EventLog::EVENT_CMS_UPDATE,
                'al_event'          => serialize(DB::getQueryLog()),
            );
            EventLog::logEvent($logData);
            /* End */
            Cms::flushCmsPageCache();
            return redirect()->route('admin.cms.index');
        } catch (Exception $ex) {
            return redirect()->back()->withInput($request->all())->withErrors($ex->getMessage());
        }
    }
}
