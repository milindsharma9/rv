<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Bundle;
use Validator;
use Illuminate\Support\Facades\Redirect;
use Intervention\Image\Facades\Image;
use Illuminate\Support\Facades\DB;

use App\Http\Helper\FileUpload;
use App\Http\Controllers\Controller;

use App\Event;
use App\Occasion;

class BundlesController extends Controller {
    const FILE_SUB_DIR = 'bundles';
    
    /**
     *
     * @var array File Validation for different type
     */
    private $aImageValidationRules = array(
        'image_thumb'         => array(
            'min_height'    => '75',
            'min_width'     => '75',
        ),
        'image'  => array(
            'min_height'    => '100',
            'min_width'     => '100',
        ),
    );


    /**
     * Display a listing of the resource.
     *
     * @return Response
     */
    public function index() {
        $bundles = Bundle::orderBy('name', 'ASC')->get();
        $fileSubDir = self::FILE_SUB_DIR;
        return view('admin.bundles.index', compact('bundles', 'fileSubDir'));
    }

    private $fileUploader       = null;

    public function __construct () {
        $this->fileUploader = new FileUpload();
    }
    
    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create() {
        return view('admin.bundles.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request) {
        $finalMap = [];
        $validateFile = $this->fileUploader->validateFiles($this->aImageValidationRules);
        if ($validateFile['status']) {
            $request = $this->saveFiles($request, self::FILE_SUB_DIR);
            $rules = array(
                'name'          => 'required',
                'description'   => 'required',
            );
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                return Redirect::to('/admin/bundles/create')->withInput()->withErrors($validator);
            }
            $request->merge(['is_recipe' => $request->get('is_recipe', '0')]);
            $request->merge(['is_popular' => $request->get('is_popular', '0')]);
            $request->merge(['is_gift' => $request->get('is_gift', '0')]);
            $bundleObj = Bundle::create($request->all());
            if($bundleObj){
                if(isset($request['productSelect']) && isset($request['quantity'])){
                foreach ($request['productSelect'] as $key => $value) {
                     $finalMap[$value] = $request['quantity'][$key];
                }
                $bundle = new Bundle();
                    $aMap = array(
                        'id' => $bundleObj->id,
                        'data' => isset($finalMap) ? $finalMap : array(),
                    );
                    $bundle->saveBundleMapping($aMap);
                    \Session::flash('message', 'Bundle mapping update successfully');
                }
            }
            return redirect()->route('admin.bundles.index');
        } else {
            return Redirect::to('/admin/bundles/create')->withInput()->withErrors($validateFile['error']);
        }
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id) {
        $bundles = Bundle::find($id);
        return view('admin.bundles.index', compact('bundles'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id) {
        $bundles = Bundle::find($id);
        $bundle = new Bundle();
        $prodMapping    = $bundle->getBundleMapping($id);
        return view('admin.bundles.edit', compact('bundles', 'prodMapping'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update($id, Request $request) {
        $events = Bundle::findOrFail($id);
        $validateFile = $this->fileUploader->validateFiles($this->aImageValidationRules);
        if ($validateFile['status']) {
            $request = $this->fileUploader->saveFiles($request, self::FILE_SUB_DIR);
            $rules = array(
                'name'          => 'required',
                'description'   => 'required',
            );
            $validator = Validator::make($request->all(), $rules);
            if ($validator->fails()) {
                //return Redirect::to('/events/create')->withInput()->withErrors($validator);
                return redirect()
                    ->back()
                    ->withInput($request->all())
                    ->withErrors($validator);
            }
            $request->merge(['is_recipe' => $request->get('is_recipe', '0')]);
            $request->merge(['is_popular' => $request->get('is_popular', '0')]);
            $request->merge(['is_gift' => $request->get('is_gift', '0')]);
            $events->update($request->all());
            if(isset($request['productSelect']) && isset($request['quantity'])){
                foreach ($request['productSelect'] as $key => $value) {
                     $finalMap[$value] = $request['quantity'][$key];
                }
                $bundle = new Bundle();
                    $aMap = array(
                        'id' => $id,
                        'data' => isset($finalMap) ? $finalMap : array(),
                    );
                    $bundle->saveBundleMapping($aMap);
                    \Session::flash('message', 'Bundle mapping update successfully');
                }
            return redirect()->route('admin.bundles.index');
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
        Bundle::destroy($id);
        DB::table('xref_bundle_products')->where('fk_bundle_id', '=', $id)->delete();
        \Session::flash('message', 'You have successfull deleted Bundle');
        return Redirect::route('admin.bundles.index');
    }
    
    /**
     * Mass delete function from index page
     * @param Request $request
     *
     * @return mixed
     */
    public function massDelete(Request $request) {
        if ($request->get('toDelete') != 'mass') {
            $toDelete = json_decode($request->get('toDelete'));
            Bundle::destroy($toDelete);
        } else {
            Bundle::whereNotNull('id')->delete();
        }
        return redirect()->route('admin.bundles.index');
    }
    
    /**
     * Removing mapping from bundle.
     * @param Request $request
     * @return string
     */
    public function removeMapping(Request $request) {
        if (isset($request['bundleId']) && isset($request['productId'])) {
            DB::table('xref_bundle_products')
                    ->where('fk_bundle_id', '=', $request['bundleId'])
                    ->where('fk_product_id', '=', $request['productId'])
                    ->delete();
            return 'success';
        }
    }

    public function mapBundle($id) {
        $eventModel     = new Event();
        $eventGroup     = $eventModel->getEventsByParentId();
        $bundleModel    = new Bundle();
        $bundleMapping  = $bundleModel->getBundleEventOccasionMapping($id);
        $bundle         = Bundle::find($id);
        $occasionModel  = new Occasion();
        $occasionGroup  = $occasionModel->getOccasionsByParentId();
        return view('admin.bundles.map', compact('bundle',
                'bundleMapping',
                'eventGroup',
                'occasionGroup'));
    }

    /**
     * Update bundle mapping with events & Occasions
     *
     * @param  int  $id
     * @return Response
     */
    public function mapBundleStore($id, Request $request) {
        $bundleModel = new Bundle();
        $aMap = array(
            'id'        => $id,
            'event'     => isset($request['event']) ? $request['event'] : array(),
            'occasion'  => isset($request['occasion']) ? $request['occasion'] : array(),
        );
        $bundleModel->saveBundleEventOccasionMapping($aMap);
        \Session::flash('message', 'Bundle mapping update successfully');
        return redirect()->back();
    }

    /**
     * return all the matching bundles.
     * 
     * @return json
     */
    public function getMatchingBundles(Request $request) {
        $prodModel  = new Bundle();
        $searchTerm = $request->get('q', '');
        $isRecipe   = $request->get('recipe', '0');
        $products   = $prodModel->getMatchingBundles($searchTerm, $isRecipe);
        return $products;
    }

}
