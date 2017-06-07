<?php

namespace App;

use Illuminate\Foundation\Auth\User as Authenticatable;
use Exception;
use Log;

/**
 * StoreDetails Model
 */
class StoreDetails extends Authenticatable {

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'fk_users_id', 'business_type',
        'cname', 'pln', 'dps', 'licence_number', 'director', 'company_number',
        'company_type', 'legal_fname', 'legal_lname', 'legal_dob', 'nationality',
        'country_residence'
    ];

    /**
     * One to one association with user
     * 
     * @package StoreDetails Model
     * @return App\User
     */
    public function user() {
        return $this->belongsTo('App\User', 'fk_users_id');
    }

    /**
     * Method creates new store_details table entry. (new store details).
     * 
     * @param Array $data
     * @return Int
     */
    public function createNewStore($data) {
        try {
            $date = isset($data['legal_representative_dob_dd']) ? $data['legal_representative_dob_dd'] : 00;
            $month = isset($data['legal_representative_dob_mm']) ? $data['legal_representative_dob_mm'] : 00;
            $year = isset($data['legal_representative_dob_yy']) ? $data['legal_representative_dob_yy'] : 0000;
            $dob = $year . '-' . $month . '-' . $date;
            return StoreDetails::create([
                        'fk_users_id' => isset($data['id_user']) ? $data['id_user'] : '',
                        'business_type' => isset($data['business_type']) ? $data['business_type'] : '',
                        'cname' => isset($data['cname']) ? $data['cname'] : '',
                        'pln' => isset($data['pln']) ? $data['pln'] : '',
                        'dps' => isset($data['dps']) ? $data['dps'] : '',
                        'licence_number' => isset($data['licence_number']) ? $data['licence_number'] : '',
                        'company_number' => isset($data['company_number']) ? $data['company_number'] : '',
                        'company_type' => isset($data['company_type']) ? $data['company_type'] : '',
                        'legal_fname' => isset($data['legal_representative_fname']) ? $data['legal_representative_fname'] : '',
                        'legal_lname' => isset($data['legal_representative_lname']) ? $data['legal_representative_lname'] : '',
                        'legal_dob' => $dob,
                        'nationality' => isset($data['legal_representative_nationality']) ? $data['legal_representative_nationality'] : '',
                        'country_residence' => isset($data['legal_representative_country_residence']) ? $data['legal_representative_country_residence'] : '',
            ]);
        } catch (Exception $ex) {
            Log::error(__METHOD__ . $ex->getMessage());
        }
    }

}
