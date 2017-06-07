<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Event;
use Validator;
use Illuminate\Support\Facades\Redirect;

use App\Http\Controllers\Controller;
use App\Http\Helper\FileUpload;
use Exception;
use Log;

use DB;
use Auth;
use App\EventLog;

class EventsController extends Controller {

    const FILE_SUB_DIR = 'events';

    private $eventModel = null;

    private $rules = array (
        'name'          => 'required',
        'image'         => 'required',
        'image_logo'    => 'required',
        'image_banner'  => 'required',
    );

    private $rulesForSubEvent = array (
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

    private function getEventModel() {
        if ($this->eventModel == null) {
            $this->eventModel = new Event();
        }
        return $this->eventModel;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index() {
        $events         = Event::orderBy('created_at', 'desc')->get();
        $allEvents      = $this->getEventModel()->getEventsList();
        $fileSubDir     = self::FILE_SUB_DIR;
        return view('admin.events.index', compact('events', 'allEvents', 'fileSubDir'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create() {
        $primaryEvents = $this->getEventModel()->getPrimaryEvents();
        return view('admin.events.create', compact('primaryEvents'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request) {
        DB::enableQueryLog();
        $validateFile = $this->fileUploader->validateFiles($this->aImageValidationRules);
        if ($validateFile['status']) {
            if ($request['parent_id'] == 0) {
                $rules = $this->rules;
            } else {
                $rules = $this->rulesForSubEvent;
            }
            $sortOrder = $request['sort_order'];
            if ($sortOrder != 100) {
               $rules['sort_order'] = 'required|unique:events,sort_order,NULL,id,deleted_at,NULL';
            }
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return Redirect::to('/admin/events/create')->withInput()->withErrors($validator);
            }
            $request = $this->fileUploader->saveFiles($request, self::FILE_SUB_DIR);
            Event::create($request->all());
            
            /* Insert data to the log table for Event/Theme add */
            $logData = array(
                'users_id'          => Auth::user()->id,
                'operation_type'    => EventLog::EVENT_THEME_ADD,
                'al_event'          => serialize(DB::getQueryLog()),
            );
            EventLog::logEvent($logData);
            /* End */
            
            $this->getEventModel()->clearEventCache();
            return redirect()->route('admin.events.index');
        } else {
            return Redirect::to('/admin/events/create')->withInput()->withErrors($validateFile['error']);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id) {
        $event = Event::find($id);
        return view('admin.events.index', compact('events'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id) {
        $events         = Event::find($id);
        $primaryEvents  = $this->getEventModel()->getPrimaryEvents();
        return view('admin.events.edit', compact('events', 'primaryEvents'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update($id, Request $request) {
        DB::enableQueryLog();
        $events = Event::findOrFail($id);
        $validateFile = $this->fileUploader->validateFiles($this->aImageValidationRules);
        if ($validateFile['status']) {
            $rules = array(
                'name' => 'required',
            );
            $sortOrder = $request['sort_order'];
            if ($sortOrder != 100) {
               $rules['sort_order'] = 'required|unique:events,sort_order,'.$id.',id,deleted_at,NULL';
            }
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return redirect()
                    ->back()
                    ->withInput($request->all())
                    ->withErrors($validator);
            }
            $request = $this->fileUploader->saveFiles($request, self::FILE_SUB_DIR);
            $events->update($request->all());
            
            /* Insert data to the log table for theme/event update */
            $logData = array(
                'users_id'          => Auth::user()->id,
                'operation_type'    => EventLog::EVENT_THEME_UPDATE,
                'al_event'          => serialize(DB::getQueryLog()),
            );
            EventLog::logEvent($logData);
            /* End */
            
            $this->getEventModel()->clearEventCache();
            return redirect()->route('admin.events.index');
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
        $hasSubEvents = $this->getEventModel()->hasSubEvents(array($id));
        if (empty($hasSubEvents)) {
            Event::destroy($id);
            
            /* Insert data to the log table for theme/event delete */
            $logData = array(
                'users_id'          => Auth::user()->id,
                'operation_type'    => EventLog::EVENT_THEME_DELETE,
                'al_event'          => serialize(DB::getQueryLog()),
            );
            EventLog::logEvent($logData);
            /* End */
            
            $message = 'You have successfully deleted Event';
        } else {
            $message = "This event has subevents associated with it. Please delete them first.";
        }
        $this->getEventModel()->clearEventCache();
        \Session::flash('message', $message);
        return Redirect::route('admin.events.index');
    }
    
    /**
     * Mass delete function from index page
     * @param Request $request
     *
     * @return mixed
     */
    public function massDelete(Request $request) {
        DB::enableQueryLog();
        $message = "You have successfully deleted Events";
        if ($request->get('toDelete') != 'mass') {
            $toDelete = json_decode($request->get('toDelete'));
            $hasSubEvents = $this->getEventModel()->hasSubEvents($toDelete);
            if (empty($hasSubEvents)) {
                Event::destroy($toDelete);
                
                 /* Insert data to the log table for theme/event mass delete */
                $logData = array(
                    'users_id'          => Auth::user()->id,
                    'operation_type'    => EventLog::EVENT_THEME_MASS_DELETE,
                    'al_event'          => serialize(DB::getQueryLog()),
                );
                EventLog::logEvent($logData);
                /* End */
                
                
            } else {
                $message = "Some events has subevents associated with them. Please delete them first.";
            }
        } else {
            Event::whereNotNull('id')->delete();
        }
        $this->getEventModel()->clearEventCache();
        \Session::flash('message', $message);
        return redirect()->route('admin.events.index');
    }

    /**
     * Get sub categories of events.
     * @param type $eventId
     * @return type
     */
    public function getSubEvents($eventId) {
        $aSecondaryEvents = array();
        $eventName = '';
        try {
            $aEvents  = $this->getEventModel()->getEventTree();
            if (isset($aEvents[$eventId]['subEvents'])) {
                $eventName = $aEvents[$eventId]['name'];
                $aSecondaryEvents = $aEvents[$eventId]['subEvents'];
            }
        } catch (Exception $ex) {
            Log::error(__METHOD__ . $ex->getMessage());
        }
        $view = view('customer.partials.sub_occasion_themes_popup', 
            [
                'occasionName' => $eventName,
                'subOccasions' => $aSecondaryEvents,
                'isTheme'        => true,
            ]
        );
        $view               = $view->render();
        $response['html_content'] = (string) $view;
        return $response;
    }

}
