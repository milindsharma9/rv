<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Exception;
use Log;
use Cache;

class Cms extends Model
{
    //
    use SoftDeletes;

    protected $table = "cms";
    //only allow the following items to be mass-assigned to our model
    protected $fillable = ['title', 'description', 'user_type', 'meta_title', 'meta_description', 'meta_keywords'
        , 'url_path', 'published'];
    protected $dates = ['deleted_at'];

    /**
     * Method to fetch CMS Page Data
     *
     * @param string $type
     * @param string $title
     * @return object $cmsData
     */
    public function getCmsPageContent($type, $title) {
        $cmsData = Cms::where('user_type', '=', $type)->where('title', '=', $title)->first();
        return $cmsData;
    }

    /**
     * Method to fetch CMS Page Data based on URL
     *
     * @param string $type
     * @param string $urlPath
     * @return object $cmsData
     */
    public function getCmsPageData($type, $urlPath) {
        $cmsData = Cms::where('url_path', '=', $urlPath)
                ->where('user_type', '=', $type)
                ->where('published', '=', 1)
                ->first();
        return $cmsData;
    }

    /**
     * Method to fetch CMS Page Data user wise
     *
     * @param string $userTypeToExclude For customer we need general & User Specific page.
     * Pass Store in case of User & vice - versa.
     * @return object $cmsData
     */
    public function getUserCmsData($userTypeToExclude) {
        $cmsData = Cms::where('user_type', '!=', $userTypeToExclude)
                ->select('title', 'url_path', 'user_type')
                ->where('published', '=', 1)
                ->get();
        return $cmsData;
    }

    /**
     * Private Method to fetch CMS Page Data for footer links
     *
     * @return object $cmsData
     */
    private function _getCmsPageForFooter() {
        $titleCookies   = config('cms.page_cookies');
        $titleLegal     = config('cms.page_legal');
        $titlePrivacy   = config('cms.page_privacy');
        $aTitle         = array(
            $titleCookies,
            $titleLegal,
            $titlePrivacy,
        );
        $cmsData = Cms::whereIn('title', $aTitle)
                ->select('title', 'url_path', 'user_type')
                ->where('published', '=', 1)
                ->get();
        if (!empty($cmsData)) {
            $cmsData = $cmsData->toArray();
        }
        return $cmsData;
    }
    
    /**
     * Public function to Fetch CMS Footer Pages
     * 
     * @return array $cmsFooterData
     */
    public function getCmsPageForFooter() {
        $cmsFooterData = array();
        try {
            $cmsFooterData = $this->_getCmsPageForFooterCache();
            //$cmsFooterData = array();
            if (empty($cmsFooterData)) {
                $cmsFooterData = $this->_getCmsPageForFooter();
                $this->_setCmsPageForFooterCache($cmsFooterData);
            }
        } catch (Exception $ex) {
            Log::error(__METHOD__ . $ex->getMessage());
        }
        return $cmsFooterData;
    }

    /**
     * Private function to Fetch CMS Footer Pages from cache
     * 
     * @return array
     */
    private function _getCmsPageForFooterCache() {
        $cmsFooterData = Cache::get('cms_page_footer', array());
        return $cmsFooterData;
        
    }

    /**
     * Private function to set CMS Footer Pages in cache 
     * 
     * @param array $cmsFooterData
     * @return void
     */
    private function _setCmsPageForFooterCache($cmsFooterData) {
        Cache::forever('cms_page_footer', $cmsFooterData);
    }

    /**
     * Method to clear CMS Page cache
     *
     */
    public static function flushCmsPageCache() {
        Cache::forget('cms_page_footer');
    }

}
