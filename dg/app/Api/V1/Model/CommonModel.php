<?php

namespace App\Api\V1\Model;

use App\Occasion;
use App\Event;
use App\User;
use JWTAuth;
use App\Product;
use App\Category;
use App\Http\Helper\CommonHelper;
use App\ValidPostcode;
use App\SalesOrder;

class CommonModel {

    /**
     *
     * @var App\Product
     */
    private $_productModel = NULL;

    /**
     * Function to Instatntiate Product Model.
     *
     * @return object App\Product Model
     *
     */
    private function getProductModel() {
        if ($this->_productModel == NULL) {
            $this->_productModel = new Product();
        }
        return $this->_productModel;
    }

    /**
     * Method to return list of All Occasions
     *
     * @return array $aOccasionResponse List Of Occasions and their child
     *
     */
    public function getOccasions() {
        $occasionModel      = new Occasion();
        $aOccasions         = $occasionModel->getOccassionTree();
        $aOccasionResponse  = array();
        // Format Response For API
        foreach ($aOccasions as $pOccasionId => $aOccasionDetail) {
            $aPrimaryOccasion                     = array();
            $aPrimaryOccasion['id']               = $aOccasionDetail['id'];
            $aPrimaryOccasion['name']             = $aOccasionDetail['name'];
            $aPrimaryOccasion['image']            = $aOccasionDetail['image'];
            $aPrimaryOccasion['image_logo']       = $aOccasionDetail['image_logo'];
            $aPrimaryOccasion['image_banner']     = $aOccasionDetail['image_banner'];
            $aPrimaryOccasion['is_banner']        = $aOccasionDetail['is_banner'];
            $aPrimaryOccasion['subOccasions']     = array();
            $aSubOccasion                         = array();
            foreach ($aOccasionDetail['subOccasions'] as $subOccasionId => $aSubOccasionDetail) {
                $subOccasionName = $aSubOccasionDetail['name'];
                $subOccasionId   = $aSubOccasionDetail['id'];
                $aSubOccasion    = array(
                    'id'        => $subOccasionId,
                    'name'      => $subOccasionName,
                );
                $aPrimaryOccasion['subOccasions'][]   = $aSubOccasion;
            }
            $aOccasionResponse[] = $aPrimaryOccasion;
        }
        return $aOccasionResponse;
    }

    /**
     * Method to return list of All Creations
     *
     * @return array $aEventResponse List Of Primary Creations and their sub child
     *
     */
    public function getCreations() {
        $eventModel         = new Event();
        $aEvents            = $eventModel->getEventTree();
        $aEventResponse     = array();
        // Format Response For API
        foreach ($aEvents as $pEventId => $aEventDetail) {
            $aPrimaryEvent                     = array();
            $aPrimaryEvent['id']               = $aEventDetail['id'];
            $aPrimaryEvent['name']             = $aEventDetail['name'];
            $aPrimaryEvent['image']            = $aEventDetail['image'];
            $aPrimaryEvent['image_logo']       = $aEventDetail['image_logo'];
            $aPrimaryEvent['image_banner']     = $aEventDetail['image_banner'];
            $aPrimaryEvent['subEvents']        = array();
            $aSubEvent                         = array();
            foreach ($aEventDetail['subEvents'] as $subEventId => $aSubEventDetail) {
                $subEventName = $aSubEventDetail['name'];
                $subEventId   = $aSubEventDetail['id'];
                $aSubEvent    = array(
                    'id'        => $subEventId,
                    'name'      => $subEventName,
                );
                $aPrimaryEvent['subEvents'][]   = $aSubEvent;
            }
            $aEventResponse[] = $aPrimaryEvent;
        }
        return $aEventResponse;
    }

    /**
     * Method to Register through API
     *
     * @param array $aRegisterData User Data Required for registration
     *
     * @return array $registerResponse User Details
     *
     */
    public function register($aRegisterData) {
        $userModel              = new User();
        $registerResponse       = $userModel->register($aRegisterData);
        return $registerResponse;
    }
    
    /**
     * Generate API token on successful login.
     * @param array $param containing email & password.
     * @return array
     */
    private function _generateApiToken(array $param) {
        $response = ['status' => FALSE, 'token' => '', 'message' => ''];
        $token = JWTAuth::attempt($param);
        if ($token) {
            $response = ['status' => TRUE, 'token' => $token, 'message' => 'Token Created Successfully'];
        } else {
            $response['message'] = 'Invalid Credentials';
        }
        return $response;
    }
    
    /**
     * Fetch user details on the basis of token.
     * @param string $token
     * @return array
     */
    public function getUserDetailsByToken($token) {
        $response = ['status' => FALSE, 'data' => '', 'message' => ''];
        $details = JWTAuth::toUser($token);
        if ($details) {
            $response = ['status' => TRUE, 'data' => $details, 'message' => 'Data fetched Successfully'];
        }
        return $response;
    }

    /**
     * Method to return list of Secondary Occasions
     *
     * @return array $subOccasions List Of Secondary Occasions
     *
     */
    public function getSubOccasions($primaryOccasionId) {
        $occasionModel      = new Occasion();
        $subOccasions       = $occasionModel->getSubOccasions($primaryOccasionId);
        return $subOccasions;
    }

    /**
     * Method to return list of Secondary Creations
     *
     * @return array $subEvents List Of Secondary Creations
     *
     */
    public function getSubCreations($primaryEventId) {
        $eventModel         = new Event();
        $subEvents          = $eventModel->getSubEvents($primaryEventId);
        return $subEvents;
    }
    
    /**
     * Generate API token on successful login 
     * & get all user details.
     * 
     * @param array $param containing email & password.
     * @return array
     */
    public function getUserdetailsWithToken(array $param) {
        $response = ['status' => FALSE, 'data' => '', 'message' => ''];
        $tokenDetails = $this->_generateApiToken($param);
        if ($tokenDetails['status']) {
            $userDetails =  $this->getUserDetailsByToken($tokenDetails['token']);
            $userDetails['token'] = $tokenDetails['token'];
            return $userDetails;
        }
        return $response;
    }
    
    /**
     * Return current user id by token
     * 
     * @param type $token
     * @return type
     */
    public function getUserIdByToken($token) {
        $response = ['status' => FALSE, 'id' => '', 'message' => ''];
        $details = JWTAuth::toUser($token);
        if ($details) {
            $response = ['status' => TRUE, 'id' => $details->id, 'message' => 'Data fetched Successfully'];
        }
        return $response;
    }

    /**
     * Method to return Products & Bundles associated with Event / Creations
     *
     * @return array $response Event Product Data
     *
     */
    public function getCreationsProducts($eventId) {
        $nearCheck          = CommonHelper::checkForNearProductsAPI();
        $prodMapping        = $this->getProductModel()->getProductEvents($eventId, $fetchParentDetail = TRUE, $nearCheck);
        $productsEvents     = $prodMapping['products'];
        $parentDetails      = $prodMapping['parentDetails'];
        $bundleMapping      = $this->getProductModel()->getProductBundles($eventId, $nearCheck);
        $products           = array_merge($productsEvents, $bundleMapping);
        $response = array(
            'products'      => $products,
            'parentDetails' => $parentDetails
        );
        return $response;
    }

    /**
     * Method to return Products & Bundles associated with Occasions
     *
     * @return array $response Occasions Product Data
     *
     */
    public function getOccasionsProducts($occassionId) {
        $nearCheck          = CommonHelper::checkForNearProductsAPI();
        $prodMapping        = $this->getProductModel()->getProductOccasions($occassionId, $fetchParentDetail = TRUE, $nearCheck);
        $productsOccasions  = $prodMapping['products'];
        $parentDetails      = $prodMapping['parentDetails'];
        $bundleMapping      = $this->getProductModel()->getProductOccasionBundles($occassionId, $nearCheck);
        $products           = array_merge($productsOccasions, $bundleMapping);
        $response = array(
            'products'      => $products,
            'parentDetails' => $parentDetails
        );
        return $response;
    }
    
    /**
     * Method to return Product Details. RelatedOccasion & Products.
     * 
     * @param Int $productId
     * @return array $response Detailed Product Meta & related details
     */
    public function getProductDetail($productId) {
        $response = ['status' => FALSE, 'response' => '', 'message' => trans('messages.product_not_found')];
        $nearCheck          = CommonHelper::checkForNearProductsAPI();
        $product            = $this->getProductModel()->getProductDetails($productId,  $isMultiple = false, $nearCheck);
        if(empty($product))
            return $response;
        $relatedOccasion    = $this->getProductModel()->getRelatedOccasionProduct($productId);
        $relatedProducts    = $this->getProductModel()->getRelatedProducts($productId, $isBundle = false, $nearCheck);
        //
        $product->energy    = $product->energy . " kcal";
        $product->fat       = $product->fat . " g";
        $product->sat_fat   = $product->sat_fat . " g";
        $product->sugar     = $product->sugar . " g";
        $product->salt      = $product->salt . " g";
        //
        // Adding Calculated % value for API
        $product->energy_per    = CommonHelper::getMacroDetails('energy', $product->energy);
        $product->fat_per       = CommonHelper::getMacroDetails('fat', $product->fat);
        $product->sat_fat_per   = CommonHelper::getMacroDetails('sat_fat', $product->sat_fat);
        $product->sugar_per     = CommonHelper::getMacroDetails('sugar', $product->sugar);
        $product->salt_per      = CommonHelper::getMacroDetails('salt', $product->salt);
        $aRelatedProducts       = array();
        foreach ($relatedProducts as $key => $products) {
            $aRelatedProducts[] = $products;
        }
        $responseData = array(
            'product_detail'    => $product,
            'related_occasion'  => $relatedOccasion,
            'related_products'  => $aRelatedProducts
        );
        $response = ['status' => TRUE, 'response' => $responseData, 'message' => trans('messages.product_found')];
        return $response;
    }
    
    /**
     * Method to return Bundle Details. RelatedOccasion & Products.
     * 
     * @param Int $bundleId
     * @return array $response Detailed Bundle Meta & related details
     */
    public function getBundleDetail($bundleId) {
        $response = ['status' => FALSE, 'response' => '', 'message' => trans('messages.bundle_not_found')];
        $nearCheck          = CommonHelper::checkForNearProductsAPI();
        $bundle            = $this->getProductModel()->getBundleDetail($bundleId, $nearCheck);
        if(empty($bundle))
            return $response;
        $relatedOccasion    = $this->getProductModel()->getRelatedOccasionProduct($bundleId, TRUE);
        $relatedProducts    = $this->getProductModel()->getRelatedProducts($bundleId, TRUE, $nearCheck);
        $aRelatedProducts       = array();
        foreach ($relatedProducts as $key => $products) {
            $aRelatedProducts[] = $products;
        }
        $responseData = array(
            'product_detail'    => $bundle,
            'related_occasion'  => $relatedOccasion,
            'related_products'  => $aRelatedProducts
        );
        $response = ['status' => TRUE, 'response' => $responseData, 'message' => trans('messages.bundle_found')];
        return $response;
    }
    
    
    /**
     * Method to return order listing Details.
     * 
     * @return array $response order listing details
     */
    public function getOrderList($userId) {
        $response = ['status' => FALSE, 'response' => '', 'message' => trans('messages.order_not_found')];
        $history = User::getUserOrders($userId);
        if (!empty($history)) {
            $response = ['status' => TRUE, 'response' => $history, 'message' => trans('messages.order_found')];
        }
        return $response;
    }

    /**
     * Method to return list of category tree
     *
     * @return array $aCatResponse category and their subcategory
     *
     */
    public function getCategoryTree() {
        $categoryModel      = new Category();
        $aCatTree           = $categoryModel->getCategoryTree();
        $aCatResponse       = array();
        // Format Response For API
        foreach ($aCatTree['categories'] as $catId => $aCatDetail) {
            $aPrimaryCat                     = array();
            $aPrimaryCat['id']               = $aCatDetail['id'];
            $aPrimaryCat['name']             = $aCatDetail['name'];
            $aPrimaryCat['image']            = $aCatDetail['image'];
            $aPrimaryCat['subCategory']      = array();
            $aSubCat                         = array();
            foreach ($aCatDetail['subCategory'] as $subCatId => $aSubCatDetail) {
                $subCatName = $aSubCatDetail['name'];
                $subCatId   = $aSubCatDetail['id'];
                $aSubCat    = array(
                    'id'        => $subCatId,
                    'name'      => $subCatName,
                    'subSubCat' => array(),
                );
                $aSubSubCat = array();
                foreach ($aSubCatDetail['subSubCat'] as $subSubCatId => $aSubSubCatDetail) {
                    $aSubSubCat[] = $aSubSubCatDetail;
                }
                $aSubCat['subSubCat']           = $aSubSubCat;
                $aPrimaryCat['subCategory'][]   = $aSubCat;
            }
            $aCatResponse[] = $aPrimaryCat;
        }
        return $aCatResponse;
    }

    /**
     * Method to return Products associated with SubCategory
     *
     * @return array $getProducts
     *
     */
    public function getSubCatProducts($subCatId) {
        $categoryModel      = new Category();
        $aCatTree           = $categoryModel->getCategoryTree();
        $aSubCatTree        = array();
        foreach ($aCatTree['categories'] as $pCatId => $aPCatDetails) {
            if (isset($aPCatDetails['subCategory'][$subCatId])) {
                $aSubCatTree = $aPCatDetails['subCategory'][$subCatId]['subSubCat'];
                break;
            }
        }
        $nearCheck          = CommonHelper::checkForNearProductsAPI();
        $getProducts        = $this->getProductModel()->getSubCatProducts($aSubCatTree, $subCatId, $nearCheck);
        $prodObj = array();
        if ($getProducts['haSubCat']) {
            foreach ($getProducts['products'][0] as $key => $keyDetails) {
                $aSubCatDetail['name']        = $key;
                $aSubCatDetail['products']    = $keyDetails;
                array_push($prodObj, $aSubCatDetail);
            }
       }
       $getProducts['products'] = $prodObj;
       return $getProducts;
    }
    
    /**
     * Method to return valid post code
     *
     * @return array $response
     *
     */
    public function getValidPostcode($postCode) {
        $postCodeData = new \stdClass();
        $response = ['serviceable' => FALSE, 'postCodeDetails' => $postCodeData];
        $postcodeModel = new ValidPostcode();
        $postCodeDetails = $postcodeModel->getPostcodeDetail($postCode);
        if (empty($postCodeDetails)) {
            return $response;
        }
        $postCodeData->postCode = $postCodeDetails['postcode'];
        $postCodeData->lat = $postCodeDetails['lat'];
        $postCodeData->lng = $postCodeDetails['lng'];
        $response = ['serviceable' => TRUE, 'postCodeDetails' => $postCodeData];
        return $response;
    }
    
    /**
     * Method to return array of matching valid post codes
     *
     * @return array $response
     *
     */
    public function getMatchingValidPostcodes($term) {
        $postCodeData = array();
        $response = ['exist' => FALSE, 'postCodeDetails' => $postCodeData];
        $postcodeModel = new ValidPostcode();
        $postCodeDetails = $postcodeModel->getValidPostcodes($term, $exactSearch = false);
        if (empty($postCodeDetails)) {
            return $response;
        }
        $response = ['exist' => TRUE, 'postCodeDetails' => $postCodeDetails];
        return $response;
    }

    /**
     * Method to return searched Products
     *
     * @param string $param
     * @param int $pCatId
     * @return array $matchedProducts
     */
    public function getSearchProducts($searchParam, $pCatId) {
        $aResponse          = array(
            'products' => array(),
            'category' => array(),
        );
        $nearCheck          = CommonHelper::checkForNearProductsAPI();
        if (empty($pCatId)) {
            $matchedProducts    = $this->getProductModel()->searchProducts($searchParam, $nearCheck);
            $categoryModel      = new Category();
            $aCatTree           = $categoryModel->getCategoryTree();
            $aCatResponse       = array();
            // Format Response For API
            foreach ($aCatTree['categories'] as $catId => $aCatDetail) {
                $aPrimaryCat            = array();
                $aPrimaryCat['id']      = $aCatDetail['id'];
                $aPrimaryCat['name']    = $aCatDetail['name'];
                $aCatResponse[]         = $aPrimaryCat;
            }
            $aResponse['category'] = $aCatResponse;
        } else {
            $matchedProducts    = $this->getProductModel()->searchProductsByCategory($pCatId, $searchParam, $nearCheck);
        }
        $aResponse['products'] = $matchedProducts;
        return $aResponse;
    }

    /**
     * Method to return matched searched Products name (autosuggest)
     *
     * @param string $param
     * @return array $matchedProducts
     */
    public function getMatchedProducts($searchParam) {
        $nearCheck          = CommonHelper::checkForNearProductsAPI();
        $matchedProducts    = $this->getProductModel()->getAutoSuggestProducts($searchParam, $nearCheck);
        return $matchedProducts;
    }
  
}