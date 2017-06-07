<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use App\Http\Helper\CommonHelper;
use Exception;
use Illuminate\Support\Facades\Cache;

class ProductImage extends Model
{
    const MAX_UPLOAD_LIMIT = 4;
    
   /**
     *
     * @var table name
     */
    protected $table = "product_images";
    
    //only allow the following items to be mass-assigned to our model
    /**
     *
     * @var Allow write on db table columns 
     */
    protected $fillable = ['fk_product_id', 'image', 'primary', 'is_thumb', 'created_at', 'updated_at'];

    /**
     * 
     * @param int $productId
     * @return array
     */
    public function getProductImages($productId) {
        $pImages = ProductImage::where('fk_product_id', '=', $productId)->where('is_thumb', '=', 0)->get();
        return $pImages;
    }
    
    /**
     * Method to fetch product Thumb Image
     * 
     * @param int $productId
     * @return array
     */
    public function getProductThumbImage($productId) {
        $pImages = ProductImage::where('fk_product_id', '=', $productId)->where('is_thumb', '=', 1)->first();
        return $pImages;
    }

    /**
     * 
     * @param int $productId
     * @param int $imageId
     * @return array
     */
    public function setProductPrimaryImage($productId, $imageId) {
        $response = array(
            'status' => false,
            'message' => '',
        );
        try {
            $aUpdate = array(
                'primary' => 0,
            );
            $resetSql = ProductImage::where('fk_product_id', '=', $productId)->update($aUpdate);
            $aUpdateAttribute = array(
                'primary' => 1,
            );
            $updateSql = ProductImage::where('id', '=', $imageId)
                    ->where('fk_product_id', '=', $productId)
                    ->update($aUpdateAttribute);
            $response['status'] = true;
        } catch (Exception $ex) {
            $response['message'] = $ex->getMessage();
        }
        return $response;
    }

    /**
     * 
     * @param int $productId
     * @param string $image
     * @param int $isPrimary
     * @return array
     */
    public function insertProductImage($productId, $image, $isPrimary = 0, $isThumb = 0) {
        $response = array(
            'status' => false,
            'message' => '',
        );
        try {
            $aInsertData = array(
                'fk_product_id' => $productId,
                'is_thumb'      => $isThumb,
                'image'         => $image,
                'primary'       => $isPrimary,
                'created_at'    => CommonHelper::getCurrentDateTime(),
            );
            ProductImage::create($aInsertData);
            $response['status'] = true;
        } catch (Exception $ex) {
            $response['message'] = $ex->getMessage();
        }
        return $response;
        
    }

    /**
     * 
     * @param int $imageId
     * @param string $image
     * @param int $productId
     * @return array
     */
    public function updateProductImage($imageId, $image, $productId =  null) {
        $response = array(
            'status' => false,
            'message' => '',
        );
        try {
            $aUpdateAttribute = array(
            'image' => $image,
            );
            $updateSql = ProductImage::where('id', '=', $imageId);
            if (!empty($productId)) {
                $updateSql = $updateSql->where('fk_product_id', '=', $productId);
            }
            $updateSql = $updateSql->update($aUpdateAttribute);
            $response['status'] = true;
        } catch (Exception $ex) {
            $response['message'] = $ex->getMessage();
        }
        return $response;
    }

    /**
     * 
     * @param int $imageId
     */
    public function deleteProductImage($imageId) {
        ProductImage::destroy($imageId);
    }

    /**
     * 
     */
    public function setProductsImageInCache() {
        $productImages = ProductImage::orderBy('primary', 'desc')->get();
        $aProductImage = $aProductImageAll = array();
        foreach ($productImages as $productImage) {
            $productId = $productImage->fk_product_id;
            $imageName = $productImage->image;
            $isPrimary = $productImage->primary;
            $isThumb = $productImage->is_thumb;
            if (!isset($aProductImage[$productId])) {
                $aProductImage[$productId] = array();
            }
            $aImage['image']        = $imageName;
            $aImage['primary']      = $isPrimary;
            $aImage['thumb']        = $isThumb;
            $aImage['product_id']   = $productId;
            $aProductImage[$productId][] = $aImage;
        }
        //$aProductImageAll['product_images_cache'] = $aProductImage;
        $aProductImageAll = $aProductImage;
        Cache::forever('product_images_cache', $aProductImageAll);
    }

    public function setProductImageInCache($productId) {
        $productImages = $this->getProductImages($productId);
        $aProductImage = array();
        foreach ($productImages as $productImage) {
            $productId = $productImage->fk_product_id;
            $imageName = $productImage->image;
            $isPrimary = $productImage->primary;
            $aImage['image']        = $imageName;
            $aImage['primary']      = $isPrimary;
            $aImage['product_id']   = $productId;
            $aProductImage[$productId][] = $aImage;
        }
        $aProductImageAll = Cache::get('product_images_cache', array());
        $aProductImageAll[$productId] = $aProductImage;
        Cache::forever('product_images_cache', $aProductImageAll);
        return $productImages;
    }
}
