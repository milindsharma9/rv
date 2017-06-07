<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Validator;
use App\Brand;
//use App\Keyword;
use DB;
use Auth;
use App\EventLog;
use Exception;
use Intervention\Image\Facades\Image;
use App\Http\Helper\FileUpload;
use Illuminate\Support\Facades\Log;


class BrandController extends Controller
{
    /**
     * Constant for image folder.
     */
    const FILE_SUB_DIR = 'brand';
    
    /**
     *
     * @var type $fileUploader
     */
    private $fileUploader   = null;
    
    /**
     *
     * @var type Brand
     */
    private $_brandModel     = null;
    
    
    /**
     * Contructor. 
     * 
     */
    public function __construct () {
        $this->fileUploader = new FileUpload();
    }
    
    /**
     * Method to instantiate BrandModel.
     * 
     * @return object BrandModel.
     */
    private function getBrandModel() {
        if ($this->_brandModel == null) {
            $this->_brandModel = new Brand();
        }
        return $this->_brandModel;
    } 
    
    
    /**
     *
     * @var array File Validation for different type
     */
    private $aImageValidationRules = array(
        'image_background'  => array(
            'min_width'     => '1200',
            'min_height'    => '768',
        )
    );
    
    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index() {
        $brand = Brand::all();
        return view('admin.brand.index', compact('brand'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create() {
        return view('admin.brand.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request) {
        try {   
            $validateFile = $this->fileUploader->validateFiles($this->aImageValidationRules);
            if ($validateFile['status']) {
                DB::beginTransaction();
                $request = $this->fileUploader->saveFiles($request, self::FILE_SUB_DIR);
                $validator = Validator::make($request->all(), $this->getBrandModel()->validationRules());
                if ($validator->fails()) {
                    return redirect()
                        ->back()
                        ->withInput($request->all())
                        ->withErrors($validator);
                }
                $request->merge(['active' => $request->get('active', '0')]);
                $request->merge(['is_external' => $request->get('is_external', '0')]);
                $aInputRequest  = $request->all();
                $brand = Brand::create($aInputRequest);
                if($brand){
                    $aProducts = $request->get('products');
                    if (empty($aProducts)) {
                        $aProducts = array();
                    }
                    $this->getBrandModel()->saveBrandProductMapping($brand->id, $aProducts);
                    $this->getBrandModel()->saveBrandBundleMapping($brand->id, explode(',', $request->get('bundles')), 1);
                    $this->getBrandModel()->saveBrandBundleMapping($brand->id, explode(',', $request->get('recipies')), 0);
                    DB::commit();
                    return redirect()->route('admin.brand.index');
                }
            } else {
                return redirect()->back()->withInput($request->all())->withErrors($validateFile['error']);
            }
        } catch (Exception $ex) {
            DB::rollBack();
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
        $brand      =  Brand::find($id);
        $products   = $this->getBrandModel()->getProductsForToken($id);
        $fileSubDir = self::FILE_SUB_DIR;
        return view('admin.brand.edit', compact('brand', 'products', 'fileSubDir'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update($id, Request $request) {
        try {
            $userType = $request->get('brand_type');
            $brand = Brand::findOrFail($id);
            $validateFile = $this->fileUploader->validateFiles($this->aImageValidationRules);
            if ($validateFile['status']) {
                DB::beginTransaction();
                $request = $this->fileUploader->saveFiles($request, self::FILE_SUB_DIR);
                $validator = Validator::make($request->all(), $this->getBrandModel()->validationRules($id));
                if ($validator->fails()) {
                    return redirect()
                        ->back()
                        ->withInput($request->all())
                        ->withErrors($validator);
                }
                $request->merge(['active' => $request->get('active', '0')]);
                $request->merge(['is_external' => $request->get('is_external', '0')]);
                $aInputRequest  = $request->all();
                $brand->update($aInputRequest);
                $aProducts = $request->get('products');
                if (empty($aProducts)) {
                    $aProducts = array();
                }
                $this->getBrandModel()->saveBrandProductMapping($brand->id, $aProducts);
                $this->getBrandModel()->saveBrandBundleMapping($brand->id, explode(',', $request->get('bundles')), 1);
                $this->getBrandModel()->saveBrandBundleMapping($brand->id, explode(',', $request->get('recipies')), 0);
                DB::commit();
                return redirect()->route('admin.brand.index');
            } else {
                return redirect()->back()->withInput($request->all())->withErrors($validateFile['error']);
            }
        } catch (Exception $ex) {
            DB::rollBack();
            return redirect()->back()->withInput($request->all())->withErrors($ex->getMessage());
        }
    }

    /**
     * Method to return saved producst for a brand.
     * 
     * @param Request $request
     * @return json
     */
    public function getSavedProducts(Request $request) {
        $response = [];
        try {
            $id = $request->get('id');
            if (isset($id)) {
                $data = $this->getBrandModel()->getProductsForToken($id);
                $response = json_decode(json_encode($data), true);
            } else {
                $response = json_decode(['status' => false, 'message' => 'some error occured']);
            }
        } catch (Exception $ex) {
            Log::error(__METHOD__ . $ex->getMessage());
        }
        return $response;
    }
    /**
     * Method to return saved producst for a brand.
     * 
     * @param Request $request
     * @return json
     */
    public function getSavedBundles(Request $request) {
        $response = [];
        try {
            $id         = $request->get('id');
            $isBundle   = $request->get('bundle', 0);
            if (isset($id)) {
                $data = $this->getBrandModel()->getBundlesForToken($id, $isBundle);
                $response = json_decode(json_encode($data), true);
            } else {
                $response = json_decode(['status' => false, 'message' => 'some error occured']);
            }
        } catch (Exception $ex) {
            Log::error(__METHOD__ . $ex->getMessage());
        }
        return $response;
    }
    
    /**
     * Method to remove images from brand edit page.
     * 
     * @param Request $request
     * @return json
     */
    public function deleteImage(Request $request) {
        $response = [];
        try {
            $imageName  = $request->get('imageName');
            $brandId    = $request->get('brandId');
            $name       = $request->get('name');
            $brand      = Brand::findOrFail($brandId);
            $aInputRequest = [$name => ''];
            if (isset($brandId) && isset($imageName) && isset($name)) {
                $brand->update($aInputRequest);
                $this->fileUploader->deleteFile(self::FILE_SUB_DIR, $imageName);
                $response = json_encode(['status' => TRUE, 'message' => 'You have successfull deleted image']);
            } else {
                $response = json_encode(['status' => false, 'message' => 'some error occured']);
            }
        } catch (Exception $ex) {
            Log::error(__METHOD__ . $ex->getMessage());
        }
        return $response;
    }
    
}
