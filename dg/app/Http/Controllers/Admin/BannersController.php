<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;

use App\Http\Requests;
use App\Http\Controllers\Controller;
use App\Banners;
use App\Http\Helper\FileUpload;

class BannersController extends Controller
{
    const FILE_SUB_DIR = 'banners';

    private $fileUploader       = null;
    
    /**
     *
     * @var array File Validation for different type
     */
    private $aImageValidationRules = array(
        'landing' => array (
            'image'  => array(
                'min_width'     => '1200',
                'min_height'    => '768',
            )
        ),
        'home' => array (
            'image'  => array(
                'min_width'     => '1024',
                'min_height'    => '170',
                'max_height'    => '300',
            )
        ),
        'theme' => array (
            'image'  => array(
                'min_width'     => '1024',
                'min_height'    => '170',
                'max_height'    => '300',
            )
        ),
        'occasion' => array (
            'image'  => array(
                'min_width'     => '1024',
                'min_height'    => '170',
                'max_height'    => '300',
            )
        ),
        'apply_retailer' => array (
            'image'  => array(
                'min_width'     => '1024',
                'min_height'    => '170',
            )
        ),
        'apply_driver' => array (
            'image'  => array(
                'min_width'     => '1024',
                'min_height'    => '170',
            )
        ),
        'content_blog' => array (
            'image'  => array(
                'min_width'     => '1024',
                'min_height'    => '170',
            )
        ),
        'content_event' => array (
            'image'  => array(
                'min_width'     => '1024',
                'min_height'    => '170',
            )
        ),
        'content_place' => array (
            'image'  => array(
                'min_width'     => '1024',
                'min_height'    => '170',
            )
        ),
    );

    public function __construct () {
        $this->fileUploader = new FileUpload();
    }
    
    protected function _getBannerModel() {
        //$bannerModel    = app('request');
        $bannerModel    = \App::make('\App\Banners');
        return $bannerModel;
    }
    /**
     * Display a listing of Banner Image.
     *
     * @return Response
     */
    public function getBannerImages($type = 'landing') {
        $bannerModel    = $this->_getBannerModel();
        $bannerImages   = $bannerModel->getBannerImages($type, $isMobile = 0);
        $fileSubDir     = self::FILE_SUB_DIR;
        $mobileImage    = $bannerModel->getBannerImages($type, $isMobile = 1);
        return view('admin.banners.image', compact('bannerImages', 'type', 'fileSubDir', 'mobileImage'));
    }

    /**
     * Method to upload Banner Image
     *
     * @param Request $request
     * @return type
     */
    public function uploadBannerImage(Request $request) {
        $bannerType     = $request->get('banner_type');
        $isMobile       = $request->get('is_mobile', 0);
        $aValidation    = ($isMobile) ? array() : $this->aImageValidationRules[$bannerType];
        $validateFile   = $this->fileUploader->validateFiles($aValidation);
        if ($validateFile['status']) {
            $bannerModel    = $this->_getBannerModel();
            $isUpdate       = $request->get('is_update');
            $oldImageName   = $request->get('image_name', '');
            if (empty($oldImageName)) {
                $bannerImages = $bannerModel->getBannerImages($bannerType, $isMobile);
                $maxUploadCount = Banners::MAX_UPLOAD_LIMIT;
                if (count($bannerImages) >= $maxUploadCount) {
                    return redirect()
                        ->back()
                        ->withErrors('Max '. $maxUploadCount .' images are allowed.');
                }
            }
            $request    = $this->fileUploader->saveFiles($request, self::FILE_SUB_DIR);
            $imageName  = $request->get('image');
            if (!empty($oldImageName)) {
                //$oldImageName   = $request->get('image_name');
                $imageId        = $request->get('image_id');
                $bannerModel->updateBannerImage($imageId, $imageName);
                $this->fileUploader->deleteFile(self::FILE_SUB_DIR, $oldImageName);
                $bannerModel->clearBannerCache($bannerType);
            } else {
                $bannerModel->insertBannerImage($bannerType, $imageName, 0, $isMobile);
                $bannerModel->clearBannerCache($bannerType);
            }
            return redirect()->back();
        } else {
            return redirect()
                    ->back()
                    ->withErrors($validateFile['error']);
        }
    }

    /**
     * Method to delete Banner Image
     * 
     * @param Request $request
     * @return type
     */
    public function deleteBannerImage(Request $request) {
        $bannerModel        = $this->_getBannerModel();
        $bannerType         = $request->get('banner_type');
        $imageId            = $request->get('image_id');
        $oldImageName       = $request->get('image_name');
        $primary            = $request->get('primary');
        if ($primary) {
            return redirect()
                        ->back()
                        ->withErrors('Cannot delete primary image.');
        }
        $bannerModel->deleteBannerImage($imageId);
        $this->fileUploader->deleteFile(self::FILE_SUB_DIR, $oldImageName);
        $bannerModel->clearBannerCache($bannerType);
        return redirect()->back();
    }

    /**
     * Method to set Product Main Image
     * 
     * @param Request $request
     * @return type
     */
    public function setBannerPrimaryImage(Request $request) {
        $bannerModel     = $this->_getBannerModel();
        $bannerType      = $request->get('banner_type');
        $imageId         = $request->get('image_id');
        $bannerModel->setBannerPrimaryImage($bannerType, $imageId);
        $bannerModel->clearBannerCache($bannerType);
        return redirect()->back();
    }
}
