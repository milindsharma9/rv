<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
//use Illuminate\Database\Eloquent\SoftDeletes;

class ValidPostcode extends Model
{
    //
    // Commented as we donot want soft delete in this case
    //use SoftDeletes;

    protected $table = "valid_postcodes";
    //only allow the following items to be mass-assigned to our model
    protected $fillable = ['postcode', 'lat', 'lng'];
   // protected $dates = ['deleted_at'];

    /**
     * Method to fetch valid Postcodes
     * Also used for Autosuggest on landing page
     *
     * @param string $searchParam
     * return object Object of ValidPostcode
     *
     */
    public function getValidPostcodes($searchParam = null, $exactSearch = true) {
        $searchParam = str_replace(" ", "", $searchParam); // To nullify space in between postcodes
        $aPostcode = array();
        if (!$exactSearch) {
            $postcodes = ValidPostcode::select('postcode')->whereRaw("replace(postcode , ' ','') like '".$searchParam."%'")->orderBy('postcode')->take(7)
                ->get();
        } else {
            $postcodes = ValidPostcode::select('postcode')->whereRaw("replace(postcode , ' ','') = '".$searchParam."'")->orderBy('postcode')->get();
        }
        foreach ($postcodes as $postcode) {
            $aPostcode[] = $postcode['postcode'];
        }
        return $aPostcode;
    }

    /**
     * Method to get Postcode Details
     *
     * @param string $postCode Postcode
     *
     * @return object ValidPostcode
     *
     */
    public function getPostcodeDetail($postCode) {
        $postCode = str_replace(" ", "", $postCode); // To nullify space in between postcodes
        $postcode = ValidPostcode::whereRaw("replace(postcode , ' ','') = '".$postCode."'")->first();
        return $postcode;
    }

    /**
     * Method to log postcode in CSV file
     *
     * @param string $postcode
     * @param int $isSuccess
     */
    public static function logPostcode($postCode, $isSuccess) {
        $filePath = public_path()."/files/postcode_log.csv";
        $file = fopen($filePath, 'a');
        $aLog = $postCode . "," . $isSuccess .",".date('Y-m-d H:i:s')."\n";
        fwrite($file, $aLog);
        fclose($file);
        
    }
}
