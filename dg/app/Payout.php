<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use DB;

class Payout extends Model
{
    protected $table = "payout";
    //only allow the following items to be mass-assigned to our model
    //protected $fillable = ['name', 'image', 'description'];
    
    const FAILED_STATUS = 'FAILED';
    
    public function getPayOutSummary() {
        $payOutDetail = DB::table('payout')
                    ->select('users.id', 'users.first_name', 'users.last_name', 'users.email', 'status')
                    ->addSelect(DB::raw('sum(payout.amount) AS amountTotal'))
                    ->rightJoin('users', 'users.id', '=', 'payout.fk_user_id')
                    ->where('status', '!=', self::FAILED_STATUS)
                    ->orwhereNull('status')
                    ->where('users.fk_users_role', '=', config('appConstants.vendor_role_id'))
                    ->join('sub_store_details AS ssd', 'ssd.fk_users_id', '=', 'users.id')
                    ->join('store_address AS sa', 'sa.fk_users_id', '=', 'users.id')
                    ->where('ssd.fk_parent_id', '=', 0)
                    ->groupBy('users.id')
                    ->get();
        
        return $payOutDetail;
    }

    public function getUserPayOutDetails($vendorId) {
        $payOutDetail = DB::table('payout')
                    ->select('users.id', 'users.first_name', 'users.last_name', 'users.email')
                    ->addSelect('payout.*')
                    ->join('users', 'users.id', '=', 'payout.fk_user_id')
                    ->where('fk_user_id', '=', $vendorId)
                    ->get();
        return $payOutDetail;
    }
}
