<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Product;
use Validator;
use Illuminate\Support\Facades\Redirect;

use App\Http\Controllers\Controller;
use DB;

use App\Event;
use App\Occasion;
use App\Category;
use App\Http\Helper\CommonHelper;
use App\ProductImage;
use App\Http\Helper\FileUpload;


class ProductsController extends Controller {

    const FILE_SUB_DIR = 'product_images';

    private $fileUploader       = null;

    public function __construct () {
        $this->fileUploader = new FileUpload();
    }

    /**
     * Display a listing of the resource.
     *
     * @return Response
     */    
    public function index() {
        $prodModel  = new Product();
        $products   = $prodModel->getProductsAdmin();
        return view('admin.products.index', compact('products'));
    }

    /**
     * Show the form for creating a new resource.
     *
     * @return Response
     */
    public function create() {
        return view('admin.products.create');
    }

    /**
     * Store a newly created resource in storage.
     *
     * @return Response
     */
    public function store(Request $request) {
        $aMessage = $this->getValidationMessages();
        $rules = array(
            'name'          => 'required',
            'barcode'       => 'required|unique:products',
            'description'   => 'required',
            'store_price'   => 'required|numeric',
            'psc'           => 'required|numeric',
            'vpc'           => 'required|numeric',
        );
        $validator = Validator::make($request->all(), $rules, $aMessage);
        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withInput($request->all())
                ->withErrors($validator);
        }
        DB::beginTransaction();
        $aInputRequest  = $request->all();
        if (isset($aInputRequest['lower_age_limit_new'])) {
            $aInputRequest['lower_age_limit_new'] = 1;
        } else {
            $aInputRequest['lower_age_limit_new'] = 0;
        }
        if (isset($aInputRequest['in_data_feed'])) {
            $aInputRequest['in_data_feed'] = 1;
        } else {
            $aInputRequest['in_data_feed'] = 0;
        }
        if (isset($aInputRequest['is_popular'])) {
            $aInputRequest['is_popular'] = 1;
        } else {
            $aInputRequest['is_popular'] = 0;
        }
        if (isset($aInputRequest['is_gifts'])) {
            $aInputRequest['is_gifts'] = 1;
        } else {
            $aInputRequest['is_gifts'] = 0;
        }
        unset($aInputRequest['_method'], $aInputRequest['_token']);
        $aAttributeProduct      = array_only($aInputRequest, ['name', 'description', 'price', 'store_price', 'barcode']);
        $aAttributeProductMeta  = array_diff_key($aInputRequest, $aAttributeProduct);
        $product                = Product::create($aAttributeProduct);
        $lastInsertedProductId  = $product->id;
        $aAttributeProductMeta['fk_product_id'] = $lastInsertedProductId;
        $aAttributeProductMeta['created_at'] = new \DateTime;
        $aAttributeProductMeta['updated_at'] = new \DateTime;
        DB::table('products_meta')->insert($aAttributeProductMeta);
        DB::commit();
        return redirect()->route('admin.products.index');
    }

    /**
     * Display the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function show($id) {
        $products = Product::find($id);
        return view('admin.products.index', compact('products'));
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function edit($id) {
        $prodModel  = new Product();
        $products   = $prodModel->getProductDetailAdmin($id);
        return view('admin.products.edit', compact('products'));
    }

    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function update($id, Request $request) {
        $aMessage = $this->getValidationMessages();
        $rules = array(
            'name'          => 'required',
            'description'   => 'required',
            'store_price'   => 'required|numeric',
            'psc'           => 'required|numeric',
            'vpc'           => 'required|numeric',
        );
        $validator = Validator::make($request->all(), $rules, $aMessage);
        if ($validator->fails()) {
            return redirect()
                ->back()
                ->withInput($request->all())
                ->withErrors($validator);
        }
        $prodModel      = new Product();
        $aInputRequest  = $request->all();
        if (isset($aInputRequest['lower_age_limit_new'])) {
            $aInputRequest['lower_age_limit_new'] = 1;
        } else {
            $aInputRequest['lower_age_limit_new'] = 0;
        }
        if (isset($aInputRequest['in_data_feed'])) {
            $aInputRequest['in_data_feed'] = 1;
        } else {
            $aInputRequest['in_data_feed'] = 0;
        }
        if (isset($aInputRequest['is_popular'])) {
            $aInputRequest['is_popular'] = 1;
        } else {
            $aInputRequest['is_popular'] = 0;
        }
        if (isset($aInputRequest['is_gifts'])) {
            $aInputRequest['is_gifts'] = 1;
        } else {
            $aInputRequest['is_gifts'] = 0;
        }
        unset($aInputRequest['_method'], $aInputRequest['_token']);
        $updateResponse = $prodModel->updateProductDetailsAdmin($id, $aInputRequest);
        if (!$updateResponse['status']) {
            return redirect()
                ->back()
                ->withInput($request->all())
                ->withErrors($updateResponse['message']);
        }
        return redirect()->route('admin.products.index');
    }

    /**
     * Remove the specified resource from storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function destroy($id) {
        //return redirect()->back(); // Not allowed as of now
        Product::destroy($id);
        \Session::flash('message', 'You have successfull deleted Product');
        return Redirect::route('admin.products.index');
    }
         
    /**
     * Mass delete function from index page
     * @param Request $request
     *
     * @return mixed
     */
    public function massDelete(Request $request) {
        $toDelete = json_decode($request->get('toDelete'));
        if ($request->get('type') == 'activate') {
            Product::withTrashed()->whereIn('id', $toDelete)->restore();
        } else {
            Product::destroy($toDelete);
        }
        return redirect()->route('admin.products.index');
    }
    
    /**
     * to show predefined product mapping.
     * 
     * @param Int $id
     * @return view
     */
    public function mapProduct($id) {
        $eventModel     = new Event();
        $eventGroup     = $eventModel->getEventsByParentId();
        $catModel       = new Category();
        $catGroup       = $catModel->getCategoriesByParentId();
        $prodModel      = new Product();
        $prodMapping    = $prodModel->getProductMapping($id);
        $occasionModel  = new Occasion();
        $occasionGroup  = $occasionModel->getOccasionsByParentId();
        $products   = $prodModel->getProductDetailAdmin($id);
        return view('admin.products.map', compact('products',
                'prodMapping',
                'catGroup',
                'eventGroup',
                'occasionGroup'));
    }
    
    /**
     * Update the specified resource in storage.
     *
     * @param  int  $id
     * @return Response
     */
    public function mapProductStore($id, Request $request) {
        $product = new Product();
        $aMap = array(
            'id' => $id,
            'category' => isset($request['category']) ? $request['category'] : array(),
            'event' => isset($request['event']) ? $request['event'] : array(),
            'occasion' => isset($request['occasion']) ? $request['occasion'] : array(),
        );
        $product->saveProductMapping($aMap);
        \Session::flash('message', 'Products mapping update successfully');
        return redirect()->back();
    }
    
    /**
     * return all the products available.
     * 
     * @return json
     */
    public function getProducts() {
        $products = Product::orderBy('name', 'asc')->get();
        foreach ($products as $key => $item) {
            $item->name = $item->name .'--'.CommonHelper::formatProductDescription($item->description);
        }
        return response()->json(['products' => $products]);
    }
    
    /**
     * return all the products available.
     * 
     * @return view
     */
    public function getallProductsUrls() {
        try {     
            $catModel       = new Category();
            $catTree       = $catModel->getCategoryTree();
            return view('admin.products.productlist', compact('catTree'));
        } catch (Exception $ex) {
            Log::error('error in ProductsController/index' . $ex->getMessage());
            return view('errors.500');
        }
    }

    /**
     * Display a listing of Product Image.
     *
     * @return Response
     */
    public function getProductImages($productId) {
        $prodImageModel  = new ProductImage();
        $productImages   = $prodImageModel->getProductImages($productId);
        $thumb           = $prodImageModel->getProductThumbImage($productId);
        $fileSubDir      = self::FILE_SUB_DIR;
        return view('admin.products.image', compact('productImages', 'productId', 'fileSubDir', 'thumb'));
    }

    /**
     * Method to upload Product Image
     *
     * @param Request $request
     * @return type
     */
    public function uploadProductImage(Request $request) {
        $validateFile = $this->fileUploader->validateFiles();
        if ($validateFile['status']) {
            $prodImageModel  = new ProductImage();
            $isUpdate   = $request->get('is_update');
            $isThumb   = $request->get('is_thumb');
            $productId  = $request->get('product_id');
            $oldImageName   = $request->get('image_name', '');
            if (empty($oldImageName)) {
                $productImages = $prodImageModel->getProductImages($productId);
                $maxUploadCount = ProductImage::MAX_UPLOAD_LIMIT;
                if (count($productImages) >= $maxUploadCount && ($isThumb) != 1) {
                    return redirect()
                        ->back()
                        ->withInput($request->all())
                        ->withErrors('Max '. $maxUploadCount .' images are allowed.');
                }
            }
            $request    = $this->fileUploader->saveFiles($request, self::FILE_SUB_DIR);
            $imageName  = $request->get('image');
            if (!empty($oldImageName)) {
                //$oldImageName   = $request->get('image_name');
                $imageId        = $request->get('image_id');
                $prodImageModel->updateProductImage($imageId, $imageName);
                $this->fileUploader->deleteFile(self::FILE_SUB_DIR, $oldImageName);
            } else {
                $prodImageModel->insertProductImage($productId, $imageName, 0, $isThumb);
            }
            return redirect()->back();
        } else {
            return redirect()
                    ->back()
                    ->withInput($request->all())
                    ->withErrors($validateFile['error']);
        }
    }

    /**
     * Method to delete Product Image
     * 
     * @param Request $request
     * @return type
     */
    public function deleteProductImage(Request $request) {
        $prodImageModel     = new ProductImage();
        $productId          = $request->get('product_id');
        $imageId            = $request->get('image_id');
        $oldImageName       = $request->get('image_name');
        $prodImageModel->deleteProductImage($imageId);
        $this->fileUploader->deleteFile(self::FILE_SUB_DIR, $oldImageName);
        return redirect()->back();
    }

    /**
     * Method to set Product Main Image
     * 
     * @param Request $request
     * @return type
     */
    public function setProductPrimaryImage(Request $request) {
        $prodImageModel     = new ProductImage();
        $productId          = $request->get('product_id');
        $imageId            = $request->get('image_id');
        $prodImageModel->setProductPrimaryImage($productId, $imageId);
        return redirect()->back();
    }
    
    /**
     * return all the matching products.
     * 
     * @return json
     */
    public function getMatchingProducts(Request $request) {
        $prodModel  = new Product();
        $searchTerm = $request->get('q', '');
        $products   = $prodModel->getMatchingProducts($searchTerm);
        return $products;
    }

    /**
     * Method to return validation messages to be shown.
     *
     * @return array $aMessage
     */
    public function getValidationMessages() {
        $aMessage = array(
            'name.required'         => 'The brand field is required.',
            'store_price.required'  => 'The vendor price field is required.',
            'store_price.numeric'   => 'The vendor price must be a number.',
        );
        return $aMessage;
    }

}
