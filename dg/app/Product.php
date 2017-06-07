<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use DB;
use Log;
use App\Event;
use App\Occasion;
use Illuminate\Support\Facades\Config;
use App\Http\Helper\CommonHelper;
use Exception;
use App\Category;
use App\Configurations;

/**
 * Product Model
 */
class Product extends Model {

    /**
     * Soft delete trait included to ensure soft delete is unable
     */
    use SoftDeletes;

    /**
     *
     * @var table name 
     */
    protected $table = "products";
    
    //only allow the following items to be mass-assigned to our model
    /**
     *
     * @var Allow write on db table columns 
     */
    protected $fillable = ['name', 'description', 'price', 'store_price', 'barcode', 'created_at', 'updated_at'];
    protected $dates = ['deleted_at'];

    /**
     * Save product mapping in db
     * 
     * @package Product Model
     * @param mixed $aMap
     */
    public function saveProductMapping($aMap) {
        $productId = $aMap['id'];
        try {
            DB::beginTransaction();
            $this->updateProductCategoryMapping($productId, $aMap['category']);
            $this->updateProductEventMapping($productId, $aMap['event']);
            $this->updateProductOccasionMapping($productId, $aMap['occasion']);
            DB::commit();
            Log::info('Product mapping Updated');
        } catch (Exception $ex) {
            Log::error('Error');
            DB::rollBack();
        }
    }

    /**
     * Method to update ProductCategoryMapping
     * 
     * @package Product Model
     * @param type $productId
     * @param type $aCat
     * @return void
     */
    public function updateProductCategoryMapping($productId, $aCat) {
        try {
            DB::table('xref_product_categories')->where('fk_product_id', '=', $productId)->delete();
            if (!empty($aCat)) {
                $insertData = array();
                foreach ($aCat as $index => $catId) {
                    $aData = array(
                        'fk_product_id' => $productId,
                        'fk_category_id' => $catId,
                    );
                    array_push($insertData, $aData);
                }
                DB::table('xref_product_categories')->insert($insertData); // Query Builder
            }
        } catch (Exception $ex) {
            Log::error(__METHOD__ . $ex->getMessage());
        }
    }

    /**
     * Method to update ProductEventMapping
     * 
     * @package Product Model
     * @param type $productId
     * @param type $aEvent
     * @return void
     */
    public function updateProductEventMapping($productId, $aEvent) {
        try {
            DB::table('xref_product_events')->where('fk_product_id', '=', $productId)->delete();
            if (!empty($aEvent)) {
                $insertData = array();
                foreach ($aEvent as $index => $eventId) {
                    $aData = array(
                        'fk_product_id' => $productId,
                        'fk_event_id' => $eventId,
                    );
                    array_push($insertData, $aData);
                }
                DB::table('xref_product_events')->insert($insertData);
            }
        } catch (Exception $ex) {
            Log::error(__METHOD__ . $ex->getMessage());
        }
    }

    /**
     * Method to update ProductOccasionMapping
     * 
     * @package Product Model
     * @param Int $productId
     * @param mixed $aOccasion
     * @return void
     */
    public function updateProductOccasionMapping($productId, $aOccasion) {
        try {
            DB::table('xref_product_occasions')->where('fk_product_id', '=', $productId)->delete();
            if (!empty($aOccasion)) {
                $insertData = array();
                foreach ($aOccasion as $index => $occasionId) {
                    $aData = array(
                        'fk_product_id' => $productId,
                        'fk_occasion_id' => $occasionId,
                    );
                    array_push($insertData, $aData);
                }
                DB::table('xref_product_occasions')->insert($insertData);
            }
        } catch (Exception $ex) {
            Log::error(__METHOD__ . $ex->getMessage());
        }
    }

    /**
     * Get Product Mapping
     * 
     * @package Product Model
     * @param Int $productId
     * @return array
     */
    public function getProductMapping($productId) {
        try {
            $prodOccasions = DB::table('xref_product_occasions')
                    ->select('fk_occasion_id as occasion_id')
                    ->where('fk_product_id', '=', $productId)
                    ->get();

            $prodEvents = DB::table('xref_product_events')
                    ->select('fk_event_id as event_id')
                    ->where('fk_product_id', '=', $productId)
                    ->get();

            $prodCategories = DB::table('xref_product_categories')
                    ->select('fk_category_id as category_id')
                    ->where('fk_product_id', '=', $productId)
                    ->get();

            $aCategory = $aOccasion = $aEvent = array();
            foreach ($prodOccasions as $occasion) {
                array_push($aOccasion, $occasion->occasion_id);
            }
            foreach ($prodEvents as $event) {
                array_push($aEvent, $event->event_id);
            }
            foreach ($prodCategories as $category) {
                array_push($aCategory, $category->category_id);
            }
            $aProdMapping = array(
                'category' => $aCategory,
                'event' => $aEvent,
                'occasion' => $aOccasion
            );
            return $aProdMapping;
        } catch (Exception $ex) {
            Log::error(__METHOD__ . $ex->getMessage());
        }
    }

    /**
     * Method to get products associated with categories.
     * 
     * @package Product Model
     * @param array $aCatId
     * @param boolean $hasSubCat
     * @param boolean $nearCheck Tells whether radius check has to be applied on products or not
     * @return mixed
     */
    public function getAssociatedProducts(array $aCatId, $hasSubCat, $nearCheck = FALSE) {
        $prod = array();
        try {
            if ($nearCheck) {
                $aNearByStores = CommonHelper::getNearByStores();
                if (empty($aNearByStores)) {
                    return $prod;
                }
            }
            $prod = DB::table('xref_product_categories')
                    ->select('products.*')
                    // For Showing RRSP when no postcode is selected
                    ->addSelect(DB::raw('ROUND((products.store_price +'
                                        . 'IF(PM.psc = 0.00,( products.store_price * ('
                                        . CommonHelper::getGlobalGPSC() . '/100)) ,( products.store_price * (PM.psc/100)))), 2) as price'))
                    ->join('products', 'products.id', '=', 'xref_product_categories.fk_product_id')
                    ->join('products_meta AS PM', 'PM.fk_product_id', '=', 'products.id');
            $prod = $prod->where('products.deleted_at', '=', NULL);
            if ($nearCheck) {
                $prod = $prod->join('products_store', 'products_store.fk_product_id', '=', 'products.id')
                        //Min Price Change
                        ->addSelect(DB::raw('ROUND((min(products_store.vendor_price) +'
                                        . 'IF(PM.psc = 0.00,( min(products_store.vendor_price) * ('
                                        . CommonHelper::getGlobalGPSC() . '/100)) ,( min(products_store.vendor_price) * (PM.psc/100)))), 2) as price'))
                        ->addSelect('products.id', 'products.name', 'products.description', 'products.barcode')
                        //Min Price Change
                        ->whereIN('products_store.fk_user_id', $aNearByStores);
            }
            $prod = $prod->whereIn('fk_category_id', $aCatId)
                    ->orderBy('products.description')
                    ->groupBy('products.id');
            if ($hasSubCat) {
                $prod = $prod->addSelect('categories.name as catName')
                        ->join('categories', 'categories.id', '=', 'xref_product_categories.fk_category_id')
                        ->orderBy('catName')
                        ->get();
                $returnResponse = array();
                foreach ($prod as $prodSingle) {
                    $catName = $prodSingle->catName;
                    if (!isset($returnResponse[$catName])) {
                        $returnResponse[$catName] = array();
                    }
                    $prodSingle->imagePath = CommonHelper::getProductImage($prodSingle->id, $isThumb = true);
                    array_push($returnResponse[$catName], $prodSingle);
                }
                return array($returnResponse);
            } else {
                $prod = $prod->get();
                foreach ($prod as $prodSingle) {
                    $prodSingle->imagePath = CommonHelper::getProductImage($prodSingle->id, $isThumb = true);
                }
                return $prod;
            }
        } catch (Exception $ex) {
            Log::error(__METHOD__ . $ex->getMessage());
        }
    }

    /**
     * Method to get categories.
     * 
     * @package Product Model
     * @param array $aSubCatTree
     * @param int $pCatId
     * @param boolean $nearCheck Tells whether radius check has to be applied on products or not
     * @return array
     */
    public function getCatProducts($aSubCatTree, $pCatId, $nearCheck = FALSE) {
        try {
            $aResponse = array(
                'haSubCat' => false,
                'products' => array(),
            );
            if (!isset($aSubCatTree['sub_categories'][0])) {
                $aCat = array($pCatId);
                $aResponse['products'] = $this->getAssociatedProducts($aCat, $aResponse['haSubCat'], $nearCheck);
                return $aResponse;
            }
            $aCat = array();
            $subSubCats = $aSubCatTree['sub_categories'][0]['subSubCat'];
            if (empty($subSubCats)) {
                $subCatId = $aSubCatTree['sub_categories'][0]['id'];
                $aCat = array($subCatId);
            } else {
                $aResponse['haSubCat'] = true;
                $aSubSubCatId = array();
                foreach ($subSubCats as $subSubCat) {
                    array_push($aSubSubCatId, $subSubCat['id']);
                }
                $aCat = $aSubSubCatId;
            }
            $aResponse['products'] = $this->getAssociatedProducts($aCat, $aResponse['haSubCat'], $nearCheck);
            return $aResponse;
        } catch (Exception $ex) {
            Log::error(__METHOD__ . $ex->getMessage());
        }
    }

    /**
     * Get sub category products 
     * 
     * @package Product Model
     * @param array $aSubCatTree
     * @param int $subCatId
     * @param boolean $nearCheck Tells whether radius check has to be applied on products or not
     * @return array
     */
    public function getSubCatProducts($aSubCatTree, $subCatId, $nearCheck = FALSE) {
        try {
            $aResponse = array(
                'haSubCat' => false,
                'products' => array(),
            );
            if (empty($aSubCatTree)) {
                $aCat = array($subCatId);
                $aResponse['products'] = $this->getAssociatedProducts($aCat, $aResponse['haSubCat'], $nearCheck);
                return $aResponse;
            } else {
                $aResponse['haSubCat'] = true;
                $aCat                  = array_keys($aSubCatTree);
            }
            $aResponse['products'] = $this->getAssociatedProducts($aCat, $aResponse['haSubCat'], $nearCheck);
        } catch (Exception $ex) {
            Log::error(__METHOD__ . $ex->getMessage());
        }
        return $aResponse;
    }

    /**
     * Get Product events mapping data.
     * 
     * @package Product Model
     * @param type $eventId
     * @param boolean $fetchParentDetail
     *
     * @return array $response
     */
    public function getProductEvents($eventId, $fetchParentDetail = FALSE, $nearCheck = FALSE) {
        $aNearByStores = array();
        if ($nearCheck) {
            $aNearByStores = CommonHelper::getNearByStores();
        }
        $response = array(
            'products'      => array(),
            'parentDetails' => array(),
        );
        try {
            $prodEvents = DB::table('xref_product_events')
                ->select('products.id', 'products.name', 'products.barcode', 'products.description',
                         'events.name AS eventName', 'events.image AS eventImage', 'events.parent_id'
                        , 'sub_text', 'floating_text')
                ->addSelect(DB::raw('ROUND((products.store_price +'
                                        . 'IF(PM.psc = 0.00,( products.store_price * ('
                                        . CommonHelper::getGlobalGPSC() . '/100)) ,( products.store_price * (PM.psc/100)))), 2) as price'))
                 //->select('products.*', 'events.name AS eventName', 'events.image AS eventImage', 'events.parent_id')
                ->addSelect(DB::raw('0 AS is_bundle'))
                ->addSelect('categories.name as catName', 'categories.id as catId')
                ->join('products', 'products.id', '=', 'xref_product_events.fk_product_id')
                ->join('events', 'events.id', '=', 'xref_product_events.fk_event_id')
                ->join('xref_product_categories', 'xref_product_categories.fk_product_id', '=', 'xref_product_events.fk_product_id')
                ->join('categories', 'categories.id', '=', 'xref_product_categories.fk_category_id')
                ->join('products_meta AS PM', 'PM.fk_product_id', '=', 'products.id');
            $prodEvents = $prodEvents->where('products.deleted_at', '=', NULL);
                // For Near Stores
                $aCatNotAllowed = config('appConstants.cat_not_allowed');
                if ($nearCheck) {
                    if (!empty($aNearByStores)) {
                        $prodEvents =   $prodEvents->join('products_store', 'products_store.fk_product_id', '=', 'products.id')
                        ->whereIN('products_store.fk_user_id', $aNearByStores)
                        ->where('fk_event_id', '=', $eventId)
                        //Min Price Change
                        ->addSelect(DB::raw('ROUND((min(products_store.vendor_price) +'
                            . 'IF(PM.psc = 0.00,( min(products_store.vendor_price) * ('
                            . CommonHelper::getGlobalGPSC() . '/100)) ,( min(products_store.vendor_price) * (PM.psc/100)))), 2) as price'))
                        //Min Price Change
                        ->groupBy('products.id')
                        ->whereNotIN('categories.id', $aCatNotAllowed)
                        ->orderBy('products.description')
                        ->get();
                    } else {
                        $prodEvents = array();
                    }
                } else {
                     $prodEvents =   $prodEvents->where('fk_event_id', '=', $eventId)
                        ->groupBy('products.id')
                        ->whereNotIN('categories.id', $aCatNotAllowed)
                        ->orderBy('products.description')
                        ->get();
                }
            if ($fetchParentDetail) {
                $primaryEventDetail         = array();
                $eventModel                 = new Event();
                $primaryEventDetail         = $eventModel->getEventDetailByChildId($eventId);
                $response['parentDetails']  = $primaryEventDetail;
            }
            $productCat = array(
                'hasProducts' => false,
                'Wine' => array(),
                'Beer and Cider' => array(),
                'Spirits' => array(),
                'Soft Drinks' => array(),
                'Food' => array(),
                'Other' => array(),
            );
            foreach ($prodEvents as $productDetails) {
                if (in_array($productDetails->catId, config('appConstants.wine_cat_ids'))) {
                    $productCat['hasProducts'] = true;
                    array_push($productCat['Wine'], $productDetails);
                }
                if (in_array($productDetails->catId, config('appConstants.beer_cat_ids'))) {
                    $productCat['hasProducts'] = true;
                    array_push($productCat['Beer and Cider'], $productDetails);
                }
                if (in_array($productDetails->catId, config('appConstants.spirit_cat_ids'))) {
                    $productCat['hasProducts'] = true;
                    array_push($productCat['Spirits'], $productDetails);
                }
                if (in_array($productDetails->catId, config('appConstants.drinks_cat_ids'))) {
                    $productCat['hasProducts'] = true;
                    array_push($productCat['Soft Drinks'], $productDetails);
                }
                if (in_array($productDetails->catId, config('appConstants.food_cat_ids'))) {
                    $productCat['hasProducts'] = true;
                    array_push($productCat['Food'], $productDetails);
                }
                if (in_array($productDetails->catId, config('appConstants.other_cat_ids'))) {
                    $productCat['hasProducts'] = true;
                    array_push($productCat['Other'], $productDetails);
                }
            }
            $response['products']       = $productCat;
        } catch (Exception $ex) {
            Log::error(__METHOD__ . $ex->getMessage());
        }
        return $response;
    }

    /**
     * Get products Bundles mapping data.
     * 
     * @package Product Model
     * @param int $eventId
     * @return array $bundEvents
     */
    public function getProductBundles($eventId, $nearCheck = FALSE) {
        $bundEvents = array();
        try {
            if ($nearCheck) {
                $aNearByStores = CommonHelper::getNearByStores();
                if (empty($aNearByStores)) {
                    return $bundEvents;
                }
            }
            $bundEvents = DB::table('xref_bundle_events')
                    ->select('bundles.id', 'bundles.name', 'bundles.serves', 'bundles.image', 'bundles.image_thumb',
                            'events.name AS eventName', 'events.image AS eventImage')
                    ->addSelect(DB::raw('ROUND((products.store_price +'
                                        . 'IF(PM.psc = 0.00,( products.store_price * ('
                                        . CommonHelper::getGlobalGPSC() . '/100)) ,( products.store_price * (PM.psc/100)))), 2) as price'))
                    ->addSelect(DB::raw('1 AS is_bundle'))
                    ->addSelect(DB::raw('group_concat(xref_bundle_products.fk_product_id) as product_id'))
                    ->join('bundles', 'bundles.id', '=', 'xref_bundle_events.fk_bundle_id')
                    ->join('events', 'events.id', '=', 'xref_bundle_events.fk_event_id')
                    ->join('xref_bundle_products', 'xref_bundle_products.fk_bundle_id', '=', 'bundles.id')
                    ->join('products', 'products.id', '=', 'xref_bundle_products.fk_product_id')
                    ->join('products_meta AS PM', 'PM.fk_product_id', '=', 'products.id')
                    ->where('fk_event_id', '=', $eventId)
                    ->where('products.deleted_at', '=', NULL)
                    ->groupBy('bundles.id')
                    ->get();
            if ($nearCheck) {
                $bundEvents = $this->getBundlePriceForListing($bundEvents);
            }
        } catch (Exception $ex) {
            Log::error(__METHOD__ . $ex->getMessage());
        }
        return $bundEvents;
    }

    /**
     * Get bundle detail & product detail on basis of bundle id.
     * 
     * @package Product Model
     * @param Integer $bundleId
     * @return object mixed
     */
    public function getBundleDetail($bundleId, $nearCheck = FALSE) {
        $bundEvents = array();
        try {
            if ($nearCheck) {
                $aNearByStores = CommonHelper::getNearByStores();
                if (empty($aNearByStores)) {
                    return $bundEvents;
                }
            }
            
            $bundEvents = DB::table('bundles')
                    ->select('xref_bundle_products.*')
                    ->addSelect('products.name AS productsName')
                    ->addSelect('products.description AS productsDescription')
                    ->addSelect('products.barcode AS productsBarcode')
                    ->addSelect('bundles.name AS bundleName')
                    ->addSelect('bundles.description AS bundleDescription')
                    ->addSelect('bundles.image AS bundleImage')
                    ->addSelect('bundles.serves AS serves')
                    ->addSelect(DB::raw('(ROUND((products.store_price +'
                                        . 'IF(PM.psc = 0.00,( products.store_price * ('
                                        . CommonHelper::getGlobalGPSC() . '/100)) ,( products.store_price * (PM.psc/100)))), 2) * xref_bundle_products.product_quantity) AS priceTot'))
                    ->addSelect(DB::raw('ROUND((products.store_price +'
                                        . 'IF(PM.psc = 0.00,( products.store_price * ('
                                        . CommonHelper::getGlobalGPSC() . '/100)) ,( products.store_price * (PM.psc/100)))), 2) as price'))
                    ->join('xref_bundle_products', 'xref_bundle_products.fk_bundle_id', '=', 'bundles.id')
                    ->join('products', 'products.id', '=', 'xref_bundle_products.fk_product_id')
                    ->join('products_meta AS PM', 'PM.fk_product_id', '=', 'products.id')
                    ->where('bundles.id', '=', $bundleId)
                    ->where('bundles.deleted_at', '=', NULL)
                    ->where('products.deleted_at', '=', NULL)
                    ->get();
            if ($nearCheck) {
                $bundEvents = $this->getBundlePrice($bundEvents);
            }
        } catch (Exception $ex) {
            Log::error(__METHOD__ . $ex->getMessage());
        }
        return $bundEvents;
    }

    /**
     * Get Product detail on basis of product id.
     * 
     * @package Product Model
     * @param Integer $productId
     * @param boolean $isMultiple
     * @param boolean $nearCheck Tells whether radius check has to be applied on products or not
     * @return object mixed
     */
    public function getProductDetails($productId, $isMultiple = FALSE, $nearCheck = FALSE, $getOnlyBarcode = FALSE) {
        $product = array();
        if ($nearCheck) {
            $aNearByStores = CommonHelper::getNearByStores();
            if (empty($aNearByStores)) {
                return $product;
            }
        }
        try {
            $product = DB::table('products AS P');
            if($getOnlyBarcode){
                $product = $product->addSelect('P.barcode AS barcode')->addSelect('P.id AS id');
            }else{
                $configModel = new Configurations();
                $ageLimitDefaultMessage = $configModel->get(config('configurations.age_limit_msg_key'));
                $product = $product->select(DB::raw('IF(P.description = "", " ",  P.description )AS description'))
                    ->addSelect(DB::raw('IF(P.name = "", " ", P.name) AS name'))
                     // For Showing RRSP when no postcode is selected
//                    ->addSelect(DB::raw('IF(P.price = "", " ", P.price) AS price'))
                    ->addSelect(DB::raw('ROUND((P.store_price +'
                                        . 'IF(PM.psc = 0.00,( P.store_price * ('
                                        . CommonHelper::getGlobalGPSC() . '/100)) ,( P.store_price * (PM.psc/100)))), 2) as price'))
//                    ->addSelect(DB::raw('IF( = "", " ", P.store_price) AS price'))
                    ->addSelect(DB::raw('IF(P.store_price = "", " ", P.store_price) AS store_price'))
                    ->addSelect(DB::raw('IF(P.id = "", " ", P.id) AS id'))
                    ->addSelect(DB::raw('IF(PM.product_marketing = "", "N/A", PM.product_marketing) AS product_marketing'))
                    ->addSelect(DB::raw('IF(PM.per100_energy_kcal = "", 0, PM.per100_energy_kcal) AS energy'))
                    ->addSelect(DB::raw('IF(PM.per100_fat = "", 0.0, PM.per100_fat) AS fat'))
                    ->addSelect(DB::raw('IF(PM.per100_thereof_sat_fat = "", 0.0, PM.per100_thereof_sat_fat) AS sat_fat'))
                    ->addSelect(DB::raw('IF(PM.per100_thereof_total_sugar = "", 0.0, PM.per100_thereof_total_sugar) AS sugar'))
                    ->addSelect(DB::raw('IF(PM.per100_salt = "", 0.0, PM.per100_salt) AS salt'))
                    //->addSelect(DB::raw('IF(PM.lower_age_limit = "", " ", PM.lower_age_limit) AS lower_age_limit'))
                    ->addSelect(DB::raw('IF(PM.lower_age_limit_new = "0", " ", "'.$ageLimitDefaultMessage.'") AS lower_age_limit'))
                    ->addSelect(DB::raw('IF(PM.safety_warnings = "", " ", PM.safety_warnings) AS safety_warnings')) 
                    ->addSelect(DB::raw('IF(PM.ingredients = "", " ", PM.ingredients) AS ingredients'))
                    ->addSelect(DB::raw('IF(PM.allergy_advice = "", " ", PM.allergy_advice) AS allergy1'))
                    ->addSelect(DB::raw('IF(PM.allergy_other_text = "", " ", PM.allergy_other_text) AS allergy2')) 
                    ->addSelect(DB::raw('IF(PM.nut_statement = "", " ", PM.nut_statement) AS allergy3'))
                    ->addSelect(DB::raw('IF(PM.psc = "", " ", PM.psc) AS psc'))
                    
                    // added new
                    ->addSelect(DB::raw('IF(PM.servings_washes = "", " ", PM.servings_washes) AS servings'))
                    ->addSelect(DB::raw('IF(PM.alcohol_grape_variety = "", " ", PM.alcohol_grape_variety) AS varietal'))
                    //
                    
                    ->addSelect('P.barcode AS barcode')
                    ->addSelect('PM.meta_keywords AS meta_keywords')
                    ->addSelect('PM.meta_description AS meta_description');
            }
                    $product = $product->join('products_meta AS PM', 'PM.fk_product_id', '=', 'P.id')
                    ->where('P.deleted_at', '=', NULL);
            if ($nearCheck) {
                $product =  $product->join('products_store', 'products_store.fk_product_id', '=', 'P.id')
                        //Min Price Change
                        ->addSelect(DB::raw('ROUND((min(products_store.vendor_price) +'
                                        . 'IF(PM.psc = 0.00,( min(products_store.vendor_price) * ('
                                        . CommonHelper::getGlobalGPSC() . '/100)) ,( min(products_store.vendor_price) * (PM.psc/100)))), 2) as price'))
                        //Min Price Change
                    ->whereIN('products_store.fk_user_id', $aNearByStores);
            }
            if ($isMultiple) {
                $product = $product->whereIn('P.id', $productId)->get();
            } else {
                $product = $product->where('P.id', '=', $productId)->first();
            }
            return $product;
        } catch (Exception $ex) {
            Log::error(__METHOD__ . $ex->getMessage());
        }
    }

    /**
     * Get Bundle Detail on the basis of bundle Id
     * 
     * @package Product Model
     * @param Integer $bundleId
     * @return object Bundles
     */
    public function getBundleDetailForCart($bundleId) {
        try {
            $bundEvents = DB::table('bundles')
                    ->select('xref_bundle_products.*')
                    ->addSelect(DB::raw('ROUND((products.store_price +'
                                        . 'IF(PM.psc = 0.00,( products.store_price * ('
                                        . CommonHelper::getGlobalGPSC() . '/100)) ,( products.store_price * (PM.psc/100)))), 2) as price'))
                    ->addSelect(DB::raw('(xref_bundle_products.product_quantity * ROUND((products.store_price +'
                                        . 'IF(PM.psc = 0.00,( products.store_price * ('
                                        . CommonHelper::getGlobalGPSC() . '/100)) ,( products.store_price * (PM.psc/100)))), 2)) AS priceTot'))
                    ->addSelect('bundles.name AS bundleName')
                    ->addSelect('xref_product_categories.fk_category_id AS prodCatId')
                    ->join('xref_bundle_products', 'xref_bundle_products.fk_bundle_id', '=', 'bundles.id')
                    ->join('products', 'products.id', '=', 'xref_bundle_products.fk_product_id')
                    ->leftJoin('xref_product_categories', 'xref_product_categories.fk_product_id', '=', 'xref_bundle_products.fk_product_id')
                    ->join('products_meta AS PM', 'PM.fk_product_id', '=', 'products.id')
                    ->where('bundles.id', '=', $bundleId)
                    ->where('bundles.deleted_at', '=', NULL)
                    ->where('products.deleted_at', '=', NULL)
                    ->groupBy('products.id')
                    ->get();
            $bundEvents = $this->getBundlePrice($bundEvents);
            $products = [];
            foreach ($bundEvents as $key => $value) {
               $products[] = $value->fk_product_id;
            }
            $specialCategory = DB::table('xref_product_categories')
                    ->addSelect('xref_product_categories.fk_category_id AS prodCatId')
                    ->whereIN('fk_product_id',$products)
                    ->whereIN('fk_category_id', config('appConstants.special_categories'))
                    ->get();
            $specialCategory = empty($specialCategory)? FALSE: TRUE ;
            foreach ($bundEvents as $key => $value) {
                //todo Can be done Specific product wise but not needed as of now.
               $value->specialCategory = $specialCategory;
            }
            return $bundEvents;
        } catch (Exception $ex) {
            Log::error(__METHOD__ . $ex->getMessage());
        }
    }

    /**
     * Get Product occasions mapping data.
     * 
     * @package Product Model
     * @param Integer $occasionId
     * @param Boolean $fetchParentDetail
     * @return array $response
     */
    /*public function getProductOccasions($occasionId, $fetchParentDetail = FALSE, $nearCheck = FALSE) {
        $aNearByStores = array();
        if ($nearCheck) {
            $aNearByStores = CommonHelper::getNearByStores();
        }
        $response = array(
            'products'      => array(),
            'parentDetails' => array(),
        );
        try {
            $prodOccasions = DB::table('xref_product_occasions')
                ->select('products.id', 'products.name', 'products.price', 'products.barcode', 'products.description',
                        'occasions.name AS occasionName', 'occasions.image AS occasionImage')
                //->select('products.*', 'occasions.name AS occasionName', 'occasions.image AS occasionImage')
                ->addSelect(DB::raw('0 AS is_bundle'))
                ->join('products', 'products.id', '=', 'xref_product_occasions.fk_product_id')
                ->join('occasions', 'occasions.id', '=', 'xref_product_occasions.fk_occasion_id');
                // For Near Stores
            if ($nearCheck) {
                if (!empty($aNearByStores)) {
                    $prodOccasions = $prodOccasions->join('products_store', 'products_store.fk_product_id', '=', 'products.id')
                            ->join('products_meta AS PM', 'PM.fk_product_id', '=', 'products.id')
                    ->whereIN('products_store.fk_user_id', $aNearByStores)
                    ->where('fk_occasion_id', '=', $occasionId)
                    //Min Price Change
                    ->addSelect(DB::raw('ROUND((min(products_store.vendor_price) +'
                        . 'IF(PM.psc = 0.00,( min(products_store.vendor_price) * ('
                        . CommonHelper::getGlobalGPSC() . '/100)) ,( min(products_store.vendor_price) * (PM.psc/100)))), 2) as price'))
                    ->addSelect('products.price as product_price')
                    //Min Price Change
                    ->groupBy('products.id')
                    ->orderBy('products.description')
                    ->get();
                } else {
                    $prodOccasions = array();
                }
            } else {
                $prodOccasions = $prodOccasions->where('fk_occasion_id', '=', $occasionId)
                    ->groupBy('products.id')
                    ->orderBy('products.description')
                    ->get();
            }
            if ($fetchParentDetail) {
                $occasionModel              = new Occasion();
                $primaryOccasionDetail      = $occasionModel->getOccasionDetailByChildId($occasionId);
                $response['parentDetails']  = $primaryOccasionDetail;
            }
            $response['products']       = $prodOccasions;
        } catch (Exception $ex) {
            Log::error(__METHOD__ . $ex->getMessage());
        }
        return $response;
    }*/
    public function getProductOccasions($occasionId, $fetchParentDetail = FALSE, $nearCheck = FALSE) {
        $aNearByStores = array();
        if ($nearCheck) {
            $aNearByStores = CommonHelper::getNearByStores();
        }
        $response = array(
            'products'      => array(),
            'parentDetails' => array(),
        );
        try {
            $prodOccasions = DB::table('xref_product_occasions')
                ->select('products.id', 'products.name', 'products.barcode', 'products.description',
                        'occasions.name AS occasionName', 'occasions.image AS occasionImage'
                        , 'sub_text', 'floating_text')
                ->addSelect(DB::raw('ROUND((products.store_price +'
                                        . 'IF(PM.psc = 0.00,( products.store_price * ('
                                        . CommonHelper::getGlobalGPSC() . '/100)) ,( products.store_price * (PM.psc/100)))), 2) as price'))    
                //->select('products.*', 'occasions.name AS occasionName', 'occasions.image AS occasionImage')
                ->addSelect(DB::raw('0 AS is_bundle'))
                ->addSelect('categories.name as catName', 'categories.id as catId')
                ->join('products', 'products.id', '=', 'xref_product_occasions.fk_product_id')
                ->join('occasions', 'occasions.id', '=', 'xref_product_occasions.fk_occasion_id')
                ->join('xref_product_categories', 'xref_product_categories.fk_product_id', '=', 'xref_product_occasions.fk_product_id')
                ->join('categories', 'categories.id', '=', 'xref_product_categories.fk_category_id')
                ->join('products_meta AS PM', 'PM.fk_product_id', '=', 'products.id');
            $prodOccasions = $prodOccasions->where('products.deleted_at', '=', NULL);
                $aCatNotAllowed = config('appConstants.cat_not_allowed');
                // For Near Stores
            if ($nearCheck) {
                if (!empty($aNearByStores)) {
                    $prodOccasions = $prodOccasions->join('products_store', 'products_store.fk_product_id', '=', 'products.id')
                    ->whereIN('products_store.fk_user_id', $aNearByStores)
                    ->where('fk_occasion_id', '=', $occasionId)
                    //Min Price Change
                    ->addSelect(DB::raw('min(products_store.vendor_price) as price'))
                    //Min Price Change
                    ->groupBy('products.id')
                    ->whereNotIN('categories.id', $aCatNotAllowed)
                    ->orderBy('products.description')
                    ->get();
                } else {
                    $prodOccasions = array();
                }
            } else {
                $prodOccasions = $prodOccasions->where('fk_occasion_id', '=', $occasionId)
                    ->groupBy('products.id')
                    ->whereNotIN('categories.id', $aCatNotAllowed)
                    ->orderBy('products.description')
                    ->get();
            }
            if ($fetchParentDetail) {
                $occasionModel              = new Occasion();
                $primaryOccasionDetail      = $occasionModel->getOccasionDetailByChildId($occasionId);
                $response['parentDetails']  = $primaryOccasionDetail;
            }
            $productCat = array(
                'hasProducts' => false,
                'Wine' => array(),
                'Beer and Cider' => array(),
                'Spirits' => array(),
                'Soft Drinks' => array(),
                'Food' => array(),
                'Other' => array(),
            );
            foreach ($prodOccasions as $productDetails) {
                if (in_array($productDetails->catId, config('appConstants.wine_cat_ids'))) {
                    $productCat['hasProducts'] = true;
                    array_push($productCat['Wine'], $productDetails);
                }
                if (in_array($productDetails->catId, config('appConstants.beer_cat_ids'))) {
                    $productCat['hasProducts'] = true;
                    array_push($productCat['Beer and Cider'], $productDetails);
                }
                if (in_array($productDetails->catId, config('appConstants.spirit_cat_ids'))) {
                    $productCat['hasProducts'] = true;
                    array_push($productCat['Spirits'], $productDetails);
                }
                if (in_array($productDetails->catId, config('appConstants.drinks_cat_ids'))) {
                    $productCat['hasProducts'] = true;
                    array_push($productCat['Soft Drinks'], $productDetails);
                }
                if (in_array($productDetails->catId, config('appConstants.food_cat_ids'))) {
                    $productCat['hasProducts'] = true;
                    array_push($productCat['Food'], $productDetails);
                }
                if (in_array($productDetails->catId, config('appConstants.other_cat_ids'))) {
                    $productCat['hasProducts'] = true;
                    array_push($productCat['Other'], $productDetails);
                }
            }
            $response['products']       = $productCat;
        } catch (Exception $ex) {
            Log::error(__METHOD__ . $ex->getMessage());
        }
        return $response;
    }

    /**
     * Get products Bundles mapping data w.r.t occasions.
     * 
     * @package Product Model
     * @param Integer $occasionId
     *
     * @return array $bundEvents
     */
    public function getProductOccasionBundles($occasionId, $nearCheck = FALSE) {
        $bundEvents = array();
        try {
            if ($nearCheck) {
                $aNearByStores = CommonHelper::getNearByStores();
                if (empty($aNearByStores)) {
                    return $bundEvents;
                }
            }
            $bundEvents = DB::table('xref_bundle_occasions')
                    ->select('bundles.id', 'bundles.name', 'bundles.serves', 'bundles.image', 'bundles.image_thumb',
                            'occasions.name AS occasionName', 'occasions.image AS occasionImage')
                    ->addSelect(DB::raw('sum((xref_bundle_products.product_quantity * ROUND((products.store_price +'
                                        . 'IF(PM.psc = 0.00,( products.store_price * ('
                                        . CommonHelper::getGlobalGPSC() . '/100)) ,( products.store_price * (PM.psc/100)))), 2))) AS price'))
                    ->addSelect(DB::raw('1 AS is_bundle'))
                    ->addSelect(DB::raw('group_concat(xref_bundle_products.fk_product_id) as product_id'))
                    ->join('bundles', 'bundles.id', '=', 'xref_bundle_occasions.fk_bundle_id')
                    ->join('occasions', 'occasions.id', '=', 'xref_bundle_occasions.fk_occasion_id')
                    ->join('xref_bundle_products', 'xref_bundle_products.fk_bundle_id', '=', 'bundles.id')
                    ->join('products', 'products.id', '=', 'xref_bundle_products.fk_product_id')
                    ->join('products_meta AS PM', 'PM.fk_product_id', '=', 'products.id')
                    ->where('fk_occasion_id', '=', $occasionId)
                    ->where('products.deleted_at', '=', NULL)
                    ->groupBy('bundles.id')
                    ->get();
            if ($nearCheck) {
                $bundEvents = $this->getBundlePriceForListing($bundEvents);
            }
        } catch (Exception $ex) {
            Log::error(__METHOD__ . $ex->getMessage());
        }
        return $bundEvents;
    }

    /**
     * Method to get Search Products List
     * 
     * @package Product Model
     * @param string $param
     * @return array
     */
    public function searchProducts($param, $nearCheck = true) {
        $products = array();
        try {
            if ($nearCheck) {
                $aNearByStores = CommonHelper::getNearByStores();
                if (empty($aNearByStores)) {
                    return $products;
                }
            }
            $products = DB::table('products')
                    ->addSelect('products.id', 'products.description', 'products.name')
                    ->addSelect(DB::raw('ROUND((products.store_price +'
                                        . 'IF(PM.psc = 0.00,( products.store_price * ('
                                        . CommonHelper::getGlobalGPSC() . '/100)) ,( products.store_price * (PM.psc/100)))), 2) as price'))
            ->join('products_meta AS PM', 'PM.fk_product_id', '=', 'products.id');
            if ($nearCheck) {
                $products = $products
                        ->join('products_store', 'products_store.fk_product_id', '=', 'products.id')
                        //Min Price Change
                        ->addSelect(DB::raw('ROUND((min(products_store.vendor_price) +'
                            . 'IF(PM.psc = 0.00,( min(products_store.vendor_price) * ('
                            . CommonHelper::getGlobalGPSC() . '/100)) ,( min(products_store.vendor_price) * (PM.psc/100)))), 2) as price'))
                        ->addSelect('products.id','products.name','products.description','products.barcode')
                        //Min Price Change
                        ->whereIN('products_store.fk_user_id', $aNearByStores);
            }
            //$products = $products->where('name', 'LIKE', "%{$param}%")
            $products = $products->whereIn('products.id', $param)
                ->where('products.deleted_at', '=', NULL)
                ->groupBy('products.id')
                ->get();
            $products = json_decode(json_encode($products), true);
        } catch (Exception $ex) {
            Log::error(__METHOD__ . $ex->getMessage());
        }
        return $products;
    }

    /**
     * Method for AutoSuggest Search
     * @param string $searchParam serached Parameter String
     * 
     * @package Product Model
     * @param string $searchParam
     * @return array
     */
    public function getAutoSuggestProducts($searchParam = NULL, $nearCheck = false) {
        $aProd = array();
        try {
            if ($nearCheck) {
                $aNearByStores = CommonHelper::getNearByStores();
                if (empty($aNearByStores)) {
                    return $aProd;
                }
            }
            $products = DB::table('products')
                    ->select('name');
            if ($nearCheck) {
                $products = $products->join('products_store', 'products_store.fk_product_id', '=', 'products.id')
                        ->whereIN('products_store.fk_user_id', $aNearByStores);
            }
            $products = $products->where('name', 'LIKE', "%{$searchParam}%")
                ->where('products.deleted_at', '=', NULL)
                ->distinct()
                ->take(7)
                ->get();
            $products = json_decode(json_encode($products), true);
            foreach ($products as $product) {
                array_push($aProd, $product['name']);
            }
        } catch (Exception $ex) {
            Log::error(__METHOD__ . $ex->getMessage());
        }
        return $aProd;
    }

    /**
     * Fetch related ossacion on the basis of bundle/product id
     * 
     * @package Product Model
     * @param Integer $productId
     * @param Boolean $isBundle
     * @return mixed
     */
    public function getRelatedOccasionProduct($productId, $isBundle = FALSE) {
        try {
            $product = [];
            $occasion = new Occasion();
            $table = 'xref_product_occasions';
            $condition = 'fk_product_id';
            if ($isBundle) {
                $table = 'xref_bundle_occasions';
                $condition = 'fk_occasion_id';
            }
            $prodOccasions = DB::table($table)
                    ->select('fk_occasion_id as occasion_id')
                    ->where($condition, '=', $productId)
                    ->get();
            if (empty($prodOccasions)) {
                return $occasion->getPrimaryOccasionsDetail(Config::get('appConstants.related_occasion_count'), $paginationRequired = FALSE);
            }
            foreach ($prodOccasions as $key => $value) {
                if ($key > Config::get('appConstants.related_occasion_count')) {
                    break;
                }
                $product = collect($occasion->getPrimaryOccasionsDetailById($value->occasion_id));
            }
            $count = count($product);
            if ($count < Config::get('appConstants.related_occasion_count')) {
                $newOccasion = $occasion->getPrimaryOccasionsDetail(Config::get('appConstants.related_occasion_count') - $count, FALSE);
                $product = $product->merge($newOccasion);
            }
            return $product;
        } catch (Exception $ex) {
            Log::error(__METHOD__ . $ex->getMessage());
        }
    }

    /**
     * Get Related Products.
     * 
     * @package Product Model
     * @param Integer $productId
     * @param boolean $isBundle
     * @param boolean $nearCheck Tells whether radius check has to be applied on products or not
     * @return mixed
     */
    public function getRelatedProducts($productId, $isBundle = FALSE, $nearCheck = FALSE) {
        try {
            $product = collect();
            if ($isBundle) {
                $bundle = new Bundle();
                $bundleId = $bundle->getBundleMapping($productId);
                $prodRelated = $this->getProductCategory($bundleId['productSelect']);
            } else {
                $prodRelated = $this->getProductCategory($productId);
            }
            foreach ($prodRelated as $key => $value) {
                $productNew = collect($this->getAssociatedProducts([$value->category_id], FALSE, $nearCheck));
                $product = $product->merge($productNew);
            }
            $productIDS = array($productId);
            foreach ($product as $key => $value) {
                if (in_array($value->id, $productIDS)
                        || $value->id == $productId) {
                    $product->forget($key);
                } else {
                    $productIDS[] = $value->id;
                }
            }
            $count = count($product);
            if ($count < Config::get('appConstants.related_products_count')) {
                $newProd = $this->getRandomProducts($productIDS, Config::get('appConstants.related_products_count') - $count, $nearCheck);
                $product = $product->merge($newProd);
            } else {
                $product = $product->take(Config::get('appConstants.related_products_count'));
            }
            return $product;
        } catch (Exception $ex) {
            Log::error(__METHOD__ . $ex->getMessage());
        }
    }

    /**
     * Get random Products.
     * 
     * @package Product Model
     * @param Integer|NULL $notInID
     * @param Integer $limit
     * @param boolean $nearCheck Tells whether radius check has to be applied on products or not
     * @return object products
     */
    public function getRandomProducts($notInID = NULL, $limit = 5, $nearCheck = FALSE) {
        $prod = array();
        try {
            if ($nearCheck) {
                $aNearByStores = CommonHelper::getNearByStores();
                if (empty($aNearByStores)) {
                    return $prod;
                }
            }
            $table = 'products';
            $prod = DB::table($table)
                    ->whereNull('products.deleted_at')
                    ->addSelect('products.id', 'products.name', 'products.description')
                    ->addSelect(DB::raw('ROUND((products.store_price +'
                                        . 'IF(PM.psc = 0.00,( products.store_price * ('
                                        . CommonHelper::getGlobalGPSC() . '/100)) ,( products.store_price * (PM.psc/100)))), 2) as price'))
                    ->join('products_meta AS PM', 'PM.fk_product_id', '=', 'products.id');
            if ($nearCheck) {
                $prod = $prod->join('products_store', 'products_store.fk_product_id', '=', 'products.id')
                        ->select('products.id', 'products.name', 'products.description', 'products.barcode','products.store_price', 'products.net_content', 'products.alcohol_by_volume', 'products.deleted_at', 'products_store.fk_product_id', 'products_store.fk_user_id', 'products_store.vendor_price')
                        //Min Price Change
                        ->addSelect(DB::raw('ROUND((min(products_store.vendor_price) +'
                                        . 'IF(PM.psc = 0.00,( min(products_store.vendor_price) * ('
                                        . CommonHelper::getGlobalGPSC() . '/100)) ,( min(products_store.vendor_price) * (PM.psc/100)))), 2) as price'))
                        ->whereIN('products_store.fk_user_id', $aNearByStores);
            }
            $prod = $prod->groupBy('products.id')
                            ->orderByRaw('RAND()')->take($limit);
            if ($notInID) {
                $prod = $prod->whereNotIn('products.id', $notInID)->get();
            } else {
                $prod = $prod->get();
            }
        } catch (Exception $ex) {
            Log::error(__METHOD__ . $ex->getMessage());
        }
        return $prod;
    }

    /**
     * Get category on the basis of product id.
     * 
     * @package Product Model
     * @param Integer $id
     * @return object xref_product_categories
     */
    public function getProductCategory($id) {
        try {
            if (is_array($id)) {
                return DB::table('xref_product_categories')
                                ->select('fk_category_id as category_id')
                                ->whereIn('fk_product_id', $id)
                                ->get();
            }
            return DB::table('xref_product_categories')
                            ->select('fk_category_id as category_id')
                            ->where('fk_product_id', '=', $id)
                            ->get();
        } catch (Exception $ex) {
            Log::error(__METHOD__ . $ex->getMessage());
        }
    }

    /**
     * Method to get Product Id's of available product at nearby Stores
     *
     * @return array $aProductId
     *
     */
    public function getNearByStoreProducts() {
        $aProductId = array();
        $aNearByStores = CommonHelper::getNearByStores();
        if (empty($aNearByStores)) {
            return $aProductId;
        }
        $products = DB::table('products_store')
                ->leftjoin('products_meta AS PM', 'PM.fk_product_id', '=', 'products_store.fk_product_id')
            ->select('products_store.fk_product_id as product_id')
            //Min Price Change
            ->addSelect(DB::raw('ROUND((min(products_store.vendor_price) +'
                . 'IF(PM.psc = 0.00,( min(products_store.vendor_price) * ('
                . CommonHelper::getGlobalGPSC() . '/100)) ,( min(products_store.vendor_price) * (PM.psc/100)))), 2) as vendor_price'))
            ->whereIN('fk_user_id', $aNearByStores)
            ->groupBy('products_store.fk_product_id')
            ->get();
        foreach ($products as $product) {
            $productId = $product->product_id;
            $productPrice = $product->vendor_price;
            $aProductId[$productId] = $productPrice;
        }
        return $aProductId;
    }

    /**
     * Method to get Search Products By category
     *
     * @param int $catId category Id
     * @param string $param Searched parameter by User
     * @param boolean $nearCheck Whether search in near by stores only
     *
     * @return array $products
     */
    public function searchProductsByCategory($catId, $param, $nearCheck = true) {
        $products = $aCatId = array();
        try {
            if ($nearCheck) {
                $aNearByStores = CommonHelper::getNearByStores();
                if (empty($aNearByStores)) {
                    return $products;
                }
            }
            $catModel   = new Category();
            $aCatTree   = $catModel->getCategoryTree();
            if (isset($aCatTree['categories'][$catId])) {
                foreach ($aCatTree['categories'][$catId]['subCategory'] as $subcatId => $aSubCatDetail) {
                    $aSubSubCatId   = array_keys($aSubCatDetail['subSubCat']);
                    $aSubSubCatId[] = $subcatId;
                    $aCatId         = array_merge($aCatId, $aSubSubCatId);
                }
            }
            if (empty($aCatId)) {
                return $products;
            }
            $products = DB::table('products');
            if ($nearCheck) {
                $products = $products->join('products_store', 'products_store.fk_product_id', '=', 'products.id')
                        ->whereIN('products_store.fk_user_id', $aNearByStores);
            }
            $products = $products->where('name', 'LIKE', "%{$param}%")
                ->join('xref_product_categories', 'xref_product_categories.fk_product_id', '=', 'products.id')
                ->whereIN('xref_product_categories.fk_category_id', $aCatId)
                ->where('products.deleted_at', '=', NULL)
                ->groupBy('products.id')
                ->get();
            $products = json_decode(json_encode($products), true);
        } catch (Exception $ex) {
            Log::error(__METHOD__ . $ex->getMessage());
        }
        return $products;
    }

    /**
     * Method to get BestSeller Products.
     *
     * @param int $count Number of procducts needs to fetch
     *
     * @return array $products Products Details of best seller Products
     */
    public function getBestSellerProducts($count = 30) {
        $products = DB::table('sales_order_item')
                    ->select('products.id', 'products.name', 'products.barcode', 'products.store_price', 'products.description')
                    ->addSelect('sales_order_item.fk_product_id')
                    ->addSelect(DB::raw('count(*) as tot'))
                    ->join('products', 'products.id', '=', 'sales_order_item.fk_product_id')
                    ->groupBy('sales_order_item.fk_product_id')
                    ->orderBy('tot', 'desc')
                    ->take($count)
                    ->get();
        /*if (count($products) < $count) {
            $randomCount    = $count - count($products);
            $randomProducts = $this->getRandomProducts(NULL, $randomCount);
            $products       = array_merge($products, $randomProducts);
        }*/
        return $products;
    }

    /**
     * Method to get BestSeller Products of store.
     *
     * @param int $storeId Store Id
     * @param int $count Number of products needs to fetch
     *
     * @return array $products Products Details of best seller Products
     */
    public function getStoreBestSellerProducts($storeId, $count = 30) {
        $products = DB::table('sales_order_item')
                    ->select('products.id', 'products.name', 'products.barcode', 'products.store_price', 'products.description')
                    ->addSelect('sales_order_item.fk_product_id')
                    ->addSelect(DB::raw('count(*) as tot'))
                    ->join('products', 'products.id', '=', 'sales_order_item.fk_product_id')
                    ->join('products_store', 'products_store.fk_product_id', '=', 'sales_order_item.fk_product_id')
                    ->where('products_store.fk_user_id', '=', $storeId)
                    ->groupBy('sales_order_item.fk_product_id')
                    ->orderBy('tot', 'desc')
                    ->take($count)
                    ->get();
        return $products;
    }

    /**
     *
     * @param int $productId
     * @return Object
     */
    public function getProductDetailAdmin($productId) {
        $product = DB::table('products')
                ->addSelect('products.id AS Pid')
                ->addSelect('name', 'description', 'price', 'store_price')
                ->addSelect('products_meta.*')
                ->join('products_meta', 'products_meta.fk_product_id', '=', 'products.id')
                //->where('products.deleted_at', '=', NULL)
                ->where('products.id', '=', $productId)->first();
        return $product;
    }

    /**
     *
     * @param int $productId
     * @return Object
     */
    public function getProductsAdmin() {
        $product = DB::table('products')->orderBy('name', 'ASC')->get();
        return $product;
    }

    /**
     *
     * @param int $productId
     * @param array $aAttribute
     * @return array
     */
    public function updateProductDetailsAdmin($productId, $aAttribute) {
        $aResponse = array(
            'status' => false,
            'message' => trans('messages.common_error'),
        );
        try {
            $aAttributeProduct = array_only($aAttribute, ['name', 'description', 'price', 'store_price']);
            DB::table('products')->where('id', '=', $productId)
                ->update($aAttributeProduct);
            $aAttributeProductMeta = array_diff_key($aAttribute, $aAttributeProduct);
            DB::table('products_meta')->where('fk_product_id', '=', $productId)
                ->update($aAttributeProductMeta);
            $aResponse['status'] = true;
        } catch (Exception $ex) {
            $aResponse['message'] = $ex->getMessage();
        }
        return $aResponse;
    }

    /**
     * Get Bundle Detail on the basis of bundle Id
     * 
     * @param Integer $productId
     * @return array $prodDetails
     */
    public function getProductDetailForCart($productId) {
        $prodDetails = array();
        try {
            $aNearByStores = CommonHelper::getNearByStores();
            $prodDetails = DB::table('products')
                    ->addSelect('xref_product_categories.fk_category_id AS prodCatId')
                    ->leftJoin('xref_product_categories', 'xref_product_categories.fk_product_id', '=', 'products.id')
                    ->join('products_store', 'products_store.fk_product_id', '=', 'products.id')
                    ->join('products_meta AS PM', 'PM.fk_product_id', '=', 'products.id')
                     //Min Price Change
                    ->addSelect(DB::raw('ROUND((min(products_store.vendor_price) +'
                        . 'IF(PM.psc = 0.00,( min(products_store.vendor_price) * ('
                        . CommonHelper::getGlobalGPSC() . '/100)) ,( min(products_store.vendor_price) * (PM.psc/100)))), 2) as price'))
                    ->whereIN('products_store.fk_user_id', $aNearByStores)
                     //Min Price Change
                    ->where('products.id', '=', $productId)
                    ->where('products.deleted_at', '=', NULL)
                    ->orderBy('prodCatId', 'desc')
                    ->first();
            $specialCategory = DB::table('xref_product_categories')
                    ->addSelect('xref_product_categories.fk_category_id AS prodCatId')
                    ->where('fk_product_id', $productId)
                    ->whereIN('fk_category_id', config('appConstants.special_categories'))
                    ->get();
            $prodDetails->specialCategory = empty($specialCategory)? FALSE: TRUE ;
        } catch (Exception $ex) {
            Log::error(__METHOD__ . $ex->getMessage());
        }
        return $prodDetails;
    }
    
    /**
     * Get product count in a near by store
     * 
     * @param array $nearByStores
     * @return array $products
     */
    public function getStoreProductCount($nearByStores) {
        $products = array();
        try {
            $products = DB::table('products_store')
                    ->select('products_store.fk_product_id')
                    ->join('products', 'products.id', '=', 'products_store.fk_product_id')
                    ->whereIN('products_store.fk_user_id', $nearByStores)
                    ->first();
        } catch (Exception $ex) {
            Log::error(__METHOD__ . $ex->getMessage());
        }
        return $products;
    }
    
    /**
     * Method will return product details by category Id.
     * 
     * @param Array $catId
     * @return array
     */
    public function getProductDetailsByCategory($catId) {
        $prodDetails = array();
        try {
            $prodDetails = DB::table('products')
                    ->select('products.id', 'products.description',
                             'products.barcode', 
                            'products.deleted_at')
                    ->addSelect(DB::raw('ROUND((products.store_price +'
                                        . 'IF(pm.psc = 0.00,( products.store_price * ('
                                        . CommonHelper::getGlobalGPSC() . '/100)) ,( products.store_price * (pm.psc/100)))), 2) as price'))
                    ->addSelect('xref_product_categories.fk_product_id', 
                            'xref_product_categories.fk_category_id')
                    ->addSelect('pm.fk_product_id', 'pm.product_marketing')
                    ->join('products_meta AS pm', 'pm.fk_product_id', '=', 'products.id')
                    ->leftJoin('xref_product_categories', 'xref_product_categories.fk_product_id', '=', 'products.id')
                    ->whereIn('xref_product_categories.fk_category_id', $catId)
                    ->where('products.deleted_at', '=', NULL)
                    ->where('pm.in_data_feed', '=', '1')
                    ->get();
        } catch (Exception $ex) {
            Log::error(__METHOD__ . $ex->getMessage());
        }
        return $prodDetails;
    }
    
    /**
     * Return all the products which are in stock.
     * 
     * @return array
     */
    public function getInStockProducts() {
        $prods = array();
        try {
            $prodData = DB::table('products_store')
                    ->select('products_store.fk_product_id')
                    ->get();
            foreach ($prodData as $key => $value) {
               $prods[] = $value->fk_product_id;
            }
        } catch (Exception $ex) {
            Log::error(__METHOD__ . $ex->getMessage());
        }
        return $prods;
    }
    
    public function getBundleQuantity($bundleId, $productId) {
        $prods = array();
        try {
            $prods = DB::table('xref_bundle_products')
                    ->select('product_quantity')
                    ->where(['fk_bundle_id' => $bundleId, 
                        'fk_product_id' => $productId])
                    ->first();
        } catch (Exception $ex) {
            Log::error(__METHOD__ . $ex->getMessage());
        }
        return $prods;
    }
    
    /**
     * Fetch bundle Individual product price & bundle total price.
     * 
     * @param array $bundEvents
     * @return array
     */
    public function getBundlePrice($bundEvents) {
        $aNearStoreProductId = $this->getNearByStoreProducts();
        foreach ($bundEvents as $key => $bundleProduct) {
            $bundleProductId = $bundleProduct->fk_product_id;
            if (!isset($aNearStoreProductId[$bundleProductId])) {
                $bundEvents = array();
                break;
            } else {
                $bundEvents[$key]->price = $aNearStoreProductId[$bundleProductId];
                if(isset($bundleProduct->product_quantity)){
                    $qty = $bundleProduct->product_quantity;
                } else{
                    $prodQty = $this->getBundleQuantity($bundleProduct->id, $bundleProductId);
                    $qty = $prodQty->product_quantity;
                }
                $bundEvents[$key]->priceTot = $qty * $aNearStoreProductId[$bundleProductId];
            }
        }
        return $bundEvents;
    }
    
    
    /**
     * Fetch bundle Individual product price & bundle total price for listing.
     * 
     * @param array $bundEvents
     * @return array
     */
    public function getBundlePriceForListing($bundEvents) {
        $aNearStoreProductId = $this->getNearByStoreProducts();
        foreach ($bundEvents as $key => $bundleProduct) {
            $vendorPrice = $vendorTotalBundlePrice = [];
            $bundleProductId = $bundleProduct->product_id;
            $aBundleProductId = explode(",", $bundleProductId);
            if (count(array_diff($aBundleProductId, array_keys($aNearStoreProductId))) != 0) {
                unset($bundEvents[$key]);
            } else {
                foreach ($aBundleProductId as $productId) {
                    $vendorPrice[$productId] = $aNearStoreProductId[$productId];
                    $prodQty = $this->getBundleQuantity($bundleProduct->id, $productId);
                    $vendorTotalBundlePrice[$productId] = $prodQty->product_quantity * $aNearStoreProductId[$productId];
                }
                $bundEvents[$key]->vendor_price = $vendorPrice;
                $bundEvents[$key]->price = array_sum($vendorTotalBundlePrice);
            }
        }
        return $bundEvents;
    }

    /**
     * Method to get popular & Gift Product on Home Page
     *
     * @param int $isPopular
     * @param int $isGift
     * @param int $limit
     * @param boolean $hasSubCat
     * @param boolean $nearCheck Tells whether radius check has to be applied on products or not
     * @return mixed
     */
    public function getPopularProducts($isPopular = 0, $isGift = 0, $limit = 10, $nearCheck = false) {
        $prod = array();
        try {
            if ($nearCheck) {
                $aNearByStores = CommonHelper::getNearByStores();
                if (empty($aNearByStores)) {
                    return $prod;
                }
            }
            $prod = DB::table('products')
                    ->addSelect('products.*')
                    ->addSelect(DB::raw('ROUND((products.store_price +'
                                        . 'IF(PM.psc = 0.00,( products.store_price * ('
                                        . CommonHelper::getGlobalGPSC() . '/100)) ,( products.store_price * (PM.psc/100)))), 2) as price'))
                    ->join('products_meta AS PM', 'PM.fk_product_id', '=', 'products.id');
            if ($nearCheck) {
                $prod =   $prod->join('products_store', 'products_store.fk_product_id', '=', 'products.id')
                        //Min Price Change
                        ->addSelect(DB::raw('ROUND((min(products_store.vendor_price) +'
                                        . 'IF(PM.psc = 0.00,( min(products_store.vendor_price) * ('
                                        . CommonHelper::getGlobalGPSC() . '/100)) ,( min(products_store.vendor_price) * (PM.psc/100)))), 2) as price'))
                        ->addSelect('products.id','products.name','products.description','products.barcode')
                        //Min Price Change
                        ->whereIN('products_store.fk_user_id', $aNearByStores);
            }
                if (!empty($isPopular)) {
                    $prod = $prod->where('PM.is_popular', '=', $isPopular);
                }
                if (!empty($isGift)) {
                    $prod = $prod->where('PM.is_gifts', '=', $isGift);
                }
                $prod = $prod->where('products.deleted_at', '=', NULL)
                    ->orderBy('products.description')
                    ->groupBy('products.id')
                    ->take($limit)
                    ->get();
            foreach ($prod as $prodSingle) {
                $prodSingle->imagePath = CommonHelper::getProductImage($prodSingle->id, $isThumb = true);
            }
        } catch (Exception $ex) {
            Log::error(__METHOD__ . $ex->getMessage());
        }
        return $prod;
    }

    /**
     * Method to get Top Bundles & Recipes for Home Page
     *
     * @param int $isRecipe
     * @param int $limit
     * @return array $aTopBundles
     */
    public function getTopBundles($isRecipe = 0, $limit = 10, $nearCheck = false) {
        $aTopBundles = array();
        try {
            if ($nearCheck) {
                $aNearByStores = CommonHelper::getNearByStores();
                if (empty($aNearByStores)) {
                    return $aTopBundles;
                }
            }
            $aTopBundles = DB::table('bundles')
                    ->select('bundles.id', 'bundles.name', 'bundles.serves', 'bundles.image', 'bundles.image_thumb',
                             DB::raw('sum((xref_bundle_products.product_quantity * ROUND((products.store_price +'
                                        . 'IF(PM.psc = 0.00,( products.store_price * ('
                                        . CommonHelper::getGlobalGPSC() . '/100)) ,( products.store_price * (PM.psc/100)))), 2))) AS price'))
                    ->addSelect(DB::raw('group_concat(xref_bundle_products.fk_product_id) as product_id'))
                    ->join('xref_bundle_products', 'xref_bundle_products.fk_bundle_id', '=', 'bundles.id')
                    ->join('products', 'products.id', '=', 'xref_bundle_products.fk_product_id')
                    ->join('products_meta AS PM', 'PM.fk_product_id', '=', 'products.id')
                    ->where('is_recipe', '=', $isRecipe)
                    ->where('bundles.deleted_at', '=', NULL)
                    ->where('products.deleted_at', '=', NULL)
                    ->where('bundles.id', '!=', NULL)
                    ->groupBy('bundles.id')
                    ->get();
            if ($nearCheck) {
                $aTopBundles = $this->getBundlePriceForListing($aTopBundles);
            }
        } catch (Exception $ex) {
            Log::error(__METHOD__ . $ex->getMessage());
        }
        return $aTopBundles;
    }

    /**
     * Method to get Popular / Gift Bundles
     *
     * @param int $isPopular
     * @param int $isGift
     * @param int $limit
     * @return array $aPopularBundles
     */
    public function getPopularBundles($isPopular = 0, $isGift = 0, $limit = 10, $nearCheck = false) {
        $aPopularBundles = array();
        try {
            if ($nearCheck) {
                $aNearByStores = CommonHelper::getNearByStores();
                if (empty($aNearByStores)) {
                    return $aPopularBundles;
                }
            }
            $aPopularBundles = DB::table('bundles')
                    ->select('bundles.id', 'bundles.name', 'bundles.serves', 'bundles.image' , 'bundles.image_thumb')
                    ->addSelect(DB::raw('sum((xref_bundle_products.product_quantity * ROUND((products.store_price +'
                                        . 'IF(PM.psc = 0.00,( products.store_price * ('
                                        . CommonHelper::getGlobalGPSC() . '/100)) ,( products.store_price * (PM.psc/100)))), 2))) AS price'))
                    ->addSelect(DB::raw('group_concat(xref_bundle_products.fk_product_id) as product_id'))
                    ->join('xref_bundle_products', 'xref_bundle_products.fk_bundle_id', '=', 'bundles.id')
                    ->join('products', 'products.id', '=', 'xref_bundle_products.fk_product_id')
                    ->join('products_meta AS PM', 'PM.fk_product_id', '=', 'products.id');
            if (!empty($isPopular)) {
                $aPopularBundles = $aPopularBundles->where('bundles.is_popular', '=', $isPopular);
            }
            if (!empty($isGift)) {
                $aPopularBundles = $aPopularBundles->where('bundles.is_gift', '=', $isGift);
            }
            $aPopularBundles = $aPopularBundles->where('bundles.deleted_at', '=', NULL)
                    ->where('products.deleted_at', '=', NULL)
                    ->where('bundles.id', '!=', NULL)
                    ->groupBy('bundles.id')
                    ->get();
            if ($nearCheck) {
                $aPopularBundles = $this->getBundlePriceForListing($aPopularBundles);
            }
        } catch (Exception $ex) {
            Log::error(__METHOD__ . $ex->getMessage());
        }
        return $aPopularBundles;
    }

    /**
     * Method to matching Products for locale
     * 
     * @param string $searchTerm
     * @return object Product
     */
    public function getMatchingProducts($searchTerm) {
        $products = Product::orderBy('name', 'asc')->where('name', 'LIKE', "%{$searchTerm}%")
                ->orWhere('description', 'LIKE', "%{$searchTerm}%")->take(50)->get();
        foreach ($products as $key => $item) {
            $item->name = $item->name .'--'.CommonHelper::formatProductDescription($item->description);
        }
        return $products;
    }

    /**
     * Get Product locale mapping data.
     * 
     * @package Product Model
     * @param int $localeId
     * @param boolean $nearCheck
     *
     * @return array $response
     */
    public function getLocaleProducts($localeId, $nearCheck = FALSE) {
        $aNearByStores = array();
        if ($nearCheck) {
            $aNearByStores = CommonHelper::getNearByStores();
        }
        $response = array(
            'products'      => array(),
        );
        try {
            $prodEvents = DB::table('xref_locale_product')
                ->select('products.id', 'products.name', 'products.barcode', 'products.description')
                ->addSelect(DB::raw('ROUND((products.store_price +'
                                        . 'IF(PM.psc = 0.00,( products.store_price * ('
                                        . CommonHelper::getGlobalGPSC() . '/100)) ,( products.store_price * (PM.psc/100)))), 2) as price'))
                ->addSelect('categories.name as catName', 'categories.id as catId')
                ->join('products', 'products.id', '=', 'xref_locale_product.fk_product_id')
                ->join('locale', 'locale.id', '=', 'xref_locale_product.fk_locale_id')
                ->join('xref_product_categories', 'xref_product_categories.fk_product_id', '=', 'xref_locale_product.fk_product_id')
                ->join('categories', 'categories.id', '=', 'xref_product_categories.fk_category_id')
                ->join('products_meta AS PM', 'PM.fk_product_id', '=', 'products.id');
            $prodEvents = $prodEvents->where('products.deleted_at', '=', NULL);
                // For Near Stores
                $aCatNotAllowed = config('appConstants.cat_not_allowed');
                if ($nearCheck) {
                    if (!empty($aNearByStores)) {
                        $prodEvents =   $prodEvents->join('products_store', 'products_store.fk_product_id', '=', 'products.id')
                        ->whereIN('products_store.fk_user_id', $aNearByStores)
                        ->where('fk_locale_id', '=', $localeId)
                        //Min Price Change
                        ->addSelect(DB::raw('ROUND((min(products_store.vendor_price) +'
                            . 'IF(PM.psc = 0.00,( min(products_store.vendor_price) * ('
                            . CommonHelper::getGlobalGPSC() . '/100)) ,( min(products_store.vendor_price) * (PM.psc/100)))), 2) as price'))
                        //Min Price Change
                        ->groupBy('products.id')
                        ->whereNotIN('categories.id', $aCatNotAllowed)
                        ->orderBy('xref_locale_product.sort_order')
                        ->orderBy('products.description')
                        ->get();
                    } else {
                        $prodEvents = array();
                    }
                } else {
                     $prodEvents =   $prodEvents->where('fk_locale_id', '=', $localeId)
                        ->groupBy('products.id')
                        ->whereNotIN('categories.id', $aCatNotAllowed)
                        ->orderBy('xref_locale_product.sort_order')
                        ->orderBy('products.description')
                        ->get();
                }
            $productCat = array(
                'hasProducts' => false,
                'Wine' => array(),
                'Beer and Cider' => array(),
                'Spirits' => array(),
                'Soft Drinks' => array(),
                'Food' => array(),
                'Other' => array(),
            );
            foreach ($prodEvents as $productDetails) {
                if (in_array($productDetails->catId, config('appConstants.wine_cat_ids'))) {
                    $productCat['hasProducts'] = true;
                    array_push($productCat['Wine'], $productDetails);
                }
                if (in_array($productDetails->catId, config('appConstants.beer_cat_ids'))) {
                    $productCat['hasProducts'] = true;
                    array_push($productCat['Beer and Cider'], $productDetails);
                }
                if (in_array($productDetails->catId, config('appConstants.spirit_cat_ids'))) {
                    $productCat['hasProducts'] = true;
                    array_push($productCat['Spirits'], $productDetails);
                }
                if (in_array($productDetails->catId, config('appConstants.drinks_cat_ids'))) {
                    $productCat['hasProducts'] = true;
                    array_push($productCat['Soft Drinks'], $productDetails);
                }
                if (in_array($productDetails->catId, config('appConstants.food_cat_ids'))) {
                    $productCat['hasProducts'] = true;
                    array_push($productCat['Food'], $productDetails);
                }
                if (in_array($productDetails->catId, config('appConstants.other_cat_ids'))) {
                    $productCat['hasProducts'] = true;
                    array_push($productCat['Other'], $productDetails);
                }
            }
            $response['products']       = $productCat;
        } catch (Exception $ex) {
            Log::error(__METHOD__ . $ex->getMessage());
        }
        return $response;
    }

    /**
     * Get products Bundles mapping data.
     * 
     * @package Product Model
     * @param int $eventId
     * @return array $bundEvents
     */
    public function getLocaleBundles($localeId, $isBundle, $nearCheck = FALSE) {
        $bundEvents = array();
        try {
            if ($nearCheck) {
                $aNearByStores = CommonHelper::getNearByStores();
                if (empty($aNearByStores)) {
                    return $bundEvents;
                }
            }
            $bundEvents = DB::table('xref_locale_bundle')
                    ->select('bundles.id', 'bundles.name', 'bundles.serves', 'bundles.image', 'bundles.image_thumb')
                    ->addSelect(DB::raw('ROUND((products.store_price +'
                                        . 'IF(PM.psc = 0.00,( products.store_price * ('
                                        . CommonHelper::getGlobalGPSC() . '/100)) ,( products.store_price * (PM.psc/100)))), 2) as price'))
                    ->addSelect(DB::raw('1 AS is_bundle'))
                    ->addSelect(DB::raw('group_concat(xref_bundle_products.fk_product_id) as product_id'))
                    ->join('bundles', 'bundles.id', '=', 'xref_locale_bundle.fk_bundle_id')
                    ->join('locale', 'locale.id', '=', 'xref_locale_bundle.fk_locale_id')
                    ->join('xref_bundle_products', 'xref_bundle_products.fk_bundle_id', '=', 'bundles.id')
                    ->join('products', 'products.id', '=', 'xref_bundle_products.fk_product_id')
                    ->join('products_meta AS PM', 'PM.fk_product_id', '=', 'products.id')
                    ->where('fk_locale_id', '=', $localeId)
                    ->where('is_bundle', '=', $isBundle)
                    ->where('products.deleted_at', '=', NULL)
                    ->groupBy('bundles.id')
                    ->get();
            if ($nearCheck) {
                $bundEvents = $this->getBundlePriceForListing($bundEvents);
            }
        } catch (Exception $ex) {
            Log::error(__METHOD__ . $ex->getMessage());
        }
        return $bundEvents;
    }

    /**
     * Get Product locale mapping data.
     * 
     * @package Product Model
     * @param int $localeId
     * @param boolean $nearCheck
     *
     * @return array $response
     */
    public function getBrandProducts($brandId, $nearCheck = FALSE) {
        $aNearByStores = array();
        if ($nearCheck) {
            $aNearByStores = CommonHelper::getNearByStores();
        }
        $response = array(
            'products'      => array(),
        );
        try {
            $prodEvents = DB::table('xref_brand_product')
                ->select('products.id', 'products.name', 'products.barcode', 'products.description')
                ->addSelect(DB::raw('ROUND((products.store_price +'
                                        . 'IF(PM.psc = 0.00,( products.store_price * ('
                                        . CommonHelper::getGlobalGPSC() . '/100)) ,( products.store_price * (PM.psc/100)))), 2) as price'))
                ->join('products', 'products.id', '=', 'xref_brand_product.fk_product_id')
                ->join('brand', 'brand.id', '=', 'xref_brand_product.fk_brand_id')
                ->join('products_meta AS PM', 'PM.fk_product_id', '=', 'products.id');
            $prodEvents = $prodEvents->where('products.deleted_at', '=', NULL);
                // For Near Stores
                if ($nearCheck) {
                    if (!empty($aNearByStores)) {
                        $prodEvents =   $prodEvents->join('products_store', 'products_store.fk_product_id', '=', 'products.id')
                        ->whereIN('products_store.fk_user_id', $aNearByStores)
                        ->where('fk_brand_id', '=', $brandId)
                        //Min Price Change
                        ->addSelect(DB::raw('ROUND((min(products_store.vendor_price) +'
                            . 'IF(PM.psc = 0.00,( min(products_store.vendor_price) * ('
                            . CommonHelper::getGlobalGPSC() . '/100)) ,( min(products_store.vendor_price) * (PM.psc/100)))), 2) as price'))
                        //Min Price Change
                        ->groupBy('products.id')
                        ->orderBy('xref_brand_product.sort_order')
                        ->orderBy('products.description')
                        ->get();
                    } else {
                        $prodEvents = array();
                    }
                } else {
                     $prodEvents =   $prodEvents->where('fk_brand_id', '=', $brandId)
                        ->groupBy('products.id')
                        ->orderBy('xref_brand_product.sort_order')
                        ->orderBy('products.description')
                        ->get();
                }
            $response['products']       = $prodEvents;
        } catch (Exception $ex) {
            Log::error(__METHOD__ . $ex->getMessage());
        }
        return $response;
    }

    /**
     * Get products Bundles mapping data.
     * 
     * @package Product Model
     * @param int $eventId
     * @return array $bundEvents
     */
    public function getBrandBundles($brandId, $isBundle, $nearCheck = FALSE) {
        $bundEvents = array();
        try {
            if ($nearCheck) {
                $aNearByStores = CommonHelper::getNearByStores();
                if (empty($aNearByStores)) {
                    return $bundEvents;
                }
            }
            $bundEvents = DB::table('xref_brand_bundle')
                    ->select('bundles.id', 'bundles.name', 'bundles.serves', 'bundles.image', 'bundles.image_thumb')
                    ->addSelect(DB::raw('ROUND((products.store_price +'
                                        . 'IF(PM.psc = 0.00,( products.store_price * ('
                                        . CommonHelper::getGlobalGPSC() . '/100)) ,( products.store_price * (PM.psc/100)))), 2) as price'))
                    ->addSelect(DB::raw('1 AS is_bundle'))
                    ->addSelect(DB::raw('group_concat(xref_bundle_products.fk_product_id) as product_id'))
                    ->join('bundles', 'bundles.id', '=', 'xref_brand_bundle.fk_bundle_id')
                    ->join('brand', 'brand.id', '=', 'xref_brand_bundle.fk_brand_id')
                    ->join('xref_bundle_products', 'xref_bundle_products.fk_bundle_id', '=', 'bundles.id')
                    ->join('products', 'products.id', '=', 'xref_bundle_products.fk_product_id')
                    ->join('products_meta AS PM', 'PM.fk_product_id', '=', 'products.id')
                    ->where('fk_brand_id', '=', $brandId)
                    ->where('is_bundle', '=', $isBundle)
                    ->where('products.deleted_at', '=', NULL)
                    ->groupBy('bundles.id')
                    ->get();
            if ($nearCheck) {
                $bundEvents = $this->getBundlePriceForListing($bundEvents);
            }
        } catch (Exception $ex) {
            Log::error(__METHOD__ . $ex->getMessage());
        }
        return $bundEvents;
    }

}
