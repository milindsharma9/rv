<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests;
use App\Category;
use App\Product;
use App\Blog;

class FeedsController extends Controller {

    
    /**
     * 
     * @return type
     */
    public function index() {
        $categoryDetails = $this->getValidSubCatDetails();
        $categoryId = array_keys($categoryDetails);
        $productModel = new Product();
        $products = $productModel->getProductDetailsByCategory($categoryId);
        $productAvailable = $productModel->getInStockProducts();
        return response()->view('feeds.index', [
                    'products' => $products,
                    'categoryData' => $categoryDetails,
                    'productAvailable' => $productAvailable,
                ])->header('Content-Type', 'text/xml');
    }

    /**
     * 
     * @return array
     */
    public function getValidSubCatDetails() {
        $categorySiteMap = array();
        $catModel = new Category();
        $aCategory = $catModel->getCategoryTree();
        foreach ($aCategory['categories'] as $catId => $catDetails) {
            foreach ($catDetails['subCategory'] as $subCatId => $subCatDetails) {
                $googleIdArray = config('google_feed_map.category_map');
                // Sub Category Level URL
                if(in_array($subCatId, config('google_feed_map.not_include')))
                    continue;
                if (empty($subCatDetails['subSubCat'])) {
                    $validCatDetails = array(
                        'id' => $subCatId,
                        'google_id' => isset($googleIdArray[$subCatId]) ? $googleIdArray[$subCatId] : 0,
                        'name' => $catDetails['name'] . " > " . $subCatDetails['name'],
                    );
                    $categorySiteMap[$subCatId] = $validCatDetails;
                } else {
                    foreach ($subCatDetails['subSubCat'] as $subSubCat => $subSubDetails) {
                        if(in_array($subSubCat, config('google_feed_map.not_include')))
                            continue;
                        $validCatDetails = array(
                            'id' => $subSubCat,
                            'google_id' => isset($googleIdArray[$subSubCat]) ? $googleIdArray[$subSubCat] : 0,
                            'name' => $catDetails['name'] . " > " . $subCatDetails['name'] . " > " . $subSubDetails['name'],
                        );
                        $categorySiteMap[$subSubCat] = $validCatDetails;
                    }
                }
            }
        }
        return $categorySiteMap;
    }

    /**
     * 
     * @return type
     */
    public function createBlogFeed() {
        $blogModel   = new Blog();
        $type        = config('blog.type_blog'); 

        $content     = $blogModel->getBlogDataForFeeds($type);
        $blogsData['blog']  = $content;
        $type           = config('blog.type_place'); 
        $content    = $blogModel->getBlogDataForFeeds($type);
        $blogsData['place']  = $content;
        $type           = config('blog.type_event'); 
        $content    = $blogModel->getBlogDataForFeeds($type);

        $blogsData['event']  = $content;
       // print_r($blogsData);
        //die();
        return response()->view('feeds.blog', [
                    'blogsData' => $blogsData,
                ])->header('Content-Type', 'text/xml');
    }

}
