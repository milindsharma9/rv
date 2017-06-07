<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;
use Exception;
use Log;
use App\Http\Helpers\Email;
use Illuminate\Support\Facades\Validator;
use App\Http\Helper\CommonHelper;

class ContactUs extends Model
{
    protected $table = "contact_us";
    //only allow the following items to be mass-assigned to our model
    protected $fillable = ['name', 'email', 'feedback', 'message', 'created_at'];
    
    /**
     * To save data contact us form.
     * @param array $data
     * @return string
     */
    public function saveContactUs($data) {
        $response = ['status' => FALSE, 'message' => 'Something Went Wrong!!'];
        try {
            if (!empty($data)) {
                $rules = array(
                    'name'          => 'required|max:64',
                    'email'         => 'required|email|max:255',
                    'message'       => 'required',
                    'CaptchaCode'   => 'valid_captcha'
                );
                $validator = Validator::make($data, $rules);
                if ($validator->fails()) {
                    $response['message'] = $validator;
                    return $response;
                }
                $aData = array(
                    'name' => isset($data['name']) ? $data['name'] : '',
                    'email' => isset($data['email']) ? $data['email'] : '',
                    'message' => isset($data['message']) ? $data['message'] : '',
                    'created_at' => CommonHelper::getCurrentDateTime(),
                );
                DB::table('contact_us')->insert($aData); // Query Builder
                $mergeVars = array(array('name' => 'data', 'content' => ''));
                Email::sendEmail(env('MAIL_BCC_ADDRESS'), $mergeVars, 'Contact us');
                $response['status'] = TRUE;
                $response['message'] = 'Details Saved Successfully';
            }
        } catch (Exception $ex) {
            Log::error(__METHOD__ . $ex->getMessage());
        }
        return $response;
    }

}
