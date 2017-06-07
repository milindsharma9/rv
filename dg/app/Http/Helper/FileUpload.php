<?php

namespace App\Http\Helper;

use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;
use Validator;

class FileUpload
{
    private $imageTypeBanner    = 'image';
    private $imageTypeLogo      = 'image_logo';
    private $imageTypeThumb     = 'image_thumb';

    private $maxFileUploadSize = '2097152'; // i.e 2 M.B In Bytes.

    /**
     * File upload trait used in controllers to upload files
     */
    public function saveFiles(Request $request, $targetSubDir, $wSize = 50, $hSize = 50)
    {
        ini_set('memory_limit', '256M');
        if (!file_exists(public_path('uploads/'.$targetSubDir))) {
            if (!file_exists(public_path('uploads'))) {
                mkdir(public_path('uploads'), 0777);
            }
            mkdir(public_path('uploads/'.$targetSubDir), 0777);
            mkdir(public_path('uploads/'.$targetSubDir.'/thumb'), 0777);
        }
        $fileSavePath = 'uploads/'.$targetSubDir;
        $fileThumbSavePath = 'uploads/'.$targetSubDir.'/thumb';
        foreach($_FILES as $key => $value) {
            if (!empty($value['tmp_name'])) {
                $filename   = time() . '-' . str_replace(" ", "_", $value['name']);
                $tmp_name   = $value['tmp_name'];
                if ($request->has($key . '_w') && $request->has($key . '_h')) {
                    $file       = $tmp_name;
                    $image      = Image::make($file);
                    if ($key != $this->imageTypeLogo && $key != $this->imageTypeThumb) {
                        Image::make($file)->resize($wSize, $hSize)->save(public_path($fileThumbSavePath) . '/' . $filename);
                    }
                    $width  = $image->width();
                    $height = $image->height();
                    if ($width > $request->{$key . '_w'} && $height > $request->{$key . '_h'}) {
                        $image->resize($request->{$key . '_w'}, $request->{$key . '_h'});
                    } elseif ($width > $request->{$key . '_w'}) {
                        $image->resize($request->{$key . '_w'}, null, function ($constraint) {
                            $constraint->aspectRatio();
                        });
                    } elseif ($height > $request->{$key . '_w'}) {
                        $image->resize(null, $request->{$key . '_h'}, function ($constraint) {
                            $constraint->aspectRatio();
                        });
                    }
                    $image->save(public_path($fileSavePath) . '/' . $filename);
                    $request = new Request(array_merge($request->all(), [$key => $filename]));
                } else {
//                    move_uploaded_file($tmp_name, $fileSavePath. '/' . $filename);
                    $file       = $tmp_name;
                    $image      = Image::make($file);
                    $image->save(public_path($fileSavePath) . '/' . $filename);
                    $request = new Request(array_merge($request->all(), [$key => $filename]));
                }
            }
        }
        return $request;
    }
    
    public function validateFiles($aImageValidationRules = array()) {
        $aResponse = array('status' => true, 'error' => "");
        foreach($_FILES as $key => $value) {
            if (!empty($value['tmp_name'])) {
                $tmp_name       = $value['tmp_name'];
                $allowed        =  array('png','jpg','jpeg');
                $filename       = $value['name'];
                $ext            = pathinfo($filename, PATHINFO_EXTENSION);
                $fileSize       = $value['size'];
                if ($fileSize > $this->maxFileUploadSize) {
                    $aResponse['status'] = false;
                    $aResponse['error']  = trans('messages.max_upload_size_increase');
                    break;
                }
                if (!in_array($ext, $allowed)) {
                    $aResponse['status'] = false;
                    $aResponse['error']  = trans('messages.invalid_file_format'). 'png,jpg,jpeg';
                    break;
                }
                $aValidation    = (isset($aImageValidationRules[$key])) ? $aImageValidationRules[$key] : array();
                $imageMinWidth  = isset($aValidation['min_width']) ? $aValidation['min_width'] : "";
                $imageMinHeight = isset($aValidation['min_height']) ? $aValidation['min_height'] : "";
                $imageMaxHeight = isset($aValidation['max_height']) ? $aValidation['max_height'] : "";
                if (!empty($imageMinWidth) || !empty($imageMinHeight)) {
                    $image_info     = getimagesize($tmp_name);
                    $image_width    = $image_info[0];
                    $image_height   = $image_info[1];
                    if (!empty($imageMinWidth) && $image_width < $imageMinWidth) {
                        $aResponse['status']    = false;
                        $aResponse['error']     = trans('messages.incorrect_dimensions') ." min_width:".$imageMinWidth;
                        break;
                    }
                    if (!empty($imageMinHeight) && $image_height < $imageMinHeight) {
                        $aResponse['status']    = false;
                        $aResponse['error']     = trans('messages.incorrect_dimensions') ." min_height:".$imageMinHeight;
                        break;
                    }
                    if (!empty($imageMaxHeight) && $image_height > $imageMaxHeight) {
                        $aResponse['status']    = false;
                        $aResponse['error']     = trans('messages.incorrect_dimensions') ." max_height:".$imageMaxHeight;
                        break;
                    }
                }
            }
        }
        return $aResponse;
    }

    /**
     * 
     * @param type $targetSubDir
     * @param type $fileName
     */
    public function deleteFile($targetSubDir, $fileName) {
        if (!empty($fileName)) {
            $filePath  = public_path('uploads/'.$targetSubDir.'/'.$fileName);
            if (file_exists($filePath)) {
                unlink($filePath);
            }
            $fileNameThumb  = public_path('uploads/'.$targetSubDir.'/thumb/'.$fileName);
            if (file_exists($fileNameThumb)) {
                unlink($fileNameThumb);
            }
        }
    }

}