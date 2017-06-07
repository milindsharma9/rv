<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\User;
use Hash;
use JWTAuth;
use Validator;

class APIController extends Controller {

    public function register(Request $request) {

        $input = $request->all();

        $input['password'] = Hash::make($input['password']);

        User::create($input);

        return response()->json(['result' => true]);
    }

    public function login(Request $request) {

        $input = $request->all();
        $rules = array(
            'email' => 'required|email|max:255',
            'password' => 'required|min:4',
        );
        $validator = Validator::make($input, $rules);
        if ($validator->fails()) {
            return response()->json(['status' => 'error', 'result' => $validator->errors()->all()]);
        } else {
            if (!$token = JWTAuth::attempt($input)) {

                return response()->json(['status' => 'error', 'result' => 'wrong email or password.']);
            }

            return response()->json(['status' => 'success', 'result' => $token]);
        }
    }

    public function get_user_details(Request $request) {

        $input = $request->all();

        $user = JWTAuth::toUser($input['token']);

        return response()->json(['result' => $user]);
    }

}
