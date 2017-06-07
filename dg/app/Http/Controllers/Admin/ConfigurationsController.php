<?php

namespace App\Http\Controllers\Admin;

use Illuminate\Http\Request;
use Validator;
use Illuminate\Support\Facades\Redirect;
use App\Http\Requests;
use App\Http\Controllers\Controller;
use Exception;
use App\Configurations;
use Auth;
use App\StoreModel;
use App\User;
use App\PartnerAvailability;
use Log;

class ConfigurationsController extends Controller {

    private $storeModel = NULL;

    /**
     * Function to Instatntiate Store Model.
     * 
     * @package StoreController
     * @return object App\StoreModel Model
     */
    private function getStoreModel() {
        if ($this->storeModel == NULL) {
            $this->storeModel = new StoreModel();
        }
        return $this->storeModel;
    }

    /**
     * Show the form for editing the specified resource.
     *
     * @param  int  $id
     * @return Response
     */
    public function manage(Request $request) {
        $configModel = new Configurations();
        $aConfigurations = $configModel->getConfigurations();
        if ($request->isMethod('post')) {
            foreach ($request->all() as $key => $value) {
                if (empty($value)) {
                    return redirect()->back()->withInput($request->all())->withErrors(trans('admin/configurations.all_required_message'));
                }
            }
            $inputRequest = $request->all();
            unset($inputRequest['_token']);
            $configModel->saveConfigurations($inputRequest);
            \Session::flash('message', trans('admin/configurations.success_message'));
            $aConfigurations = $configModel->getConfigurations();
        }
        return view('admin.configurations.manage', compact('aConfigurations'));
    }

    /**
     * Method to get store available timings.
     * 
     * @return resources/views/store mixed
     */
    public function getSiteTime() {
        try {
            $storeId = config('appConstants.admin_role_id'); // Use Role Instead of user id. Multiple admin users case.
            $timings = array();
            $time = \Config::get('appConstants.site_timings');
            $days = \Config::get('appConstants.store_days');
            return view('admin.configurations.site-availability', compact('timings', 'time', 'days', 'storeId'));
        } catch (Exception $ex) {
            return redirect()->route('admin.site.time')->with('warning', trans('messages.common_error'));
        }
    }

    /**
     * Method to update store time in database.
     * 
     * @param Request $request
     * @return resources/views/store mixed
     */
    public function updateSiteTime(Request $request) {
        try {
            $adminRoleId    = isset(Auth::user()['fk_users_role']) ? Auth::user()['fk_users_role'] : FALSE;
            $storeId        = $request['storeId'];
            $this->getStoreModel()->updateSiteTimeEntries($storeId, $request->all());
            $message = trans('messages.store_timings_updated');
            \Session::flash('message', $message);
            if ($adminRoleId == config('appConstants.admin_role_id')) {
                return redirect()->route('admin.configurations.manage')->with('status', $message);
            } else {
                return redirect()->route('admin.vendors.edit', $storeId)->with('status', $message);
            }
        } catch (Exception $ex) {
            return redirect()->route('admin.configurations.manage')->with('warning', trans('messages.common_error'));
        }
    }

    /**
     * Method to get Site Opening info by Ajax
     * 
     * @param Request $request
     * @return resources/views/store mixed
     */
    public function getSiteOpeningInfo(Request $request) {
        try {
            if ($request->isMethod('post')) {
                $storeId = $request['StoreId'];
                $time = \Config::get('appConstants.site_timings');
                $days = \Config::get('appConstants.store_days');
                $theDay = $request['theDay'];
                $storeData = $this->getStoreModel()->getTimings($storeId, $theDay);
                $storeTimings = $storeData['data'];
                $timings = array();
                if (!empty($storeTimings)) {
                    foreach ($storeTimings as $key => $value) {
                        $tem_arr = $value->open_time . "-" . $value->close_time;
                        $timings['schedule'][] = $tem_arr;
                        $timings['is_24hrs'] = $value->is_24hrs;
                        $timings['is_closed'] = $value->is_closed;
                        $timings['theDay'] = $value->day;
                    }
                } else {
                    $timings['schedule'][] = array();
                    $timings['is_24hrs'] = 0;
                    $timings['is_closed'] = 0;
                    $timings['theDay'] = $theDay;
                }
                return view('admin.partials.site-availability-data', compact('timings', 'time', 'days', 'storeId'));
            }
        } catch (Exception $ex) {
            return redirect()->route('admin.configurations.manage')->with('warning', trans('messages.common_error'));
        }
    }

    /**
     * Method to get store available timings.
     * 
     * @param $storeId
     * 
     * @return resources/views/store mixed
     */
    public function getStoreTime($storeId) {
        try {
            $timings = array();
            $time = \Config::get('appConstants.site_timings');
            $days = \Config::get('appConstants.store_days');
            //$vendors = User::getSystemVendors($storeId);
            $vendors = User::getSystemVendorsForEdit($storeId);
            return view('admin.configurations.site-availability', compact('timings', 'time', 'days', 'storeId', 'vendors'));
        } catch (Exception $ex) {
            //echo $ex->getMessage();
            return redirect()->route('admin.site.time')->with('warning', trans('messages.common_error'));
        }
    }

    /**
     * Method to render Tookan Availability Form
     * 
     * @param Request $request
     * @return view
     */
    public function showTokanForm(Request $request) {
        return view('admin.configurations.tookan_availability');
    }

    /**
     * Method to update tookan availability in database.
     * 
     * @param Request $request
     * @return resources/views/store mixed
     */
    public function updateTokanData(Request $request) {
        try {
            PartnerAvailability::updateTookanTimeEntries($request->all());
            $message = trans('messages.store_timings_updated');
            \Session::flash('message', $message);
            return redirect()->back()->with('status', $message);
        } catch (Exception $ex) {
            return redirect()->back()->with('warning', trans('messages.common_error'));
        }
    }

    /**
     * Method to get Tookan Availability info by Ajax
     * 
     * @param Request $request
     * @return resources/views/store mixed
     */
    public function getTookanAvailability(Request $request) {
        $response = [
            'status' => false,
            'html_content' => "",
        ];
        try {
            $time           = config('appConstants.site_timings');
            $theDay         = $request['theDay'];
            $storeData      = PartnerAvailability::getTookanAvailability($theDay);
            $storeTimings   = $storeData['data'];
            $timings        = array();
            if (!empty($storeTimings)) {
                foreach ($storeTimings as $key => $value) {
                    $tem_arr = $value->open_time . "-" . $value->close_time;
                    $timings['schedule'][$tem_arr] = $value->task_type;
                    $timings['task_type'] = $value->task_type;
                }
            } else {
                $timings['schedule'][] = array();
            }
            $view = view('admin.partials.tookan-availability', 
                [
                    'time' => $time,
                    'timings' => $timings,
                ]
            );
            $view               = $view->render();
            $response['status'] = true;
            $response['html_content'] = (string) $view;
        } catch (Exception $ex) {
            Log::error(__METHOD__ . $ex->getMessage() . "|" . $ex->getLine());
        }
        return $response;
    }

}
