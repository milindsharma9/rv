<?php

namespace App;

use Illuminate\Http\Request;
use Illuminate\Database\Eloquent\Model;
use DB;
use App\Http\Helper\CommonHelper;

class Coupon extends Model {

    protected $table = "coupon";

    /**
     * The attributes that are mass assignable.
     *
     * @var array
     */
    protected $fillable = [
        'id', 'coupon_code', 'discount_type', 'discount_amount', 'usage', 'updated_at', 'created_at', 'date_expiry'
    ];

    /**
     * Method to validate coupon
     *
     * @param string $promoCode
     * @return array Details of nearBY Stores
     */
    public function validateCoupon($promoCode, $userId) {
        $couponResponse = array(
            'valid'     => false,
            'message'   => trans('messages.coupon_invalid'),
            'data'      => '',
        );
        $couponDataDB = DB::table('coupon')
                ->select('id', 'coupon_code', 'discount_type', 'date_expiry', 'discount_amount', 'usage')
                ->where('coupon_code', '=', $promoCode)
                ->first();
        if (!empty($couponDataDB)) {
            $couponResponse['data'] = $couponDataDB;
            if ($couponDataDB->date_expiry != '0000-00-00 00:00:00' && strtotime($couponDataDB->date_expiry) <= time()) {
                $couponResponse['message'] = trans('messages.coupon_expired');
            } else {
                $usageResponse = $this->checkCouponUsageForCustomer($couponDataDB->id, $userId, $couponDataDB->usage);
                if ($usageResponse['status']) {
                    $couponResponse['valid']    = true;
                    $couponResponse['message']  = trans('messages.coupon_valid');
                } else {
                    $couponResponse['message']  = $usageResponse['message'];
                }
            }
        }
        return $couponResponse;
    }

    /**
     * Method to check usage count of coupon for user.
     *
     * @param int $couponId
     * @param int $userId
     * @param int $usageLimit
     * @return array
     */
    public function checkCouponUsageForCustomer($couponId, $userId = null, $usageLimit) {
        $response = array(
            'status'  => false,
            'message' => '',
        );
        if($usageLimit == 0){
            $response['status'] =  true;
            return $response;
        }
        $couponUsageData = DB::table('user_coupon')
            ->select('id')
            ->where('fk_coupon_id', '=', $couponId);
        if (!empty($userId)) {
            $couponUsageData = $couponUsageData->where('fk_users_id', '=', $userId);
        }
        $couponUsageData = $couponUsageData->get();
        if (count($couponUsageData) < $usageLimit) {
            $response['status'] =  true;
        } else {
            $response['message'] =  trans('messages.coupon_usage_exceeds');
        }
        return $response;
    }

    /**
     * Method to log user coupon usage.
     *
     * @param int $couponId
     * @param int $userId
     */
    public function logUserCouponUsage($couponId, $userId = null) {
        $insertData = array(
            'fk_users_id' => $userId,
            'fk_coupon_id' => $couponId,
            'date_used' => CommonHelper::getCurrentDateTime(),
            'created_at' => CommonHelper::getCurrentDateTime(),
        );
        $orderId = DB::table('user_coupon')->insertGetId($insertData);
    }

}
