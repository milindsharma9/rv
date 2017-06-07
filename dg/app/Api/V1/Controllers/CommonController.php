<?php

namespace App\Api\V1\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use App\Http\Controllers\Controller;
use App\Api\V1\Response\Response;
use JWTAuth;
use Exception;

class CommonController extends ApiBaseController
{

    /**
     * Method to get Primary Occasion List
     *
     * @package api/CommonController
     * @return Object $serviceResponse 
     */
    public function getOccasions() {
        $response = $this->responseGenerator;
        try {
            $apiModel               = $this->_commonModel;
            $occasions              = $apiModel->getOccasions();
            $data['occasions']      = $occasions;
            $fileSubDir             = 'occasions';
            $data['image_base_url'] = $this->getBaseDirectoryForImages($fileSubDir, FALSE);
            $response->setResponseData($data);
            $response->setResponseMessage(trans('messages.success'));
            $response->setResponseStatus(TRUE);
            $response->setMessage(trans('messages.success'));
            $response->setStatus(TRUE);
        } catch (Exception $ex) {
            $message = trans('messages.common_error');
            $response->setMessage($message);
            $debugTrace = $ex->getMessage() . "|" . $ex->getFile() . "|" . $ex->getLine();
            $this->handleAPIErrorMessages($debugTrace);
        }
        $serviceResponse = $response->getServiceResponse();
        return $serviceResponse;
    }

    /**
     * Method to get Primary Creations List
     *
     * @package api/CommonController
     * @return Object $serviceResponse
     */
    public function getCreations() {
        $response = $this->responseGenerator;
        try {
            $apiModel               = $this->_commonModel;
            $creations              = $apiModel->getCreations();
            $data['creations']      = $creations;
            $fileSubDir             = 'events';
            $data['image_base_url'] = $this->getBaseDirectoryForImages($fileSubDir, FALSE);
            $response->setResponseData($data);
            $response->setResponseMessage(trans('messages.success'));
            $response->setResponseStatus(TRUE);
            $response->setMessage(trans('messages.success'));
            $response->setStatus(TRUE);
        } catch (Exception $ex) {
            $message = trans('messages.common_error');
            $response->setMessage($message);
            $debugTrace = $ex->getMessage() . "|" . $ex->getFile() . "|" . $ex->getLine();
            $this->handleAPIErrorMessages($debugTrace);
        }
        $serviceResponse = $response->getServiceResponse();
        return $serviceResponse;
    }

    /**
     * Method to get Sub Occasions
     * 
     * @package api/CommonController
     * @param Int $occasionId
     * @return Object $serviceResponse
     */
    public function getSubOccasions($occasionId) {
        $response = $this->responseGenerator;
        try {
            $apiModel               = $this->_commonModel;
            if (!empty($occasionId)) {
                $subOccasions              = $apiModel->getSubOccasions($occasionId);
                $data['sub_occasions']         = $subOccasions;
                $response->setResponseData($data);
                $response->setResponseMessage(trans('messages.success'));
                $response->setResponseStatus(TRUE);
                $response->setMessage(trans('messages.success'));
                $response->setStatus(TRUE);
            } else {
                $response->setStatus(FALSE);
                $response->setMessage(trans('messages.api_parameter_missing'));
            }
        } catch (Exception $ex) {
            $message = trans('messages.common_error');
            $response->setMessage($message);
            $debugTrace = $ex->getMessage() . "|" . $ex->getFile() . "|" . $ex->getLine();
            $this->handleAPIErrorMessages($debugTrace);
        }
        $serviceResponse = $response->getServiceResponse();
        return $serviceResponse;
    }

    /**
     * Method to get Sub Creations
     *
     * @package api/CommonController
     * @param Int $creationId
     * @return Object $serviceResponse
     */
    public function getSubCreations($creationId) {
        $response = $this->responseGenerator;
        try {
            $apiModel               = $this->_commonModel;
            if (!empty($creationId)) {
                $subCreations              = $apiModel->getSubCreations($creationId);
                $data['sub_creations']     = $subCreations;
                $response->setResponseData($data);
                $response->setResponseMessage(trans('messages.success'));
                $response->setResponseStatus(TRUE);
                $response->setMessage(trans('messages.success'));
                $response->setStatus(TRUE);
            } else {
                $response->setStatus(FALSE);
                $response->setMessage(trans('messages.api_parameter_missing'));
            }
        } catch (Exception $ex) {
            $message = trans('messages.common_error');
            $response->setMessage($message);
            $debugTrace = $ex->getMessage() . "|" . $ex->getFile() . "|" . $ex->getLine();
            $this->handleAPIErrorMessages($debugTrace);
        }
        $serviceResponse = $response->getServiceResponse();
        return $serviceResponse;
    }

    /**
     * Method to get Products & Bundles Associated with Creation / Event
     *
     * @package api/CommonController
     * @param Int $creationId
     * @return Object $serviceResponse
     */
    public function getCreationsProducts($creationId) {
        $response = $this->responseGenerator;
        try {
            $apiModel               = $this->_commonModel;
            if (!empty($creationId)) {
                $subCreations                   = $apiModel->getCreationsProducts($creationId);
                $data['creations_data']         = $subCreations;
                $fileSubDir                     = 'events';
                $bundleImageDir                 = 'bundles';
                $productImageDir                = 'alchemy/images/product-images';
                $data['image_base_url']         = $this->getBaseDirectoryForImages($fileSubDir, FALSE);
                $data['product_image_base_url'] = $this->getBaseDirectoryForImages($productImageDir, TRUE, FALSE);
                $data['bundle_image_base_url']  = $this->getBaseDirectoryForImages($bundleImageDir);
                $response->setResponseData($data);
                $response->setResponseMessage(trans('messages.success'));
                $response->setResponseStatus(TRUE);
                $response->setMessage(trans('messages.success'));
                $response->setStatus(TRUE);
            } else {
                $response->setStatus(FALSE);
                $response->setMessage(trans('messages.api_parameter_missing'));
            }
        } catch (Exception $ex) {
            $message = trans('messages.common_error');
            $response->setMessage($message);
            $debugTrace = $ex->getMessage() . "|" . $ex->getFile() . "|" . $ex->getLine();
            $this->handleAPIErrorMessages($debugTrace);
        }
        $serviceResponse = $response->getServiceResponse();
        return $serviceResponse;
    }

    /**
     * Method to get Products & Bundles Associated with Occasions
     *
     * @package api/CommonController
     * @param Int $occassionId
     * @return Object $serviceResponse
     */
    public function getOccasionsProducts($occassionId) {
        $response = $this->responseGenerator;
        try {
            $apiModel               = $this->_commonModel;
            if (!empty($occassionId)) {
                $occassionProducts              = $apiModel->getOccasionsProducts($occassionId);
                $data['occasions_data']         = $occassionProducts;
                $fileSubDir                     = 'occasions';
                $bundleImageDir                 = 'bundles';
                $productImageDir                = 'alchemy/images/product-images';
                $data['image_base_url']         = $this->getBaseDirectoryForImages($fileSubDir, FALSE);
                $data['product_image_base_url'] = $this->getBaseDirectoryForImages($productImageDir, TRUE, FALSE);
                $data['bundle_image_base_url']  = $this->getBaseDirectoryForImages($bundleImageDir);
                $response->setResponseData($data);
                $response->setResponseMessage(trans('messages.success'));
                $response->setResponseStatus(TRUE);
                $response->setMessage(trans('messages.success'));
                $response->setStatus(TRUE);
            } else {
                $response->setStatus(FALSE);
                $response->setMessage(trans('messages.api_parameter_missing'));
            }
        } catch (Exception $ex) {
            $message = trans('messages.common_error');
            $response->setMessage($message);
            $debugTrace = $ex->getMessage() . "|" . $ex->getFile() . "|" . $ex->getLine();
            $this->handleAPIErrorMessages($debugTrace);
        }
        $serviceResponse = $response->getServiceResponse();
        return $serviceResponse;
    }
    
    /**
     * Method to getProduct Details. RelatedOccasion & Products
     * 
     * @package api/CommonController
     * @param Int $productId
     * @return Object $serviceResponse
     *
     */
    public function getProductDetail($productId = NULL) {
        $response = $this->responseGenerator;
        try {
            $apiModel = $this->_commonModel;
            if (!empty($productId)) {
                $productDetail = $apiModel->getProductDetail($productId);
                if ($productDetail['status']) {
                    $data['products_data'] = $productDetail['response'];
                    $fileSubDir = 'occasions';
                    $productImageDir = 'alchemy/images/product-images';
                    $data['occasion_image_base_url'] = $this->getBaseDirectoryForImages($fileSubDir, FALSE);
                    $data['product_image_base_url'] = $this->getBaseDirectoryForImages($productImageDir, FALSE, FALSE);
                    $data['product_thumb_image_base_url'] = $this->getBaseDirectoryForImages($productImageDir, TRUE, FALSE);
                    $response->setResponseData($data);
                    $response->setResponseMessage(trans('messages.product_found'));
                    $response->setResponseStatus(TRUE);
                    $response->setMessage(trans('messages.success'));
                    $response->setStatus(TRUE);
                }else{
                    $response->setMessage($productDetail['message']);
                }
            } else {
                $response->setMessage(trans('messages.api_parameter_missing'));
            }
        } catch (Exception $ex) {
            $message = trans('messages.common_error');
            $response->setMessage($message);
            $debugTrace = $ex->getMessage() . "|" . $ex->getFile() . "|" . $ex->getLine();
            $this->handleAPIErrorMessages($debugTrace);
        }
        $serviceResponse = $response->getServiceResponse();
        return $serviceResponse;
    }
    
    /**
     * Method to getBundle Details. RelatedOccasion & Bundles
     * 
     * @package api/CommonController
     * @param Int $bundleId
     * @return Object $serviceResponse
     *
     */
    public function getBundleDetail($bundleId = NULL) {
        $response = $this->responseGenerator;
        try {
            $apiModel = $this->_commonModel;
            if (!empty($bundleId)) {
                $productDetail = $apiModel->getBundleDetail($bundleId);
                if ($productDetail['status']) {
                    $data['products_data'] = $productDetail['response'];
                    if ($productDetail['response']['product_detail']) {
                        $total = 0.00;
                        foreach ($productDetail['response']['product_detail'] as $key => $value) {
                            $total += $value->priceTot;
                        }
                        $data['total_sum'] = $total;
                    }
                    $fileSubDir = 'occasions';
                    $bundleImageDir = 'bundles';
                    $productImageDir = 'alchemy/images/product-images';
                    $data['occasion_image_base_url'] = $this->getBaseDirectoryForImages($fileSubDir, FALSE);
                    $data['product_image_base_url'] = $this->getBaseDirectoryForImages($productImageDir, FALSE, FALSE);
                    $data['product_thumb_image_base_url'] = $this->getBaseDirectoryForImages($productImageDir, TRUE, FALSE);
                    $data['bundle_image_base_url'] = $this->getBaseDirectoryForImages($bundleImageDir, FALSE);
                    $response->setResponseData($data);
                    $response->setResponseMessage(trans('messages.bundle_found'));
                    $response->setResponseStatus(TRUE);
                    $response->setMessage(trans('messages.success'));
                    $response->setStatus(TRUE);
                } else {
                    $response->setMessage($productDetail['message']);
                }
            } else {
                $response->setMessage(trans('messages.api_parameter_missing'));
            }
        } catch (Exception $ex) {
            $message = trans('messages.common_error');
            $response->setMessage($message);
            $debugTrace = $ex->getMessage() . "|" . $ex->getFile() . "|" . $ex->getLine();
            $this->handleAPIErrorMessages($debugTrace);
        }
        $serviceResponse = $response->getServiceResponse();
        return $serviceResponse;
    }

    /**
     * Method to getOrderList Details.
     * 
     * @package api/CommonController
     * @param Request $request | required page
     * @return Object $serviceResponse
     */
    public function getOrderList(Request $request) {
        $response = $this->responseGenerator;
        try {
            if ($request->input('page')) {
                $apiModel = $this->_commonModel;
                $userIdResponse = $apiModel->getUserIdByToken(JWTAuth::getToken());
                if ($userIdResponse['status']) {
                    $productImageDir = 'alchemy/images/product-images';
                    $data = $apiModel->getOrderList($userIdResponse['id']);
                    $data['product_image_base_url'] = $this->getBaseDirectoryForImages($productImageDir, FALSE, FALSE);
                    $data['product_thumb_image_base_url'] = $this->getBaseDirectoryForImages($productImageDir, TRUE, FALSE);
                    $response->setResponseData($data);
                    $response->setResponseMessage(trans('messages.order_found'));
                    $response->setResponseStatus(TRUE);
                    $response->setMessage(trans('messages.success'));
                    $response->setStatus(TRUE);
                } else {
                    $response->setResponseStatus(FALSE);
                    $response->setResponseMessage(trans('messages.user_invalid'));
                }
            } else {
                $response->setMessage(trans('messages.api_parameter_missing'));
            }
        } catch (Exception $ex) {
            $message = trans('messages.common_error');
            $response->setMessage($message);
            $debugTrace = $ex->getMessage() . "|" . $ex->getFile() . "|" . $ex->getLine();
            $this->handleAPIErrorMessages($debugTrace);
        }
        $serviceResponse = $response->getServiceResponse();
        return $serviceResponse;
    }

    /**
     * Method to get Primary Creations List
     *
     * @package api/CommonController
     * @return Object $serviceResponse
     */
    public function getCategoryListing() {
        $response = $this->responseGenerator;
        try {
            $apiModel               = $this->_commonModel;
            $aCatTree               = $apiModel->getCategoryTree();
            $data['categories']     = $aCatTree;
            $fileSubDir             = 'categories';
            $data['image_base_url'] = $this->getBaseDirectoryForImages($fileSubDir, FALSE);
            $response->setResponseData($data);
            $response->setResponseMessage(trans('messages.success'));
            $response->setResponseStatus(TRUE);
            $response->setMessage(trans('messages.success'));
            $response->setStatus(TRUE);
        } catch (Exception $ex) {
            $message = trans('messages.common_error');
            $response->setMessage($message);
            $debugTrace = $ex->getMessage() . "|" . $ex->getFile() . "|" . $ex->getLine();
            $this->handleAPIErrorMessages($debugTrace);
        }
        $serviceResponse = $response->getServiceResponse();
        return $serviceResponse;
    }

    /**
     * Method to fetch products associated with Subcategory Group BY SubSubCategory (if present)
     *
     * @param Int $subCatId
     * @return Object $serviceResponse
     *
     */
    public function getSubCatProducts($subCatId) {
        $response = $this->responseGenerator;
        try {
            $apiModel               = $this->_commonModel;
            if (!empty($subCatId)) {
                $subCatProductsDetails          = $apiModel->getSubCatProducts($subCatId);
                $data                           = $subCatProductsDetails;
                $response->setResponseData($data);
                $response->setResponseMessage(trans('messages.success'));
                $response->setResponseStatus(TRUE);
                $response->setMessage(trans('messages.success'));
                $response->setStatus(TRUE);
            } else {
                $response->setStatus(FALSE);
                $response->setMessage(trans('messages.api_parameter_missing'));
            }
        } catch (Exception $ex) {
            $message = trans('messages.common_error');
            $response->setMessage($message);
            $debugTrace = $ex->getMessage() . "|" . $ex->getFile() . "|" . $ex->getLine();
            $this->handleAPIErrorMessages($debugTrace);
        }
        $serviceResponse = $response->getServiceResponse();
        return $serviceResponse;
    }

    /**
     * Method to get Banners Image
     *
     * @return Object $serviceResponse
     */
    public function getBanners() {
        $response = $this->responseGenerator;
        try {
            $aBanner = array();
            /*for ($i = 2; $i <=4; $i++) {
                $ext = ".png";
                if ($i == 4) {
                    $ext = ".jpg";
                }*/
                array_push($aBanner,
                    array(
                        'home-banner-4.jpg',
                        //'sort_order'    => $i,
                    )
                );
            //}
            $aBanner = array('home-banner-4.jpg');
            $data['banners']                    = $aBanner;
            $bannerImageDir                     = 'alchemy/images';
            $data['image_base_url']             = $this->getBaseDirectoryForImages($bannerImageDir, FALSE, FALSE);
            $response->setResponseData($data);
            $response->setResponseMessage(trans('messages.success'));
            $response->setResponseStatus(TRUE);
            $response->setMessage(trans('messages.success'));
            $response->setStatus(TRUE);
        } catch (Exception $ex) {
            $message = trans('messages.common_error');
            $response->setMessage($message);
            $debugTrace = $ex->getMessage() . "|" . $ex->getFile() . "|" . $ex->getLine();
            $this->handleAPIErrorMessages($debugTrace);
        }
        $serviceResponse = $response->getServiceResponse();
        return $serviceResponse;
    }    
    
    /**
     * Method to validate Postcode 
     * 
     * @package api/CommonController
     * @param $postCode
     * @return Object $serviceResponse
     *
     */
    public function validatePostcode($postCode = NULL) {
        $response = $this->responseGenerator;
        try {
            if (!empty($postCode)) {
                $apiModel           = $this->_commonModel;
                $postCodeDetails    = $apiModel->getValidPostcode($postCode);
                $response->setResponseData($postCodeDetails);
                if ($postCodeDetails['serviceable']) {
                    $response->setResponseMessage(trans('messages.success'));
                    $response->setResponseStatus(TRUE);
                }
                else {
                    $response->setResponseMessage(trans('messages.postcode_error_not_serviceable'));
                }
                $response->setMessage(trans('messages.success'));
                $response->setStatus(TRUE);
            }
            else {
                $response->setMessage(trans('messages.api_parameter_missing'));
            }
        }
        catch (Exception $ex) {
            $message = trans('messages.common_error');
            $response->setMessage($message);
            $debugTrace = $ex->getMessage() . "|" . $ex->getFile() . "|" . $ex->getLine();
            $this->handleAPIErrorMessages($debugTrace);
        }
        $serviceResponse = $response->getServiceResponse();
        return $serviceResponse;
    }

    /**
     * Method to validate matching Postcodes 
     * 
     * @package api/CommonController
     * @param $term
     * @return Object $serviceResponse
     *
     */
    public function getMatchingValidPostcodes($term = NULL) {
        $response = $this->responseGenerator;
        try {
            if (!empty($term)) {
                $apiModel = $this->_commonModel;
                $postCodeDetails =  $apiModel->getMatchingValidPostcodes($term);                
                     $response->setResponseData($postCodeDetails);
                if ($postCodeDetails['exist']) {
                    $response->setResponseMessage(trans('messages.success'));
                    $response->setResponseStatus(TRUE);
                    $response->setMessage(trans('messages.success'));
                    $response->setStatus(TRUE);
                }
                else {
                    $response->setResponseMessage(trans('messages.postcode_not_found'));
                }
            }
            else {
                $response->setMessage(trans('messages.api_parameter_missing'));
            }
        }
        catch (Exception $ex) {
            $message = trans('messages.common_error');
            $response->setMessage($message);
            $debugTrace = $ex->getMessage() . "|" . $ex->getFile() . "|" . $ex->getLine();
            $this->handleAPIErrorMessages($debugTrace);
        }
        $serviceResponse = $response->getServiceResponse();
        return $serviceResponse;
    }
    
    /**
     * Method to get footer links
     * 
     * @package api/CommonController
     * 
     * @return Object $serviceResponse
     *
     */
    public function getFooterLinks() {
        $response = $this->responseGenerator;
        try {
            $footerLinks = array();
            $footerLinks['twitter'] = config('appConstants.twitter');
            $footerLinks['facebook'] = config('appConstants.facebook');
            $footerLinks['instagram'] = config('appConstants.instagram');
            $footerLinks['pinterest'] = config('appConstants.pinterest');
            $footerLinks['mailto'] = config('appConstants.mailto');
            $footerLinks['tel'] = config('appConstants.tel');
            $response->setResponseData($footerLinks);
            $response->setResponseMessage(trans('messages.success'));
            $response->setResponseStatus(TRUE);
            $response->setMessage(trans('messages.success'));
            $response->setStatus(TRUE);
        }
        catch (Exception $ex) {
            $message = trans('messages.common_error');
            $response->setMessage($message);
            $debugTrace = $ex->getMessage() . "|" . $ex->getFile() . "|" . $ex->getLine();
            $this->handleAPIErrorMessages($debugTrace);
        }
        $serviceResponse = $response->getServiceResponse();
        return $serviceResponse;
    }

    /**
     * Method to return searched Products
     *
     * @param string $param
     * @param int $pCatId
     * @return Object $serviceResponse
     */
    public function getSearchProducts($searchParam = NULL, $pCatId = NULL) {
        $response = $this->responseGenerator;
        try {
            if (!empty($searchParam)) {
                $apiModel           = $this->_commonModel;
                $searchResult       =  $apiModel->getSearchProducts($searchParam, $pCatId);
                $data['products']   =  $searchResult['products'];
                if (!empty($searchResult['category'])) {
                    $data['category']   =  $searchResult['category'];
                }
                $productImageDir = 'alchemy/images/product-images';
                $data['product_thumb_image_base_url'] = $this->getBaseDirectoryForImages($productImageDir, TRUE, FALSE);
                $response->setResponseData($data);
                $response->setResponseMessage(trans('messages.success'));
                $response->setResponseStatus(TRUE);
                $response->setMessage(trans('messages.success'));
                $response->setStatus(TRUE);
            } else {
                $response->setMessage(trans('messages.api_parameter_missing'));
            }
        }
        catch (Exception $ex) {
            $message = trans('messages.common_error');
            $response->setMessage($message);
            $debugTrace = $ex->getMessage() . "|" . $ex->getFile() . "|" . $ex->getLine();
            $this->handleAPIErrorMessages($debugTrace);
        }
        $serviceResponse = $response->getServiceResponse();
        return $serviceResponse;
    }

    /**
     * Method to return matched searched Products name (autosuggest)
     *
     * @param string $param
     * @param int $pCatId
     * @return Object $serviceResponse
     */
    public function getMatchedProducts($searchParam = NULL) {
        $response = $this->responseGenerator;
        try {
            if (!empty($searchParam)) {
                $apiModel           = $this->_commonModel;
                $matchedProducts    =  $apiModel->getMatchedProducts($searchParam);
                $data['products']   =  $matchedProducts;
                $response->setResponseData($data);
                $response->setResponseMessage(trans('messages.success'));
                $response->setResponseStatus(TRUE);
                $response->setMessage(trans('messages.success'));
                $response->setStatus(TRUE);
            } else {
                $response->setMessage(trans('messages.api_parameter_missing'));
            }
        }
        catch (Exception $ex) {
            $message = trans('messages.common_error');
            $response->setMessage($message);
            $debugTrace = $ex->getMessage() . "|" . $ex->getFile() . "|" . $ex->getLine();
            $this->handleAPIErrorMessages($debugTrace);
        }
        $serviceResponse = $response->getServiceResponse();
        return $serviceResponse;
    }

}
