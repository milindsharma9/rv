<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Traits\FileUploadTrait;
use Illuminate\Foundation\Bus\DispatchesJobs;
use Illuminate\Routing\Controller as BaseController;
use Illuminate\Foundation\Validation\ValidatesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesRequests;
use Illuminate\Foundation\Auth\Access\AuthorizesResources;

use Illuminate\Support\Facades\Auth;
use App\Cart;
use Exception;
use App\Http\Helper\CommonHelper;

class Controller extends BaseController
{
    use AuthorizesRequests, AuthorizesResources, DispatchesJobs, ValidatesRequests, FileUploadTrait;

    public function __construct() {
        $this->getCartCountforWeb();
    }

    /**
     * Method to get cart count in session
     * Shown on Web Header
     *
     */
    public function getCartCountforWeb() {
        try {
            if (Auth::user()) {
                $userId = Auth::user()->id; // If user is logged in, use Id from users table.
            } else {
                // Check if data is present in session. Not LoggedIn user revisit case.
                $userCartSessionKey = config('appConstants.user_cart_unique_session_key');
                $userId = session()->get($userCartSessionKey, NULL);
            }
            if (!empty($userId)) {
                $cartModel          = new Cart($userId);
                $cartItemCount      = $cartModel->count(true);
                if (!empty($cartItemCount)) {
                    session()->put("cart_custom_total", $cartItemCount);
                    // Set Cart Postcode in session after user Logs in
                    $postCodeDetails = $cartModel->getDeliveryPostcode();
                    $postCode   = $postCodeDetails['postcode'];
                    $lat        = $postCodeDetails['lat'];
                    $lng        = $postCodeDetails['lng'];
                    $cartDeliveryPostcodeSessionKey = CommonHelper::getUserCartDeliveryPostcodeSessionKey();
                    $cartDeliveryLatSessionKey      = \Config::get('appConstants.user_landing_lat_session_key');
                    $cartDeliveryLngSessionKey      = \Config::get('appConstants.user_landing_lng_session_key');
                    session()->put($cartDeliveryPostcodeSessionKey, $postCode);
                    session()->put($cartDeliveryLatSessionKey, $lat);
                    session()->put($cartDeliveryLngSessionKey, $lng);
                    $cartContent = $cartModel->content();
                    session()->put('user_cart_data', $cartContent);
                } else {
                    session()->forget("cart_custom_total");
                    session()->forget("user_cart_data");
                }
            }
        } catch (Exception $ex) {
            // @todo
        }
    }
}
