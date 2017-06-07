<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Validator;
use App\Manual;
use Illuminate\Support\Facades\File as LaraFile;
use DB;
//use Illuminate\Support\Facades\DB;
use Auth;

use Exception;

#use Intervention\Image\Facades\Image;
#use App\Http\Helper\FileUpload;
use Input;
//use Validator;
use Redirect;
//use Request;file" => "required|mimes:pdf|max:10000"
use Session;

class ManualController extends Controller {

    /**
     * Constant for pdf folder.
     */
    const FILE_SUB_DIR = 'uploads/manuals';

    private $paginatorvalue;

    //
    /** http://tutsnare.com/upload-files-in-laravel/
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function __construct() {
        $this->paginatorvalue = \Config::get('appConstants.paginatorvalue');
        if (Auth::user()->fk_users_role == \Config::get('appConstants.admin_role_id')) {
            $this->currentUserTemplate = 'admin';
        }
    }

    public function index() {
        $params = null;
        if (Input::has('title')){
        
            $params['title'] = Input::get('title');
        
        }
        
        $data =  DB::table('manuals');
        if($params!=null && count($params)>0 && trim($params['title'])!=''){
                        $data->where('title','LIKE',"%" .$params['title'] . "%");
          }
        $blog = $data->paginate($this->paginatorvalue);
        if (Input::has('title')){
             $blog->appends( Input::only('title') ); 
        }
          /*  $blog->appends(array(
    'date-from' => Input::get('date-from'),
    'date-to'   => Input::get('date-to'),

           * 
           * or 
           * $searchResult->appends( Input::only('data-from', 'date-to') ); 
           *            */
        //echo "<pre/>"
        $filepath=self::FILE_SUB_DIR;
        return view('admin.manuals.index', compact('blog','filepath'));
    }

    public function create() {
        return view('admin.manuals.create');
    }

    public function store(Request $request) {
        
        try {
/*
        $file = array('image' => Input::file('image'));
        // getting all of the post data
        $file = array('image' => Input::file('image'));*/
        // setting up rules
        $rules = array('title' => 'required', 'description' => 'required', 'filename' => 'required|mimes:pdf|max:10000'); //mimes:jpeg,bmp,png and for max size max:10000
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()
                            ->back()
                            ->withInput($request->all())
                            ->withErrors($validator);
        } else {
            // die("ooooppppp");
            // checking file is valid.
            if (Input::file('filename')->isValid()) {
                $destinationPath = self::FILE_SUB_DIR;
                $extension = Input::file('filename')->getClientOriginalExtension();
                $fileName = date("YmdHis") . '.' . $extension; // renameing image
                Input::file('filename')->move($destinationPath, $fileName);
                $userData = array();
                $userData['title'] = $request->input('title');
                $userData['description'] = $request->input('description');
                $userData['filename'] = $fileName;

                if ($request->published) {
                    $userData['published'] = 1;
                } else {
                    $userData['published'] = 0;
                }
                DB::table('manuals')->insert($userData);
                $request->session()->flash('message', 'Mnaual added successfully!');
                return redirect()->route('admin.manuals.index');
                //
                //Check file was uploaded :- if (Input::hasFile('image')) { }
                //Getting path of uploaded file :- $path = Input::file('image')->getRealPath();
                //Getting original name of uploaded file :- $name = Input::file('image')->getClientOriginalName();
                // Getting extension Of uploaded file :- $extension = Input::file('image')->getClientOriginalExtension();
                //Getting size of An uploaded file :-  $size = Input::file('image')->getSize();
                //Getting MIME Type of uploaded file :- $mime = Input::file('image')->getMimeType();
            }
        }

        /*
          $product = new Product(array(
          'name' => $request->get('name'),
          'sku'  => $request->get('sku')
          ));

          $product->save();

         */
        } catch (Exception $ex) {
            return redirect()->route($this->currentUserTemplate.'.manuals.index')->withErrors(trans('admin/users.not_exists'));
        }
        
    }

    public function edit($id) {
        $manualData = DB::table('manuals')->where('id',  $id)->get();
        
       // echo "<pre/>";
      // print_r($manualData[0]->id);die;
        return view('admin.manuals.edit', ['manuals'=>$manualData[0],'filepath'=>self::FILE_SUB_DIR]);
        
    }
    public function update($id, Request $request) {
       
        try {
        
        if (Input::hasFile('filename')) { 
            $rules = array('title' => 'required', 'description' => 'required', 'filename' => 'required|mimes:pdf|max:10000');
            
        } else{
            $rules = array('title' => 'required', 'description' => 'required');
            
        }
        
        $validator = Validator::make($request->all(), $rules);
        if ($validator->fails()) {
            return redirect()
                            ->back()
                            ->withInput($request->all())
                            ->withErrors($validator);
        } else {
            $userData = array();
            if (Input::hasFile('filename')) { 
                if (Input::file('filename')->isValid()) {
                    $manualData = DB::table('manuals')->where('id',  $id)->get();
                    $fileToremove = $manualData[0]->filename;
                    LaraFile::delete(self::FILE_SUB_DIR ."/" .$fileToremove);
                    $destinationPath = self::FILE_SUB_DIR;
                    $extension = Input::file('filename')->getClientOriginalExtension();
                    $fileName = date("YmdHis") . '.' . $extension; // renameing image
                    Input::file('filename')->move($destinationPath, $fileName);
                    $userData['filename'] = $fileName;
                    
                    
                }
            }
            
            
                $userData['title'] = $request->input('title');
                $userData['description'] = $request->input('description');
                

                if ($request->published) {
                    $userData['published'] = 1;
                } else {
                    $userData['published'] = 0;
                }
                
                DB::table('manuals')
            ->where('id', $id)
            ->update($userData);
            
            
            $request->session()->flash('message', 'Manual details updated successfully!');
        return redirect()->route('admin.manuals.index');
        }
        
        
        
        } catch (Exception $ex) {
            return redirect()->route($this->currentUserTemplate.'.manuals.index')->withErrors(trans('admin/manual.not_exists'));
        }
        
    }
    public function destroy($id){
        //echo $id;die;
        try {
            
                    $manualData = DB::table('manuals')->where('id',  $id)->get();
                    $fileToremove = $manualData[0]->filename;
                    LaraFile::delete(self::FILE_SUB_DIR ."/" .$fileToremove);
                    DB::table('manuals')->where('id', '=', $id)->delete();
                    echo "1"; exit();
          ///          $request->session()->flash('message', 'Manual deleted successfully!');
           //         return redirect()->route('admin.manuals.index');
        } catch (Exception $ex) {
    //           return redirect()->route($this->currentUserTemplate.'.manuals.index')->withErrors(trans('admin/manual.not_exists')); 
            echo "0";exit();
        }
    }
    
 }
    