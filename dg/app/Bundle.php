<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

use DB;
use Log;
use Exception;
use App\Http\Helper\CommonHelper;

class Bundle extends Model {

    //
    use SoftDeletes;

    protected $table = "bundles";
    //only allow the following items to be mass-assigned to our model
    protected $fillable = ['name', 'image', 'image_thumb', 'description', 'serves', 'is_recipe', 'is_popular', 'is_gift'];
    protected $dates = ['deleted_at'];

    /**
     * Wrapper Method to update Bundle Mapping
     * 
     * @param type $aMap
     * @return void
     *
     */
    public function saveBundleMapping($aMap) {
        $bundleId = $aMap['id'];
        try {
            DB::beginTransaction();
            $this->updateBundleProductMapping($bundleId, $aMap['data']);
            DB::commit();
        } catch (Exception $ex) {
            Log::error('Error while updating Bundle Maping|'. $ex->getMessage());
            DB::rollBack();
        }
    }

    /**
     * Method to update Bundle Product Mapping
     *
     * @param int $bundleId
     * @param array $aCat
     * @return void
     */
    public function updateBundleProductMapping($bundleId, $aCat) {
        if (!empty($aCat)) {
            $insertData = array();
            foreach ($aCat as $index => $catId) {
                $aData = array(
                    'fk_bundle_id' => $bundleId,
                    'fk_product_id' => $index,
                    'product_quantity' => $catId,
                );
                array_push($insertData, $aData);
            }
            DB::table('xref_bundle_products')->insert($insertData); // Query Builder
        }
    }
    
    /**
     * Method to get Bundle Mapping with products
     *
     * @param int $bundleId
     * @return array $aProdMapping
     *
     */
    public function getBundleMapping($bundleId) {
        $bundleProd = DB::table('xref_bundle_products')
                ->select('fk_product_id as product_id', 'product_quantity as quantity', 'name as product_name', 'description')
                ->join('products', 'products.id', '=', 'xref_bundle_products.fk_product_id')
                ->where('fk_bundle_id', '=', $bundleId)
                ->get();
        
        $idProduct = $qtyProduct = $nameProduct = array();
        foreach ($bundleProd as $bundleMap) {
            array_push($idProduct, $bundleMap->product_id);
            array_push($qtyProduct, $bundleMap->quantity);
            array_push($nameProduct, $bundleMap->product_name .'--' . CommonHelper::formatProductDescription($bundleMap->description));
        }
        $aProdMapping = array(
            'productSelect' => $idProduct,
            'quantity' => $qtyProduct,
            'name' => $nameProduct,
        );
        return $aProdMapping;
    }

    /**
     * Method to get Bundle Mapping with Events & Occasion
     *
     * @param int $bundleId
     * @return array $aBundleMapping
     */
    public function getBundleEventOccasionMapping($bundleId) {
        $bundleOccasions = DB::table('xref_bundle_occasions')
                ->select('fk_occasion_id as occasion_id')
                ->where('fk_bundle_id', '=', $bundleId)
                ->get();

        $bundleEvents = DB::table('xref_bundle_events')
                ->select('fk_event_id as event_id')
                ->where('fk_bundle_id', '=', $bundleId)
                ->get();

        $aOccasion = $aEvent = array();
        foreach ($bundleOccasions as $occasion) {
            array_push($aOccasion, $occasion->occasion_id);
        }
        foreach ($bundleEvents as $event) {
            array_push($aEvent, $event->event_id);
        }
        $aBundleMapping = array(
            'event' => $aEvent,
            'occasion' => $aOccasion
        );
        return $aBundleMapping;
    }

    /**
     * Wrapper Method to update Bundle Event/Occasion Mapping
     *
     * @param array $aMap
     * @return void
     *
     */
    public function saveBundleEventOccasionMapping($aMap) {
        $bundleId = $aMap['id'];
        try {
            DB::beginTransaction();
            $this->updateBundleEventMapping($bundleId, $aMap['event']);
            $this->updateBundleOccasionMapping($bundleId, $aMap['occasion']);
            DB::commit();
        } catch (Exception $ex) {
            Log::error('Error|'.$ex->getMessage());
            DB::rollBack();
        }
    }

    /**
     * Method to update BundleEventMapping
     *
     * @param int $bundleId
     * @param array $aEvent
     * @return void
     *
     */
    public function updateBundleEventMapping($bundleId, $aEvent) {
        if (!empty($aEvent)) {
            DB::table('xref_bundle_events')->where('fk_bundle_id', '=', $bundleId)->delete();
            $insertData = array();
            foreach ($aEvent as $index => $eventId ) {
                $aData = array(
                    'fk_bundle_id' => $bundleId,
                    'fk_event_id' => $eventId,
                );
                array_push($insertData, $aData);
            }
            DB::table('xref_bundle_events')->insert($insertData);
        }
    }

    /**
     * Method to update BundleOccasionMapping
     *
     * @param int $bundleId
     * @param array $aOccasion
     * @return void
     *
     */
    public function updateBundleOccasionMapping($bundleId, $aOccasion) {
        if (!empty($aOccasion)) {
            DB::table('xref_bundle_occasions')->where('fk_bundle_id', '=', $bundleId)->delete();
            $insertData = array();
            foreach ($aOccasion as $index => $occasionId ) {
                $aData = array(
                    'fk_bundle_id' => $bundleId,
                    'fk_occasion_id' => $occasionId,
                );
                array_push($insertData, $aData);
            }
            DB::table('xref_bundle_occasions')->insert($insertData);
        }
    }

    /**
     * Method to matching Bundle / Recipe for locale
     * 
     * @param string $searchTerm
     * @param int $isRecipe
     * @return object Bundle
     */
    public function getMatchingBundles($searchTerm, $isRecipe) {
        $bundles = Bundle::orderBy('name', 'asc')->where('name', 'LIKE', "%{$searchTerm}%")
                ->where('is_recipe', '=', $isRecipe)
                ->take(20)->get();
        return $bundles;
    }
}
