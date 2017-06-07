<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use Validator;
use App\Locale;
use App\Keyword;
use DB;
use Auth;
use App\EventLog;
use Exception;
use Intervention\Image\Facades\Image;
use App\Http\Helper\FileUpload;
use Illuminate\Support\Facades\Log;


class LocaleController extends Controller
{
    /**
     * Constant for image folder.
     */
    const FILE_SUB_DIR = 'locale';
    
    /**
     *
     * @var type $fileUploader
     */
    private $fileUploader   = null;
    
    /**
     *
     * @var type Locale
     */
    private $_localeModel     = null;
    
    
    /**
     * Contructor. 
     * 
     */
    public function __construct () {
        $this->fileUploader = new FileUpload();
    }
    
    /**
     * Method to instantiate LocaleModel.
     * 
     * @return object LocaleModel.
     */
    private function getLocaleModel() {
        if ($this->_localeModel == null) {
            $this->_localeModel = new Locale();
        }
        return $this->_localeModel;
    } 
    
    
    /**
     *
     * @var array File Validation for different type
     */
    private $aImageValidationRules = array(
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
        $locale = Locale::all();
        return view('admin.locale.index', compact('locale'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create() {
        return view('admin.locale.create');
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
                $validator = Validator::make($request->all(), $this->getLocaleModel()->validationRules());
                if ($validator->fails()) {
                    return redirect()
                        ->back()
                        ->withInput($request->all())
                        ->withErrors($validator);
                }
                $request->merge(['active' => $request->get('active', '0')]);
                $aInputRequest  = $request->all();
                $locale = Locale::create($aInputRequest);
                if($locale){
                    $this->getLocaleModel()->saveLocaleKeywordMapping($locale->id, explode(',', $request->get('keywords')));
                    $aProducts = $request->get('products');
                    if (empty($aProducts)) {
                        $aProducts = array();
                    }
                    $this->getLocaleModel()->saveLocaleProductMapping($locale->id, $aProducts);
                    $this->getLocaleModel()->saveLocaleBundleMapping($locale->id, explode(',', $request->get('bundles')), 1);
                    $this->getLocaleModel()->saveLocaleBundleMapping($locale->id, explode(',', $request->get('recipies')), 0);
                    DB::commit();
                    return redirect()->route('admin.locale.index');
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
        $locale      =  Locale::find($id);
        $products    = $this->getLocaleModel()->getProductsForToken($id);
        return view('admin.locale.edit', compact('locale', 'products'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update($id, Request $request) {
        try {
            $userType = $request->get('locale_type');
            $locale = Locale::findOrFail($id);
            $validateFile = $this->fileUploader->validateFiles($this->aImageValidationRules);
            if ($validateFile['status']) {
                DB::beginTransaction();
                $request = $this->fileUploader->saveFiles($request, self::FILE_SUB_DIR);
                $validator = Validator::make($request->all(), $this->getLocaleModel()->validationRules($id));
                if ($validator->fails()) {
                    return redirect()
                        ->back()
                        ->withInput($request->all())
                        ->withErrors($validator);
                }
                $request->merge(['active' => $request->get('active', '0')]);
                $aInputRequest  = $request->all();
                $locale->update($aInputRequest);
                $this->getLocaleModel()->saveLocaleKeywordMapping($locale->id, explode(',', $request->get('keywords')));
                $aProducts = $request->get('products');
                if (empty($aProducts)) {
                    $aProducts = array();
                }
                $this->getLocaleModel()->saveLocaleProductMapping($locale->id, $aProducts);
                $this->getLocaleModel()->saveLocaleBundleMapping($locale->id, explode(',', $request->get('bundles')), 1);
                $this->getLocaleModel()->saveLocaleBundleMapping($locale->id, explode(',', $request->get('recipies')), 0);
                DB::commit();
                return redirect()->route('admin.locale.index');
            } else {
                return redirect()->back()->withInput($request->all())->withErrors($validateFile['error']);
            }
        } catch (Exception $ex) {
            DB::rollBack();
            return redirect()->back()->withInput($request->all())->withErrors($ex->getMessage());
        }
    }

    /**
     * Method to return saved producst for a locale.
     * 
     * @param Request $request
     * @return json
     */
    public function getSavedProducts(Request $request) {
        $response = [];
        try {
            $id = $request->get('id');
            if (isset($id)) {
                $data = $this->getLocaleModel()->getProductsForToken($id);
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
     * Method to return saved producst for a locale.
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
                $data = $this->getLocaleModel()->getBundlesForToken($id, $isBundle);
                $response = json_decode(json_encode($data), true);
            } else {
                $response = json_decode(['status' => false, 'message' => 'some error occured']);
            }
        } catch (Exception $ex) {
            Log::error(__METHOD__ . $ex->getMessage());
        }
        return $response;
    }
    
}
