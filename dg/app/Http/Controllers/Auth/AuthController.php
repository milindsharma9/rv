<?php

namespace App\Http\Controllers\Auth;

use App\User;
use Validator;
use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ThrottlesLogins;
use Illuminate\Foundation\Auth\AuthenticatesAndRegistersUsers;
use App\Cart;
use App\Http\Helper\CommonHelper;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Log;

class AuthController extends Controller {
    /*
      |--------------------------------------------------------------------------
      | Registration & Login Controller
      |--------------------------------------------------------------------------
      |
      | This controller handles the registration of new users, as well as the
      | authentication of existing users. By default, this controller uses
      | a simple trait to add these behaviors. Why don't you explore it?
      |
     */

use AuthenticatesAndRegistersUsers,
    ThrottlesLogins;

    /**
     * Where to redirect users after login / registration.
     *
     * @var string
     */
    protected $redirectTo = '/';

    /**
     * Create a new authentication controller instance.
     *
     * @return void
     */
    public function __construct() {
        $this->middleware($this->guestMiddleware(), ['except' => 'logout']);
    }

    /**
     * Function to validate Registration.
     *
     * @return array
     */
    public function validationMessages() {
        try {
            return [
                'fname.required' => trans('The First Name is required.'),
                'fname.max' => trans('The First Name may not be greater than 64 characters.'),
                'lname.required' => trans('The Last Name is required.'),
                'lname.max' => trans('The Last Name may not be greater than 64 characters'),
            ];
        } catch (Exception $ex) {
            Log::error(__METHOD__ . $ex->getMessage());
        }
    }

    /**
     * Get a validator for an incoming registration request.
     *
     * @param  array  $data
     * @return \Illuminate\Contracts\Validation\Validator
     */
    protected function validator(array $data) {
        $validator =  Validator::make($data, [
                    'fname'     => 'required|max:64',
                    'lname'     => 'required|max:64',
                    'phone'     => 'required|numeric|digits_between:7,15',
                    'email'     => 'required|email|max:255|unique:users',
                    'password'  => 'required|min:6|confirmed',
        ], $this->validationMessages());
        if ($validator->fails()) {
            return $validator;
        } else {
            $phoneNumber            = isset($data['phone']) ? $data['phone'] : "";
            $validatePhoneResponse  = CommonHelper::validatePhoneNumber($phoneNumber);
            if (!$validatePhoneResponse['status']) {
                $validator->after(function($validator) {
                    $validator->errors()->add('phone', trans('messages.invalid_phone_number'));
                });
            }
        }
        return $validator;
    }

    /**
     * Create a new user instance after a valid registration.
     *
     * @param  array  $data
     * @return User
     */
    protected function create(array $data) {
        return User::create([
                    'first_name'    => isset($data['fname']) ? $data['fname'] : "First name",
                    'email'         => $data['email'],
                    'password'      => bcrypt($data['password']),
                    'last_name'     => isset($data['lname']) ? $data['lname'] : "Last name",
                    'phone'         => isset($data['phone']) ? $data['phone'] : "",
                    'fk_users_role' => isset($data['fk_users_role']) ? $data['fk_users_role'] : "3",
                    'email'         => $data['email'],
                    'activated'     => $data['activated'],
        ]);
    }

    /**
     * Overide Function Called after successfull login
     *
     */
    protected function authenticated($request, $user) {
        $this->handleUserCart($user);
        if ($request->ajax()) {
            return response()->json(['message' => 'this is done successfully', 'status' => '1', 'url' => $this->getUrl($user)
            ]);
        }
        if (!$user->activated) {
            auth()->logout();
            return back()->with('warning', 'You need to confirm your account. And set your password.');
        }
        if ($user->fk_users_role == \Config::get('appConstants.admin_role_id')) {
            return redirect()->route('admin.events.index');
        }
        return redirect()->intended('/');
    }

    /**
     * 
     * @param type $request
     * @return type
     */
    protected function sendFailedLoginResponse($request) {
        if ($request->ajax()) {
            return response()->json([
                        'message' => $this->getFailedLoginMessage(),
                        'username' => $this->loginUsername(),
                        'request' => $request,
                        'status' => '0'
            ]);
        }
    }

    /**
     * 
     * @param type $user
     * @return type
     */
    protected function getUrl($user) {
        if ($user->fk_users_role == \Config::get('appConstants.admin_role_id')) {
            return route('admin.dashboard');
        } else if ($user->fk_users_role == \Config::get('appConstants.user_role_id')) {
            if (session()->has('checkout_login')) {
                session()->forget('checkout_login');
                return route('customer.checkout');
            }
            return route('home.index');
        } else if ($user->fk_users_role == \Config::get('appConstants.vendor_role_id')) {
            return route('store.dashboard');
        }
        return route('home.index');
    }
    
    /*
     * Ovewrite Login Display page
     * @todo remove Login View
     */
    public function showLoginForm() {
        return redirect()->route('home.index');
    }

    /**
     * Method to update / merge cart data on user log in
     *
     * @param object $user User Object
     * @return void
     *
     */
    public function handleUserCart($user) {
        if ($user->fk_users_role == \Config::get('appConstants.user_role_id')) {
            $userCartSessionKey = \Config::get('appConstants.user_cart_unique_session_key');
            $userCartId         = $user->id;
            $cartModel          = new Cart($userCartId);
            $cartDataResponse   = $cartModel->getUserCartData($userCartId);
            $cartContent        = $cartDataResponse['data'];
            $userCartWebKey     = session()->get($userCartSessionKey, '');
            if (!empty($userCartWebKey)) {
                $userCartPreviousContent = $cartModel->getUserCartData($userCartWebKey);
                if ($userCartPreviousContent['existing']) {
                    $currentDeliveryPostcode = CommonHelper::getUserCartDeliveryPostcode();
                    $previousDeliveryPostcode = '';
                    if (isset($cartDataResponse['order_data'])) {
                        $previousOrderData = $cartDataResponse['order_data'];
                        $previousDeliveryPostcode = isset($previousOrderData['delivery_postcode']) ? $previousOrderData['delivery_postcode'] : array();
                        $previousDeliveryPostcode = isset($previousDeliveryPostcode['postcode']) ? $previousDeliveryPostcode['postcode'] : '';
                    }
                    if ($previousDeliveryPostcode == $currentDeliveryPostcode) {
                        $mergedCartContent = $cartContent->merge($userCartPreviousContent['data']);
                    } else {
                        $mergedCartContent = $userCartPreviousContent['data'];
                    }
                    $count = 0;
                    foreach ($mergedCartContent as $row) {
                        $count += $row['qty'];
                    }
                    $cartModel->saveUserCartData($userCartId, $mergedCartContent, $count);
                    $cartModel->deleteUserCartData($userCartWebKey);
                }
                session()->forget($userCartSessionKey);
            }
        }
    }

    /**
     *
     * Logout User From Platform
     *
     */
    public function logout() {
        auth()->logout();
        \Session::flush();
        return redirect('/administrator');
    }
    
    /**
     * Handle a registration request for the application.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function register(Request $request)
    {
        $validator = $this->validator($request->all());

        if ($validator->fails()) {
            $this->throwValidationException(
                $request, $validator
            );
        }

        Auth::guard($this->getGuard())->login($this->create($request->all()));
        $user = Auth::user();
        if($request->get('is_subscribe')){
            User::updateSubscriptionStatus($user->id,1);
        }
        $this->handleUserCart($user);
        $redirectUrl = $this->getUrl($user);
        //return redirect($redirectUrl);
        return response()->json(['message' => 'register success', 'status' => '1', 'url' => $this->getUrl($user)]);
        
    }
}
