<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\Redirect;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\FaqCategory;
use App\Faq;
use Exception;
use Illuminate\Support\Facades\Auth;
use DB;
use App\EventLog;

class FaqController extends Controller {
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index() {
        $faq = Faq::all();
        $cat = FaqCategory::all();
        $faqCat = array();
        foreach ($cat as $catn => $catname) {
            $faqCat[$catname->id] = $catname->category_name;
        }
        return view('admin.faq.index', compact('faq', 'faqCat'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create() {
        $cat = FaqCategory::all();
        $faqCat = array();
        foreach ($cat as $catn => $catname) {
            $faqCat[$catname->id] = $catname->category_name;
        }
        $userGroup = config('faq.user_group');
        return view('admin.faq.create', compact('userGroup', 'faqCat'));
    }

    public function store(Request $request) {
        DB::enableQueryLog();
        try {
            $rules = array(
                'title' => 'required',
                'description' => 'required',
                'category' => 'required',
                'user_group' => 'required',
            );
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return Redirect::to('/admin/faq/create')->withInput()->withErrors($validator);
            }
            $aInputRequest = $request->all();
            $aUserGroupId = $aInputRequest['user_group'];
            unset($aInputRequest['user_group']);
            $faq = Faq::create($aInputRequest);
            $faqId = $faq->id;
            $faqModel = new Faq();
            $faqModel->updateFaqUserGroupMapping($faqId, $aUserGroupId);
            
            /* Insert data to the log table for FAQ add */
            $logData = array(
                'users_id'          => Auth::user()->id,
                'operation_type'    => EventLog::EVENT_FAQ_ADD,
                'al_event'          => serialize(DB::getQueryLog()),
            );
            EventLog::logEvent($logData);
            /* End */
            
            return redirect()->route('admin.faq.index');
        } catch (Exception $ex) {
            return Redirect::to('/admin/faq/create')->withInput()->withErrors($ex->getMessage());
        }
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id) {
        $faq = Faq::find($id);
        $userGroup = config('faq.user_group');
        $faqModel = new Faq();
        $userGroupId = $faqModel->getfaqUserGroupIds($id);
        $cat = FaqCategory::all();
        $faqCat = array();
        foreach ($cat as $catn => $catname) {
            $faqCat[$catname->id] = $catname->category_name;
        }
        return view('admin.faq.edit', compact('faq', 'userGroup', 'userGroupId', 'faqCat'));
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
            $faq = Faq::findOrFail($id);
            $rules = array(
                'title' => 'required',
                'description' => 'required',
                'category' => 'required',
                'user_group' => 'required',
            );
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return redirect()
                    ->back()
                    ->withInput($request->all())
                    ->withErrors($validator);
            }
            $aInputRequest = $request->all();
            $aUserGroupId = $aInputRequest['user_group'];
            unset($aInputRequest['user_group']);
            $faq->update($aInputRequest);
            $faqModel = new Faq();
            $faqModel->updateFaqUserGroupMapping($id, $aUserGroupId);
            
            /* Insert data to the log table for FAQ update */
            $logData = array(
                'users_id'          => Auth::user()->id,
                'operation_type'    => EventLog::EVENT_FAQ_UPDATE,
                'al_event'          => serialize(DB::getQueryLog()),
            );
            EventLog::logEvent($logData);
            /* End */
            
            return redirect()->route('admin.faq.index');
        } catch (Exception $ex) {
            return redirect()->back()->withInput($request->all())->withErrors($ex->getMessage());
        }
    }

    public function destroy($id) {
        DB::enableQueryLog();
        Faq::destroy($id);
        
        /* Insert data to the log table for FAQ delete */
            $logData = array(
                'users_id'          => Auth::user()->id,
                'operation_type'    => EventLog::EVENT_FAQ_DELETE,
                'al_event'          => serialize(DB::getQueryLog()),
            );
            EventLog::logEvent($logData);
            /* End */
        
        \Session::flash('message', 'You have successfully deleted Faq');
        return Redirect::route('admin.faq.index');
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
            Faq::destroy($toDelete);
            
            /* Insert data to the log table for FAQ mass delete */
            $logData = array(
                'users_id'          => Auth::user()->id,
                'operation_type'    => EventLog::EVENT_FAQ_MASS_DELETE,
                'al_event'          => serialize(DB::getQueryLog()),
            );
            EventLog::logEvent($logData);
            /* End */
            
        } else {
            Faq::whereNotNull('id')->delete();
        }
        return redirect()->route('admin.faq.index');
    }

    /**
     * Method to render FAQ page for customer & stores
     *
     * @return view Object
     */
    public function getFaqs($isApi = NULL) {
        try {
            $faqModel = new Faq();
            $aFaqList = $faqModel->getFaqsDetails();
            $userGroup = config('faq.user_group');
            $templatePrefix = 'customer';
            if (!empty($isApi) && ($isApi == 'api')) {
                $templatePrefix = 'api';
            } else {
                if (Auth::user() && Auth::user()->fk_users_role == \Config::get('appConstants.vendor_role_id')) {
                    $templatePrefix = 'store';
                }
            }
            return view($templatePrefix . '.faq', compact('aFaqList', 'userGroup'));
        } catch (Exception $ex) {
            return view('errors.404');
        }
    }

    /**
     * Display a listing of the Faq Categories.
     *
     * @return Response
     */
    public function faqCategorylist() {
        $faqCat = FaqCategory::all();
        return view('admin.faqCategory.index', compact('faqCat'));
    }

    /**
     * Add a new Faq Categories.
     *
     * @return Response
     */
    public function faqCategoryadd() {
        return view('admin.faqCategory.create', compact('faqCat'));
    }
    
     /**
     * Save Faq Categories.
     *
     * @return Response
     */
    public function savefaqCat(Request $request) {
        try {
            //print_r($request->all());exit;
            $rules = array(
                'category_name' => 'required',
            );
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return Redirect::to('/admin/faq/category/list')->withInput()->withErrors($validator);
            }
            FaqCategory::create($request->all());

            return redirect()->route('admin.faq.category.list');
        } catch (Exception $ex) {
            return Redirect::to('/admin/faq/category/add')->withInput()->withErrors($ex->getMessage());
        }
    }

    /**
     * Display a listing of the Faq Categories.
     *
     * @return Response
     */
    public function faqCategoryedit($id) {
        $faq = FaqCategory::find($id);
        return view('admin.faqCategory.edit', compact('faq'));
    }

    /**
     * Update Faq Categories.
     *
     * @return Response
     */
    public function faqCategoryupdate(Request $request) {
        try {
            $aInputRequest = $request->all();
            $faq = FaqCategory::findOrFail($aInputRequest['id']);
            $rules = array(
                'category_name' => 'required',
            );
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return redirect()
                    ->back()
                    ->withInput($aInputRequest['category_name'])
                    ->withErrors($validator);
            }
            $faq->update($aInputRequest);

            return redirect()->route('admin.faq.category.list');
        } catch (Exception $ex) {
            return redirect()->back()->withInput($request->all())->withErrors($ex->getMessage());
        }
    }

}
