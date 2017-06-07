<?php

namespace App;

use Illuminate\Database\Eloquent\Model;

use App\Http\Helper\CommonHelper;
use Exception;
use Illuminate\Support\Facades\Cache;
use Log;

class Banners extends Model
{
    const MAX_UPLOAD_LIMIT = 4;
    
   /**
     *
     * @var table name
     */
    protected $table = "banners";
    
    //only allow the following items to be mass-assigned to our model
    /**
     *
     * @var Allow write on db table columns 
     */
    protected $fillable = ['type', 'image', 'primary', 'is_mobile', 'created_at', 'updated_at'];

    /**
     * 
     * @param string $type
     * @return array
     */
    public function getBannerImages($type, $isMobile = 0) {
        $aImages = Banners::where('type', '=', $type)->where('is_mobile', '=', $isMobile);
        if ($isMobile) {
            $aImages = $aImages->first();
        } else {
            $aImages = $aImages->get();
        }
        return $aImages;
    }

    /**
     * 
     * @param string $type
     * @param int $imageId
     * @return array
     */
    public function setBannerPrimaryImage($type, $imageId) {
        $response = array(
            'status' => false,
            'message' => '',
        );
        try {
            $aUpdate = array(
                'primary' => 0,
            );
            $resetSql = Banners::where('type', '=', $type)->update($aUpdate);
            $aUpdateAttribute = array(
                'primary' => 1,
            );
            $updateSql = Banners::where('id', '=', $imageId)
                    ->where('type', '=', $type)
                    ->update($aUpdateAttribute);
            $response['status'] = true;
        } catch (Exception $ex) {
            $response['message'] = $ex->getMessage();
        }
        return $response;
    }

    /**
     * 
     * @param string $type
     * @param string $image
     * @param int $isPrimary
     * @return array
     */
    public function insertBannerImage($type, $image, $isPrimary = 0, $isMobile = 0) {
        $response = array(
            'status' => false,
            'message' => '',
        );
        try {
            $aInsertData = array(
                'type'          => $type,
                'image'         => $image,
                'primary'       => $isPrimary,
                'is_mobile'       => $isMobile,
                'created_at'    => CommonHelper::getCurrentDateTime(),
            );
            Banners::create($aInsertData);
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
     * @param string $type
     * @return array
     */
    public function updateBannerImage($imageId, $image, $type =  null) {
        $response = array(
            'status' => false,
            'message' => '',
        );
        try {
            $aUpdateAttribute = array(
                'image' => $image,
            );
            $updateSql = Banners::where('id', '=', $imageId);
            if (!empty($type)) {
                $updateSql = $updateSql->where('type', '=', $type);
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
    public function deleteBannerImage($imageId) {
        Banners::destroy($imageId);
    }

    /**
     * Public function to Fetch banner Image
     * 
     * @return array $bannerData
     */
    public function getBannerData($type, $isMobile = 0) {
        $bannerData = array();
        try {
            $bannerData = $this->_getBannerDataCache($type, $isMobile);
            if (empty($bannerData)) {
                $bannerData = $this->_getBannerData($type, $isMobile);
                $this->_setBannerDataCache($type, $bannerData, $isMobile);
            }
        } catch (Exception $ex) {
            Log::error(__METHOD__ . $ex->getMessage());
        }
        return $bannerData;
    }

    /**
     * Private function to Fetch Banner data from cache
     * 
     * @return array $bannerData
     */
    private function _getBannerDataCache($type, $isMobile) {
        $bannerData = '';
        try {
            $bannerData = Cache::get('banner_type_' . $type . "_" . $isMobile, '');
        } catch (Exception $ex) {
            Log::error(__METHOD__ . $ex->getMessage());
        }
        return $bannerData;
    }

    /**
     * Private function to set Banner data in cache 
     *
     * @param string $bannerData
     * @param string $type
     * @return void
     *
     */
    private function _setBannerDataCache($type, $bannerData, $isMobile) {
        try {
            Cache::forever('banner_type_' . $type . "_" . $isMobile , $bannerData);
        } catch (Exception $ex) {
            Log::error(__METHOD__ . $ex->getMessage());
        }
    }

    /**
     * Private function to get Banner Data from DB
     * 
     * @return string
     */
    private function _getBannerData($type, $isMobile) {
        $bannerImage = $type . "_banner_default.png";
        try {
            $aBannerData = Banners::where('type', '=', $type)->where('is_mobile', '=', $isMobile)->where('primary', '=', 1)->first();
            if (empty($aBannerData)) {
                $aBannerData = Banners::where('type', '=', $type)->where('is_mobile', '=', $isMobile)->first();
            }
            if (!empty($aBannerData)) {
                $bannerImage = $aBannerData->image;
            }
        } catch (Exception $ex) {
            Log::error(__METHOD__ . $ex->getMessage());
        }
        return $bannerImage;
    }

    /**
     * Clear Banner Cache.
     * 
     * @return void
     */
    public function clearBannerCache($type) {
        try {
            Cache::forget('banner_type_' . $type . "_0");
            Cache::forget('banner_type_' . $type . "_1");
        } catch (Exception $ex) {
            Log::error(__METHOD__ . $ex->getMessage());
        }
    }
}
