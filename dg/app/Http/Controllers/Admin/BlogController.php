<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Validator;
use App\Blog;
use App\BlogMeta;
use App\Keyword;
use DB;
use Auth;
use App\EventLog;
use Exception;

use Intervention\Image\Facades\Image;
use App\Http\Helper\FileUpload;


class BlogController extends Controller
{
    /**
     * Constant for image folder.
     */
    const FILE_SUB_DIR = 'blog';
    
    /**
     *
     * @var type $fileUploader
     */
    private $fileUploader   = null;
    
    /**
     *
     * @var type Blog
     */
    private $_blogModel     = null;
    
    
    /**
     * Contructor. 
     * 
     */
    public function __construct () {
        $this->fileUploader = new FileUpload();
    }
    
    /**
     * Method to instantiate BlogModel.
     * 
     * @return object BlogModel.
     */
    private function getBlogModel() {
        if ($this->_blogModel == null) {
            $this->_blogModel = new Blog();
        }
        return $this->_blogModel;
    } 
    
    
    /**
     *
     * @var array File Validation for different type
     */
    private $aImageValidationRules = array(
        'image_thumb'         => array(
            'min_height'    => '400',
            'min_width'     => '400',
        ),
        'image'  => array(
            'min_width'     => '1024',
            'min_height'    => '170',
            'max_height'    => '300',
        ),
    );
    
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index() {
        $blog = Blog::all();
        return view('admin.blog.index', compact('blog'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create() {
        return view('admin.blog.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request) {
//        DB::enableQueryLog();
        try {
            $validateFile = $this->fileUploader->validateFiles($this->aImageValidationRules);
            if ($validateFile['status']) {
                $request = $this->fileUploader->saveFiles($request, self::FILE_SUB_DIR);
                $userType   = $request->get('type');
                $validator = Validator::make($request->all(), $this->getBlogModel()->validationRules($userType), $this->getValidationRules());
                if ($validator->fails()) {
                    return redirect()
                        ->back()
                        ->withInput($request->all())
                        ->withErrors($validator);
                }
                $request->merge(['published' => $request->get('published', '0')]);
                $request['start_date'] = date("Y-m-d H:i", strtotime($request->get('start_date')));
                $request['end_date'] = date("Y-m-d H:i", strtotime($request->get('end_date')));
                $aInputRequest  = $request->all();
                $aInputRequest['description'] = str_replace('../../../uploads', url('uploads'), $aInputRequest['description']);
                $blog = Blog::create($aInputRequest);
                if($blog){
                    $aInputRequest['fk_master_blog_id'] = $blog->id;
                    BlogMeta::create($aInputRequest);
                    $this->getBlogModel()->saveBlogKeywordMapping($blog->id, explode(',', $request->get('keywords')));
                    /* Insert data to the log table for blog page update */
//                    $logData = array(
//                        'users_id'          => Auth::user()->id,
//                        'operation_type'    => EventLog::EVENT_BLOG_ADD,
//                        'al_event'          => serialize(DB::getQueryLog()),
//                    );
//                    EventLog::logEvent($logData);
                    /* End */
                    return redirect()->route('admin.blog.index');
                }
            } else {
                return redirect()->back()->withInput($request->all())->withErrors($validateFile['error']);
            }
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
        $blog               = $this->getBlogModel()->getBlogDataById($id);
        $blog->start_date   = date("Y-m-d H:i", strtotime($blog->start_date));
        if ($blog->end_date != '0000-00-00 00:00:00') {
            $blog->end_date     = date("Y-m-d H:i", strtotime($blog->end_date));
        } else {
            $blog->end_date = '';
        }
        return view('admin.blog.edit', compact('blog'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update($id, Request $request) {
//        DB::enableQueryLog();
        try {
            $userType = $request->get('blog_type');
            $blog = Blog::findOrFail($id);
            $validateFile = $this->fileUploader->validateFiles($this->aImageValidationRules);
            if ($validateFile['status']) {
                $request = $this->fileUploader->saveFiles($request, self::FILE_SUB_DIR);
                $validator = Validator::make($request->all(), $this->getBlogModel()->validationRules($userType, $id), $this->getValidationRules());
                if ($validator->fails()) {
                    return redirect()
                        ->back()
                        ->withInput($request->all())
                        ->withErrors($validator);
                }
                $request->merge(['published' => $request->get('published', '0')]);
                $request['start_date'] = date("Y-m-d H:i", strtotime($request->get('start_date')));
                if (!empty($request->get('end_date'))) {
                    $request['end_date'] = date("Y-m-d H:i", strtotime($request->get('end_date')));
                }
                $aInputRequest  = $request->all();
                $aInputRequest['description'] = str_replace('../../../uploads', url('uploads'), $aInputRequest['description']);
                $blog->update($aInputRequest);
                $blogMeta = BlogMeta::where('fk_master_blog_id', '=', $blog->id)->first();
                $this->getBlogModel()->saveBlogKeywordMapping($blog->id, explode(',', $request->get('keywords')));
                if($blogMeta){
                    $blogMeta->update($aInputRequest);
                }else{
                    $aInputRequest['fk_master_blog_id'] = $blog->id;
                    BlogMeta::create($aInputRequest);
                }

                /* Insert data to the log table for blog page update */
//                $logData = array(
//                    'users_id'          => Auth::user()->id,
//                    'operation_type'    => EventLog::EVENT_BLOG_UPDATE,
//                    'al_event'          => serialize(DB::getQueryLog()),
//                );
//                EventLog::logEvent($logData);
                /* End */
                return redirect()->route('admin.blog.index');
            } else {
                return redirect()->back()->withInput($request->all())->withErrors($validateFile['error']);
            }
        } catch (Exception $ex) {
            return redirect()->back()->withInput($request->all())->withErrors($ex->getMessage());
        }
    }
    
    /**
     * Function to return all the keywords available in the system.
     * 
     * @return Object Keyword
     */
    public function getAllKeywords(Request $request) {
        $searchTerm = $request->get('q', '');
        return Keyword::orderBy('name', 'asc')->where('name', 'LIKE', "%{$searchTerm}%")->take(5)->get();
    }
    
    /**
     * Method to return saved keyword for a blog.
     * 
     * @param Request $request
     * @return json
     */
    public function getSavedKeywords(Request $request) {
        $response = [];
        try {
            $id = $request->get('id');
            if(isset($id)){
                $data = $this->getBlogModel()->getKeywordForToken($id, $request->get('type', 'blog'));
                $response = json_decode(json_encode($data), true);
            }else{
                $response = json_decode(['status' => 'error', 'message' => 'some error occured']);
            }
        } catch (Exception $ex) {
             Log::error(__METHOD__ . $ex->getMessage());
        }
        return $response;
    }

    /**
     * Method to overwrite default validation messages
     * @return array
     */
    public function getValidationRules() {
        $messages = array(
            'location.required'    => 'Place name field is required.',
        );
        return $messages;
    }
    
}
