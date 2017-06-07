<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
//use App\StoreDetails;
use App\StoreModel;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Input;
use Illuminate\Support\Facades\Redirect;
use App\Http\Helpers\Email;
use Exception;
use App\Cms;
use App\ContactUs;
use Illuminate\Support\Facades\Log;
use App\Http\Helper\CommonHelper;
use App\Configurations;

/**
 * HomeController 
 * 
 * PHP version 5.6
 * 
 * @category  Laravel 5.2
 * @package   HomeController
 * @copyright 2016 
 * @license   http://52.50.219.163/
 * @link      http://52.50.219.163/
 * 
 */
class HomeController extends Controller {

    /**
     * Create a new controller instance.
     *
     * @package HomeController
     * @return void
     */
    public function __construct() {

        $this->middleware('auth', [
            'except' => [
                'registerVendor', 'activate', 'createPassword', 'renderContactUs', 'renderRetailerApplyForm', 'renderDriverApplyForm'
                , 'saveDriverApplicationForm', 'saveContactUs', 'resetPassword'
            ]
        ]);
    }

    /**
     * Registor a new Vendor.
     * 
     * @package HomeController
     * @param Request $request
     * @return mixed
     */
    /*public function registerVendor(Request $request) {
        $formUrl = route('home.registerVendor');
        if ($request->isMethod('post')) {
            try {
                $data               = $request->all();
                $data['state']      = config('appConstants.store_default_country');
                $businessType       = $data['business_type'];
                $storeModel         = new StoreModel();
                $validationRules    = $storeModel->getValidationRulesForRegistration($businessType);
                $validation         = Validator::make($data, $validationRules, $storeModel->validationMessagesForAddress());
                if ($validation->fails()) {
                    return redirect()->back()->withInput()->withErrors($validation);
                } else {
                    $registrationResponse = $storeModel->registerVendor($data);
                    return redirect()->back()->with('status', $registrationResponse['message']);
                }
            } catch (Exception $ex) {
                return redirect()->route('home.registerVendor')->with('warning', trans('messages.common_error'));
            }
        } else {
            $businessType   = config('mangopay.legal_user_type');
            $type           = config('cms.user_type.Store');
            $title          = config('cms.page_seller_aggrement');
            $cmsModel       = new Cms();
            $cmsData        = $cmsModel->getCmsPageContent($type, $title);
            return view('home.registerVendor', compact('businessType', 'cmsData', 'formUrl'));
        }
    } */

    /**
     * Activate new Vendor on successful verification of token.
     * 
     * @package HomeController
     * @param string $token
     * @return mixed
     */
    public function activate($token = NULL) {
        if (!empty($token)) {
            $user = \DB::table('users')->where('token', $token)->where('activated', '0')->first();
            if ($user)
                return view('home.createPassword')->with('userId', $user->id);
            else
                return redirect()->route('home.registerVendor')->with('warning', trans('messages.store_register_activate_error'));
        } else
            return redirect()->route('home.registerVendor')->with('warning', trans('messages.store_register_activate_exception_error'));
    }

    /**
     * To set Password for registored vendor after succesful verification.
     * 
     * @package HomeController
     * @param Request $request
     * @return mixed
     */
    public function createPassword(Request $request) {
        if ($request->isMethod('post')) {
            $data = $request->all();
            $rules = array(
                'password' => 'required|min:6|confirmed',
            );
            $validation = Validator::make($data, $rules);
            if ($validation->fails()) {
                return back()->withInput()->withErrors($validation);
            } else {
                $update = \DB::table('users')->where('id', $data['id'])
                        ->update(['password' => bcrypt($data['password']), 'activated' => $data['activated']]);
                if ($update) {
                    return Redirect::to('/');
                }
                else {
                    return Redirect::to('/home/createPassword')->with('userId', $data['id'])->withErrors('error', 'Something went wrong, Please get in touch with Admin.');
                }
            }
        }
        return view('home.createPassword');
    }

    /**
     * To reset user Password
     * 
     * @package HomeController
     * @return mixed
     */
    public function resetPassword() {
        return view('auth.passwords.email');
    }

    /**
     * To render Contact Us Page
     * 
     * @return mixed
     */
    public function renderContactUs() {
        return view('contact-us');
    }

    /**
     *  To render Driver apply Form
     * 
     * @return mixed
     */
    public function renderDriverApplyForm() {
        $bannerType     = config('banner.banner_type_apply_driver');
        $bannerImage    = CommonHelper::getBannerImage($bannerType, 0);
        $configModel    = new Configurations();
        $bannerTitle    = $configModel->get(config('configurations.apply_driver_title_key'));
        $bannerSubText  = $configModel->get(config('configurations.apply_driver_subtext_key'));
        $driverFormRoute = 'common.drivers.apply.save';
        return view('rider-apply', compact('driverFormRoute', 'bannerImage', 'bannerTitle', 'bannerSubText'));
    }

    /**
     *  To render Retailer apply Form
     * 
     * @return mixed
     */
    public function renderRetailerApplyForm(Request $request) {
        $formUrl = route('common.retailers.apply.show');
        if ($request->isMethod('post')) {
            try {
                $previousUrl        = app('url')->previous();
                $previousUrl        = $previousUrl . '#response';
                $data               = $request->all();
                $data['state']      = config('appConstants.store_default_country');
                $businessType       = $data['business_type'];
                $storeModel         = new StoreModel();
                $validationRules    = $storeModel->getValidationRulesForRegistration($businessType);
                $validation         = Validator::make($data, $validationRules, $storeModel->validationMessagesForAddress());
                if ($validation->fails()) {
                    return redirect()->to($previousUrl)->withInput()->withErrors($validation);
                } else {
                    $registrationResponse = $storeModel->registerVendor($data);
                    return redirect()->to($previousUrl)->with('status', $registrationResponse['message']);
                }
            } catch (Exception $ex) {
                return redirect()->to($previousUrl)->with('warning', trans('messages.common_error'));
            }
        } else {
            $businessType   = config('mangopay.legal_user_type');
            $type           = config('cms.user_type.Store');
            $title          = config('cms.page_seller_aggrement');
            $cmsModel       = new Cms();
            $cmsData        = $cmsModel->getCmsPageContent($type, $title);
            //
            $bannerType     = config('banner.banner_type_apply_retailer');
            $bannerImage    = CommonHelper::getBannerImage($bannerType, 0);
            $configModel    = new Configurations();
            $bannerTitle    = $configModel->get(config('configurations.apply_retailer_title_key'));
            $bannerSubText  = $configModel->get(config('configurations.apply_retailer_subtext_key'));
            //
            return view('vendor-apply', compact('businessType', 'cmsData', 'formUrl', 'bannerImage', 'bannerTitle', 'bannerSubText'));
        }
    }

    /**
     *  To render Retailer apply Form
     * 
     * @return mixed
     */
    public function saveDriverApplicationForm(Request $request) {
        $userModel      = new User();
        $rules          = $userModel->getDriverValidationRules();
        $messages       = $userModel->getDriverValidationMessages();
        $validator      = Validator::make($request->all(), $rules, $messages);
        $previousUrl    = app('url')->previous();
        $previousUrl    = $previousUrl . '#response';
        $vehicleType    = $request['vehicle'];
        $vehicleQuestions = config('rider_configurations.'.$vehicleType.'_question');
        $data             = $request->all();
        if ($validator->fails()) {
            return redirect()->to($previousUrl)->withInput()->withErrors($validator);
        }
        foreach ($vehicleQuestions as $qId => $question) {
            if ($qId == 8)
                continue;
            if (!isset($data['question_'.$qId])) {
                return redirect()->to($previousUrl)->withInput()->withErrors('Please answer for all required questions.');
            }
        }
        $data['activated']  = 0;
        $data['password']   = '';
        $driverRegistration = $userModel->saveDriverRegistrationData($data);
        if ($driverRegistration['status']) {
            return redirect()->to($previousUrl)->with('status', $driverRegistration['message']);
        } else {
            return redirect()->to($previousUrl)->withInput()->withErrors($driverRegistration['message']);
        }
    }
        
    /**
     * 
     * @param Request $request
     * @return type
     */
    public function saveContactUs(Request $request) {
        try {
            if ($request->isMethod('post')) {
                $contactus = new ContactUs();
                $saveData = $contactus->saveContactUs($request->all());
                if ($saveData['status']){
                    return redirect()->route('common.contact.us')->with('status', $saveData['message']);
                } else{
                    return redirect()->route('common.contact.us')->withInput()->withErrors($saveData['message']);
                }
            } else{
                return view('common.contact.us');
            }
        } catch (Exception $ex) {
            Log::error(__METHOD__ . $ex->getMessage());
            return view('errors.500');
        }
    }

}
