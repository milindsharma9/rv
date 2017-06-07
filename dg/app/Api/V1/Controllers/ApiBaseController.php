<?php

namespace App\Api\V1\Controllers;

use Illuminate\Http\Request;

use App\Http\Requests;

use App\Http\Controllers\Controller;
use App\Api\V1\Response\Response;
use App\Api\V1\Model\CommonModel;

class ApiBaseController extends Controller
{
    protected $responseGenerator;
    protected $_commonModel;

    public function __construct(Request $request) {
        $this->responseGenerator = new Response($request);
        $this->_commonModel = new CommonModel();
    }

    /**
     * Common Method to set Developer Message & Debug Trace in API
     *
     * @param string $debugTrace Error trace in Detail
     *
     */
    public function handleAPIErrorMessages($debugTrace) {
        $aDeveloperMessage = explode("|", $debugTrace);
        $this->responseGenerator->setDeveloperMessage($aDeveloperMessage[0]);
        $this->responseGenerator->setDebugTraceMessage($debugTrace);
    }

    /**
     * Method to get Image Directoy Path
     *
     * @param string $directoryPath
     * @param boolean $upload Tells whether Custom Uploaded Image Path or other
     * @return string BaseUrl for image
     *
     */
    public function getBaseDirectoryForImages($directoryPath, $isThumb = true, $upload = true) {
        $directoryPath = ($isThumb) ? $directoryPath ."/thumb" : $directoryPath;
        if ($upload) {
            $imageDirectory = asset('uploads/'.$directoryPath);
        } else {
            $imageDirectory = asset($directoryPath);
        }
        return $imageDirectory;
    }

}
