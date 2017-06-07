<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Occasion;
use Validator;
use Illuminate\Support\Facades\Redirect;

use App\Http\Controllers\Controller;
use App\Http\Helper\FileUpload;
use Exception;
use Log;

use DB;
use Auth;
use App\EventLog;

class OccasionsController extends Controller {

    const FILE_SUB_DIR = 'occasions';

    private $occasionModel = null;

    private $rules = array (
        'name'          => 'required',
        'image'         => 'required',
        'image_logo'    => 'required',
        'image_banner'  => 'required',
    );

    private $rulesForSubOccasion = array (
        'name'          => 'required',
        'image'         => 'required',
        'image_logo'    => 'required',
        'image_banner'  => 'required',
    );

    /**
     *
     * @var array File Validation for different type
     */
    private $aImageValidationRules = array(
        'image_banner'         => array(
            'min_height'    => '260',
            'min_width'     => '1024',
        ),
        'image_logo'    => array(),
        'image'  => array(
            'min_height'    => '400',
            'min_width'     => '400',
        ),
    );

    private $fileUploader       = null;

    public function __construct () {
        $this->fileUploader = new FileUpload();
    }

    private function getOccasionModel() {
        if ($this->occasionModel == null) {
            $this->occasionModel = new Occasion();
        }
        return $this->occasionModel;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index() {
        $occasions          = Occasion::orderBy('created_at', 'desc')->get();
        $fileSubDir         = self::FILE_SUB_DIR;
        $allOccasions       = $this->getOccasionModel()->getOccasionsList();
        return view('admin.occasions.index', compact('occasions', 'fileSubDir', 'allOccasions'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create() {
        $primaryOccasions = $this->getOccasionModel()->getPrimaryOccasions();
        return view('admin.occasions.create', compact('primaryOccasions'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request) {
        $validateFile = $this->fileUploader->validateFiles($this->aImageValidationRules);
        if ($validateFile['status']) {
            if ($request['parent_id'] == 0) {
                $rules = $this->rules;
            } else {
                $rules = $this->rulesForSubOccasion;
            }
            $sortOrder = $request['sort_order'];
            if ($sortOrder != 100) {
               $rules['sort_order'] = 'required|unique:occasions,sort_order,NULL,id,deleted_at,NULL';
            }
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return Redirect::to('/admin/occasions/create')->withInput()->withErrors($validator);
            }
            $request = $this->fileUploader->saveFiles($request, self::FILE_SUB_DIR);
            if (isset($request['is_banner'])) {
                $request['is_banner'] = 1;
                $this->getOccasionModel()->resetBannerImage();
            } else{
                $request['is_banner'] = 0;
            }
            Occasion::create($request->all());
            $this->getOccasionModel()->clearOccasionCache();
            return redirect()->route('admin.occasions.index');
        } else {
            return Redirect::to('/admin/occasions/create')->withInput()->withErrors($validateFile['error']);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id) {
        $occasions = Occasion::find($id);
        return view('admin.occasions.index', compact('occasions'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id) {
        $occasions          = Occasion::find($id);
        $primaryOccasions   = $this->getOccasionModel()->getPrimaryOccasions();
        return view('admin.occasions.edit', compact('occasions', 'primaryOccasions'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update($id, Request $request) {
        DB::enableQueryLog();
        $events = Occasion::findOrFail($id);
        $validateFile = $this->fileUploader->validateFiles($this->aImageValidationRules);
        if ($validateFile['status']) {
            $rules = array(
                'name' => 'required',
            );
            $sortOrder = $request['sort_order'];
            if ($sortOrder != 100) {
               $rules['sort_order'] = 'required|unique:occasions,sort_order,'.$id.',id,deleted_at,NULL';
            }
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return redirect()
                    ->back()
                    ->withInput($request->all())
                    ->withErrors($validator);
            }
            $request = $this->fileUploader->saveFiles($request, self::FILE_SUB_DIR);
            if (isset($request['is_banner'])) {
                $request['is_banner'] = 1;
                $this->getOccasionModel()->resetBannerImage();
            } else {
                $request['is_banner'] = 0;
            }
            $events->update($request->all());
            
            /* Insert data to the log table for Occasion update */
            $logData = array(
                'users_id'          => Auth::user()->id,
                'operation_type'    => EventLog::EVENT_OCCASSION_UPDATE,
                'al_event'          => serialize(DB::getQueryLog()),
            );
            EventLog::logEvent($logData);
            /* End */
            
            $this->getOccasionModel()->clearOccasionCache();
            return redirect()->route('admin.occasions.index');
        } else {
            return redirect()
                    ->back()
                    ->withInput($request->all())
                    ->withErrors($validateFile['error']);
        }
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id) {
        DB::enableQueryLog();
        $hasSubOccasions = $this->getOccasionModel()->hasSubOccasions(array($id));
        if (empty($hasSubOccasions)) {
            Occasion::destroy($id);
            
            /* Insert data to the log table for Occasion delete */
            $logData = array(
                'users_id'          => Auth::user()->id,
                'operation_type'    => EventLog::EVENT_OCCASSION_DELETE,
                'al_event'          => serialize(DB::getQueryLog()),
            );
            EventLog::logEvent($logData);
            /* End */
            
            $message = 'You have successfully deleted Occasion';
        } else {
            $message = "This occasions has suboccasions associated with it. Please delete them first.";
        }
        $this->getOccasionModel()->clearOccasionCache();
        \Session::flash('message', $message);
        return Redirect::route('admin.occasions.index');
    }
    
    /**
     * Mass delete function from index page
     * @param Request $request
     *
     * @return mixed
     */
    public function massDelete(Request $request) {
        DB::enableQueryLog();
        $message = "You have successfully deleted Occasions";
        if ($request->get('toDelete') != 'mass') {
            $toDelete = json_decode($request->get('toDelete'));
            $hasSubOccasions = $this->getOccasionModel()->hasSubOccasions($toDelete);
            if (empty($hasSubOccasions)) {
                Occasion::destroy($toDelete);
                
                /* Insert data to the log table for Occasion mass delete */
                $logData = array(
                    'users_id'          => Auth::user()->id,
                    'operation_type'    => EventLog::EVENT_OCCASSION_MASS_DELETE,
                    'al_event'          => serialize(DB::getQueryLog()),
                );
                EventLog::logEvent($logData);
                /* End */
                
            } else {
                $message = "Some occasions has suboccasions associated with them. Please delete them first.";
            }
        } else {
            Occasion::whereNotNull('id')->delete();
        }
        $this->getOccasionModel()->clearOccasionCache();
        \Session::flash('message', $message);
        return redirect()->route('admin.occasions.index');
    }
    
    /**
     * Get Subcatergories of occasions.
     * @param type $occasionId
     * @return type
     */
    public function getSubOccasion($occasionId) {
        $aSecondaryOccasions = array();
        $occasionName = '';
        try {
            $aOccasions  = $this->getOccasionModel()->getOccassionTree();
            if (isset($aOccasions[$occasionId]['subOccasions'])) {
                $occasionName = $aOccasions[$occasionId]['name'];
                $aSecondaryOccasions = $aOccasions[$occasionId]['subOccasions'];
            }
        } catch (Exception $ex) {
            Log::error(__METHOD__ . $ex->getMessage());
        }
        $view = view('customer.partials.sub_occasion_themes_popup', 
            [
                'occasionName' => $occasionName,
                'subOccasions' => $aSecondaryOccasions,
            ]
        );
        $view               = $view->render();
        $response['html_content'] = (string) $view;
        return $response;
    }

}
