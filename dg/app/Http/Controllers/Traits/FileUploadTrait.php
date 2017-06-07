<?php

namespace App\Http\Controllers\Traits;

use Illuminate\Http\Request;
use Intervention\Image\Facades\Image;
use Validator;

trait FileUploadTrait
{

    /**
     * File upload trait used in controllers to upload files
     */
    public function saveFiles(Request $request, $targetSubDir, $wSize = 50, $hSize = 50)
    {
        if (!file_exists(public_path('uploads/'.$targetSubDir))) {
            if (!file_exists(public_path('uploads'))) {
                mkdir(public_path('uploads'), 0777);
            }
            mkdir(public_path('uploads/'.$targetSubDir), 0777);
            mkdir(public_path('uploads/'.$targetSubDir.'/thumb'), 0777);
        }
        $fileSavePath = 'uploads/'.$targetSubDir;
        $fileThumbSavePath = 'uploads/'.$targetSubDir.'/thumb';

        foreach ($request->all() as $key => $value) {
            if ($request->hasFile($key)) {
                if ($request->has($key . '_w') && $request->has($key . '_h')) {
                    // Check file width
                    $filename = time() . '-' . $request->file($key)->getClientOriginalName();
                    $file     = $request->file($key);
                    $image    = Image::make($file);
                    Image::make($file)->resize($wSize, $hSize)->save(public_path($fileThumbSavePath) . '/' . $filename);
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
                    $filename = time() . '-' . $request->file($key)->getClientOriginalName();
                    $request->file($key)->move(public_path($fileSavePath), $filename);
                    $request = new Request(array_merge($request->all(), [$key => $filename]));
                }
            }
        }
        return $request;
    }
    
    public function validateFile($request) {
        $aResponse = array('status' => true, 'error' => "");
        foreach ($request->all() as $key => $value) {
            if ($request->hasFile($key)) {
                $rules = array('file' => 'required|mimes:png,gif,jpeg,jpg'); //'required|mimes:png,gif,jpeg,txt,pdf,doc'
                $validator = Validator::make(array('file' => $request->file($key)), $rules);
                if ($validator->fails()) {
                    return array('status' => false, 'error' => $validator);
                }
            }
        }
        return $aResponse;
    }
}