<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Facades\Log;

class Brand extends Model
{
    //
    /**
     * Set table name.
     * @var type 
     */
    protected $table = "brand";
    //only allow the following items to be mass-assigned to our model
    protected $fillable = ['title','sub_title', 'image',  'image_background',
        'url_path', 'meta_title', 'meta_keywords', 'meta_description', 'active',
        'image2', 'image3', 'image4', 'button_text', 'button_url', 'is_external'];
    
    /**
     * Return validation rules for Events.
     * 
     * @return array
     */
    public function validationRules($id = NULL) {
        $rules = array(
            'title'         => 'required',
            'sub_title'     => 'required',
            'button_text'   => 'max:80',
            'button_url'    => 'url',
        );
        if ($id != NULL) {
            $rules = array_merge($rules, ['url_path' => 'required|unique:brand,url_path,' . $id . ',id']);
        } else {
            $rules = array_merge($rules, ['url_path' => 'required|unique:brand,url_path,NULL,id',
                'image' => 'required', 'image_background' => 'required']);
        }
        return $rules;
    }

    /**
     * Method to update Brand Product mapping
     *
     * @param int $brandId Primary Id of brand
     * @param array $aProductId
     */
    public static function saveBrandProductMapping($brandId, array $aProductId) {
        DB::table('xref_brand_product')->where('fk_brand_id', '=', $brandId)->delete();
        $insertData = array();
        foreach ($aProductId as $index => $productId) {
            if (!empty($productId)) {
                $sortOrder = $index + 1; // as index starts from 0.
                $aData = array(
                    'fk_brand_id'  => $brandId,
                    'fk_product_id' => $productId,
                    'sort_order'    => $sortOrder,
                );
                array_push($insertData, $aData);
            }
        }
        if (!empty($insertData)) {
            DB::table('xref_brand_product')->insert($insertData);
        }
        
    }

    /**
     * Method to update Brand Bundle / Recipe mapping
     *
     * @param int $brandId Primary Id of brand
     * @param array $aBundleId
     * @param int $isBundle
     */
    public static function saveBrandBundleMapping($brandId, array $aBundleId, $isBundle = 0) {
        DB::table('xref_brand_bundle')->where('fk_brand_id', '=', $brandId)->where('is_bundle', '=', $isBundle)->delete();
        $insertData = array();
        foreach ($aBundleId as $index => $bundleId) {
            if (!empty($bundleId)) {
                $aData = array(
                    'fk_brand_id'  => $brandId,
                    'fk_bundle_id'  => $bundleId,
                    'is_bundle'     => $isBundle,
                );
                array_push($insertData, $aData);
            }
        }
        if (!empty($insertData)) {
            DB::table('xref_brand_bundle')->insert($insertData);
        }
    }
    
    public function getBrandDetails($url) {
        $data = array();
        try {
            $data = DB::table('brand')
                    ->select('brand.*')
                    ->where('brand.url_path', '=', $url)
            ->where('brand.active', '=', 1);
            $data = $data->first();
        } catch (Exception $ex) {
            Log::error(__METHOD__ . $ex->getMessage());
        }
        return $data;
        
    }

    /**
     * Return keyword as required for JS.
     * 
     * @param Int $id
     * @return array
     */
    public function getProductsForToken($brandId) {
        $blogData = array();
        try {
            $blogData = DB::table('products')
                    ->addSelect('products.id AS id')
                    ->addSelect('products.name AS name')
                    ->addSelect('products.description AS description')
                ->join('xref_brand_product', 'xref_brand_product.fk_product_id', '=', 'products.id')
                ->join('brand', 'brand.id', '=', 'xref_brand_product.fk_brand_id') 
                ->where('brand.id', '=', $brandId)
                ->where('products.deleted_at', '=', NULL)
                ->orderBy('xref_brand_product.sort_order')
                ->get();
        } catch (Exception $ex) {
            Log::error(__METHOD__ . $ex->getMessage());
        }
        return $blogData;
    }

    /**
     * Return bundle / keyword as required for JS.
     * 
     * @param Int $id
     * @return array
     */
    public function getBundlesForToken($brandId, $isBundle) {
        $blogData = array();
        try {
            $blogData = DB::table('bundles')
                    ->addSelect('bundles.id AS id')
                    ->addSelect('bundles.name AS name')
                    ->addSelect('bundles.description AS description')
                ->join('xref_brand_bundle', 'xref_brand_bundle.fk_bundle_id', '=', 'bundles.id')
                ->join('brand', 'brand.id', '=', 'xref_brand_bundle.fk_brand_id') 
                ->where('brand.id', '=', $brandId)
                ->where('bundles.deleted_at', '=', NULL)
                ->where('xref_brand_bundle.is_bundle', '=', $isBundle)
                ->get();
        } catch (Exception $ex) {
            Log::error(__METHOD__ . $ex->getMessage());
        }
        return $blogData;
    }

}
