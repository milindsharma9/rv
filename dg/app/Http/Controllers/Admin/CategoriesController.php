<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Category;
use Validator;
use Illuminate\Support\Facades\Redirect;
use Intervention\Image\Facades\Image;

use App\Http\Controllers\Traits\FileUploadTrait;
use App\Http\Controllers\Controller;

use DB;
use Auth;
use App\EventLog;

class CategoriesController extends Controller {

    const FILE_SUB_DIR = 'categories';

    private $catModel = null;

    private function getCategoryModel() {
        if ($this->catModel == null) {
            $this->catModel = new Category();
        }
        return $this->catModel;
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index() {
        $catModel           = $this->getCategoryModel();
        $primaryCategories  = $catModel->getSubCategories();
        $categories         = $catModel->getCategoryListing();
        $fileSubDir         = self::FILE_SUB_DIR;
        return view('admin.categories.index', compact('categories', 'primaryCategories', 'fileSubDir'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create() {
        $catModel = $this->getCategoryModel();
        $parentId = 0;
        $primaryCategories = $catModel->getSubCategories($parentId);
        $defaultSubCategory = $catModel->getDefaultSubCategory();
        return view('admin.categories.create', compact('primaryCategories', 'defaultSubCategory'));
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request) {
        $validateFile = $this->validateFile($request);
        $parentId = $request->all()['parent_id'];
        if ($validateFile['status']) {
            $request = $this->saveFiles($request, self::FILE_SUB_DIR);
            $rules = array(
                'name' => 'required',
            );
            if ($parentId == 0) {
               $rules['image'] = 'required';
            }
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return Redirect::to('/admin/categories/create')->withInput()->withErrors($validator);
            }
            $this->mapSubcategory($request);
            Category::create($request->all());
            $this->getCategoryModel()->clearCategoryCache();
            return redirect()->route('admin.categories.index');
        } else {
            return Redirect::to('/admin/categories/create')->withInput()->withErrors($validateFile['error']);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id) {
        $categories = Category::find($id);
        return view('admin.categories.index', compact('categories'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id) {
        $categories                = Category::find($id);
        $catModel                  = $this->getCategoryModel();
        $categoryParentDetails     = $catModel->getCategoryParentDetails($id);
        $catParentId = $categoryParentDetails['parentId'];
        $catSuperParentId = $categoryParentDetails['superParentId'];
        $primaryCategories  = $catModel->getSubCategories(0);
        if ($catParentId == 0) {
            $selParentId = $selSubcatId = 0;
            $defaultSubCategory = $catModel->getDefaultSubCategory();
        } else {
            if ($catSuperParentId == 0) {
                $selParentId = $catParentId;
                $selSubcatId = $catSuperParentId;
                $defaultSubCategory = $catModel->getSubCategories($catParentId, $id);
            } else {
                $selParentId = $catSuperParentId;
                $selSubcatId = $catParentId;
                $defaultSubCategory = $catModel->getSubCategories($catSuperParentId, $id);
            }
        }
        return view('admin.categories.edit', compact(
                'categories',
                'primaryCategories',
                'defaultSubCategory',
                'selParentId',
                'selSubcatId'
            )
        );
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update($id, Request $request) {
        DB::enableQueryLog();
        $categories = Category::findOrFail($id);
        $validateFile = $this->validateFile($request);
        if ($validateFile['status']) {
            $request = $this->saveFiles($request, self::FILE_SUB_DIR);
            $rules = array(
                'name' => 'required',
            );
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return redirect()
                    ->back()
                    ->withInput($request->all())
                    ->withErrors($validator);
            }
            $this->mapSubcategory($request);
            $categories->update($request->all());
            $this->getCategoryModel()->clearCategoryCache();
            
            /* Insert data to the log table for Category update */
            $logData = array(
                'users_id'          => Auth::user()->id,
                'operation_type'    => EventLog::EVENT_CATEGORY_UPDATE,
                'al_event'          => serialize(DB::getQueryLog()),
            );
            EventLog::logEvent($logData);
            /* End */
            return redirect()->route('admin.categories.index');
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
        $hasSubCategories = $this->getCategoryModel()->hasSubCategories(array($id));
        if (empty($hasSubCategories)) {
            Category::destroy($id);
            $message = 'You have successfully deleted Category';
        } else {
            $message = "This category has subcategory associated with it. Please delete them first.";
        }
        $this->getCategoryModel()->clearCategoryCache();
        
        /* Insert data to the log table for Category update */
            $logData = array(
                'users_id'          => Auth::user()->id,
                'operation_type'    => EventLog::EVENT_CATEGORY_DELETE,
                'al_event'          => serialize(DB::getQueryLog()),
            );
            EventLog::logEvent($logData);
            /* End */
        
        \Session::flash('message', $message);
        return Redirect::route('admin.categories.index');
    }
    
    /**
     * Mass delete function from index page
     * @param Request $request
     *
     * @return mixed
     */
    public function massDelete(Request $request) {
        $message = "You have successfully deleted Categories.";
        if ($request->get('toDelete') != 'mass') {
            $toDelete = json_decode($request->get('toDelete'));
            $hasSubCategories = $this->getCategoryModel()->hasSubCategories($toDelete);
            if (empty($hasSubCategories)) {
                Category::destroy($toDelete);
            } else {
                $message = "Some category has subcategory associated with them. Please delete them first.";
            }
        } else {
            Category::whereNotNull('id')->delete();
        }
        $this->getCategoryModel()->clearCategoryCache();
        \Session::flash('message', $message);
        return redirect()->route('admin.categories.index');
    }

    /*
     * Get Subcategory of parent Id. Use to populate select box in ajax request
     */
    public function getSubcategory($parentId, $currentCatId) {
        $catModel = $this->getCategoryModel();
        if ($parentId == 0) {
            $subCategories = $catModel->getDefaultSubCategory();
        } else {
           $subCategories = $catModel->getSubCategories($parentId, $currentCatId);
        }
        return $subCategories;
    }

    /*
     * Map Selected Subcategory to DB value.
     */
    public function mapSubcategory(Request $request) {
        $subCatId = $request->sub_category_id;
        if ($subCatId != "0") {
            $request->parent_id = $subCatId;
            $request->merge(array('parent_id' => $subCatId));
        }
    }

}
