<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Category;
use App\Product;

class SitemapController extends Controller
{
    /**
     * 
     * @return type
     */
    public function index() {        
        $productSiteMap = $this->getProductUrls();
        $categorySiteMap = $this->getCategoryUrls();
        $aSiteMap = array_merge($categorySiteMap, $productSiteMap);
        return response()->view('sitemap.index', [
                    'posts' => $aSiteMap,
                ])->header('Content-Type', 'text/xml');
    }

    /**
     * 
     * @return array
     */
    public function getProductUrls() {
        $productSiteMap = array();
        $products       = Product::all();
        foreach ($products as $product) {
            $aSiteMapDetails = array(
                'url' =>  url('/') .'/productdetail/'. $product->id,
                'changefreq' => 'weekly',
                'priority' => 0.6,
                'lastmod' => $product->updated_at,
            );
            $productSiteMap[] = $aSiteMapDetails;
        }
        return $productSiteMap;
    }

    /**
     * 
     * @return array
     */
    public function getCategoryUrls() {
        $categorySiteMap = array();
        $catModel       = new Category();
        $aCategory      = $catModel->getCategoryTree();
        foreach ($aCategory['categories'] as $catId => $catDetails) {
            foreach ($catDetails['subCategory'] as $subCatId => $subCatDetails) {
                // Sub Category Level URL
                $aSiteMapDetails = array(
                    'url' =>  url('/') .'/products/cat/'. $catId."/subcat/".$subCatId,
                    'changefreq' => 'weekly',
                    'priority' => 0.6,
                    //'lastmod' => '',
                );
                $categorySiteMap[] = $aSiteMapDetails;
            }
            // Category Level URL
            $aSiteMapDetails = array(
                    'url' =>  url('/') .'/products/'. $catId,
                    'changefreq' => 'weekly',
                    'priority' => 0.6,
                    'lastmod' => '',
            );
            $categorySiteMap[] = $aSiteMapDetails;
        }
        return $categorySiteMap;
    }
    
    /**
     * Function to get sitemap.html
     *  
     * @return array
     */
    function getsitemapHtml() {
        $aSiteMap = array();
        $catModel = new Category();
        $aCategory = $catModel->getCategoryTree();
        foreach ($aCategory['categories'] as $catId => $catDetails) {
            $aSiteMap[$catId]['id'] = $catId;
            $aSiteMap[$catId]['name'] = $catDetails['name'];
            foreach ($catDetails['subCategory'] as $subCatId => $subCatDetails) {
                $aSiteMap[$catId]['data'][$subCatId]['id'] = $subCatId;
                $aSiteMap[$catId]['data'][$subCatId]['name'] = $subCatDetails['name'];
                foreach ($subCatDetails['subSubCat'] as $subSubCatId => $subSubCatDetail) {
                    $aSiteMap[$catId]['data'][$subCatId]['data'][$subSubCatId]['id'] = $subSubCatId;
                    $aSiteMap[$catId]['data'][$subCatId]['data'][$subSubCatId]['name'] = $subSubCatDetail['name'];
                    $products = $catModel->getProductDetail($subSubCatDetail['id']);
                    foreach ($products as $key => $value) {
                        $products[$key]->url = url('/') .'/productdetail/'. $products[$key]->id;
                    }
                    $aSiteMap[$catId]['data'][$subCatId]['data'][$subSubCatId]['products'] = $products;
                }
            }
        }
        return view('sitemap', compact('aSiteMap'));
    }

}
