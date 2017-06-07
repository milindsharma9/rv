<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\ResetsPasswords;
use Illuminate\Http\Request;
use App\Http\Requests;
use App\User;
use DB;
use App\EventLog;
use Validator;
use Illuminate\Support\Facades\Password;
use App\Http\Helper\CommonHelper;
use App\Http\Helper\EmailForce24;

class PasswordController extends Controller
{
	//protected $redirectTo = '/dashboard';
    /*
    |--------------------------------------------------------------------------
    | Password Reset Controller
    |--------------------------------------------------------------------------
    |
    | This controller is responsible for handling password reset requests
    | and uses a simple trait to include this behavior. You're free to
    | explore this trait and override any methods you wish to tweak.
    |
    */

    use ResetsPasswords;
    
    /**
     * Where to redirect users after password reset.
     *
     * @var string
     */
    protected $redirectTo = '/logout';

    /**
     * Create a new password controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        //$this->middleware('guest');
    }

    /**
     * Reset the given user's password.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function postReset(Request $request) {
        $response = ['message' => 'Some error occured', 'status' => FALSE, 'data' => ''];
        DB::enableQueryLog();
        if (\Auth::check()) {
            $user = \Auth::user();
            $request = new Request(array_merge($request->all(), ['email' => $user['email']]));
            $userModel = new User();
            $validRules = $userModel->getChangePasswordValidation();
            $validator = Validator::make($request->all(), $validRules);
            if ($validator->fails()) {
                if ($request->get('requestAjax') == 1) {
                    $response['message'] = 'Validation Failed';
                    $response['data'] = $validator->messages();
                    return response()->json($response);
                } else {
                    return redirect()->back()->withInput()->withErrors($validator);
                }
            }
            $credentials = $request->only(
                    'email', 'password', 'password_confirmation'
            );
            $userstatus = $userModel->updateUserPassword($user['id'], $user['email'], $credentials['password']);


            /* Insert data to the log table for changepassword */
            $logData = array(
                'users_id' => $user['id'],
                'operation_type' => EventLog::EVENT_CHANGE_PASSWORD,
                'al_event' => serialize(DB::getQueryLog()),
            );
            EventLog::logEvent($logData);
            /* End */


            if ($userstatus['status']) {
                $message = trans('messages.password_change_success');
                $response['status'] = TRUE;
            } else {
                $message = trans('messages.user_invalid');
            }
            if ($request->get('requestAjax') == 1) {
                $response['message'] = $message;
                $response['data'] = $message;
                return response()->json($response);
            } else {
                return redirect()->back()->with('message', $message);
            }
        } else {
            if ($request->get('requestAjax') == 1) {
                return response()->json($response);
            }
            return redirect()->back();
        }
    }

    /**
     * Send a reset link to the given user. (Reset password functionality over ride here, to send custom email)
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\Response
     */
    public function sendResetLinkEmail(Request $request)
    {
        try {
            $this->validateSendResetLinkEmail($request);
            $email  = $request['email'];
            $user   = User::where('email', $email)->first();
            if (empty($user)) {
               return $this->getSendResetLinkEmailFailureResponse(Password::INVALID_USER);
            }
            DB::table('password_resets')->where('email', '=', $email)->delete();
            $token      = CommonHelper::getToken();
            $insertData = array(
                'email'         => $email,
                'token'         => $token,
                'created_at'    => CommonHelper::getCurrentDateTime(),
            );
            DB::table('password_resets')->insert($insertData); // Query Builder
            $firstName  = $user->first_name;
            $lastName   = $user->last_name;
            $link       = '';
            $link       = url('password/reset', $token).'?email=' . urlencode($user->getEmailForPasswordReset());
            $data = [
                'firstName'     => $firstName,
                'lastName'      => $lastName,
                'email'         => $email,
                'link'          => $link,
            ];
            EmailForce24::sendEmailAPI($data, EmailForce24::PASSWORD_RESET);
            return $this->getSendResetLinkEmailSuccessResponse(Password::RESET_LINK_SENT);
        } catch (Exception $ex) {
            //@todo log exception, if needed.
        }
        return $this->getSendResetLinkEmailFailureResponse(Password::INVALID_USER);
    }

}
