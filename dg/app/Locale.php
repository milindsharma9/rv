<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Support\Facades\DB;
use Exception;
use Illuminate\Support\Facades\Log;

class Locale extends Model
{
    //
    /**
     * Set table name.
     * @var type 
     */
    protected $table = "locale";
    //only allow the following items to be mass-assigned to our model
    protected $fillable = ['title', 'description', 'image',
        'url_path', 'sub_title', 'meta_title', 'meta_keywords', 'meta_description', 'active'];
    
    /**
     * Return validation rules for Events.
     * 
     * @return array
     */
    public function validationRules($id = NULL) {
        $rules = array(
            'title'         => 'required',
            'description'   => 'required',
            'sub_title'     => 'required',
            'keywords'      => 'required',
        );
        if ($id != NULL) {
            $rules = array_merge($rules, ['url_path' => 'required|unique:locale,url_path,' . $id . ',id']);
        } else {
            $rules = array_merge($rules, ['url_path' => 'required|unique:locale,url_path,NULL,id',
                'image' => 'required']);
        }
        return $rules;
    }

    /**
     * Method to update Locale Keywords mapping
     *
     * @param int $localeId Primary Id of locale
     * @param array $aKeyWordId
     */
    public static function saveLocaleKeywordMapping($localeId, array $aKeyWordId) {
        DB::table('xref_locale_keyword')->where('fk_locale_id', '=', $localeId)->delete();
        $insertData = array();
        foreach ($aKeyWordId as $index => $keywordId) {
            if (!empty($keywordId)) {
                $aData = array(
                    'fk_locale_id'  => $localeId,
                    'fk_keyword_id' => $keywordId,
                );
                array_push($insertData, $aData);
            }
        }
        if (!empty($insertData)) {
            DB::table('xref_locale_keyword')->insert($insertData);
        }
        
    }

    /**
     * Method to update Locale Product mapping
     *
     * @param int $localeId Primary Id of locale
     * @param array $aProductId
     */
    public static function saveLocaleProductMapping($localeId, array $aProductId) {
        DB::table('xref_locale_product')->where('fk_locale_id', '=', $localeId)->delete();
        $insertData = array();
        foreach ($aProductId as $index => $productId) {
            if (!empty($productId)) {
                $sortOrder = $index + 1; // as index starts from 0.
                $aData = array(
                    'fk_locale_id'  => $localeId,
                    'fk_product_id' => $productId,
                    'sort_order'    => $sortOrder,
                );
                array_push($insertData, $aData);
            }
        }
        if (!empty($insertData)) {
            DB::table('xref_locale_product')->insert($insertData);
        }
        
    }

    /**
     * Method to update Locale Bundle / Recipe mapping
     *
     * @param int $localeId Primary Id of locale
     * @param array $aBundleId
     * @param int $isBundle
     */
    public static function saveLocaleBundleMapping($localeId, array $aBundleId, $isBundle = 0) {
        DB::table('xref_locale_bundle')->where('fk_locale_id', '=', $localeId)->where('is_bundle', '=', $isBundle)->delete();
        $insertData = array();
        foreach ($aBundleId as $index => $bundleId) {
            if (!empty($bundleId)) {
                $aData = array(
                    'fk_locale_id'  => $localeId,
                    'fk_bundle_id'  => $bundleId,
                    'is_bundle'     => $isBundle,
                );
                array_push($insertData, $aData);
            }
        }
        if (!empty($insertData)) {
            DB::table('xref_locale_bundle')->insert($insertData);
        }
    }
    
    public function getLocaleDetails($url) {
        $data = array();
        try {
            $data = DB::table('locale')
                    ->select('locale.*')
                    ->addSelect('keyword.machine_name')
                    ->where('locale.url_path', '=', $url)
                    ->leftjoin('xref_locale_keyword', 'xref_locale_keyword.fk_locale_id', '=', 'locale.id')
                    ->leftjoin('keyword', 'keyword.id', '=', 'xref_locale_keyword.fk_keyword_id')
            ->where('locale.active', '=', 1);
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
    public function getProductsForToken($localeId) {
        $blogData = array();
        try {
            $blogData = DB::table('products')
                    ->addSelect('products.id AS id')
                    ->addSelect('products.name AS name')
                    ->addSelect('products.description AS description')
                ->join('xref_locale_product', 'xref_locale_product.fk_product_id', '=', 'products.id')
                ->join('locale', 'locale.id', '=', 'xref_locale_product.fk_locale_id') 
                ->where('locale.id', '=', $localeId)
                ->where('products.deleted_at', '=', NULL)
                ->orderBy('xref_locale_product.sort_order')
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
    public function getBundlesForToken($localeId, $isBundle) {
        $blogData = array();
        try {
            $blogData = DB::table('bundles')
                    ->addSelect('bundles.id AS id')
                    ->addSelect('bundles.name AS name')
                    ->addSelect('bundles.description AS description')
                ->join('xref_locale_bundle', 'xref_locale_bundle.fk_bundle_id', '=', 'bundles.id')
                ->join('locale', 'locale.id', '=', 'xref_locale_bundle.fk_locale_id') 
                ->where('locale.id', '=', $localeId)
                ->where('bundles.deleted_at', '=', NULL)
                ->where('xref_locale_bundle.is_bundle', '=', $isBundle)
                ->get();
        } catch (Exception $ex) {
            Log::error(__METHOD__ . $ex->getMessage());
        }
        return $blogData;
    }

}
