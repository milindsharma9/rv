<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Cache;

/**
 * SessionController 
 * 
 * PHP version 5.6
 * 
 * @category  Laravel 5.2
 * @package   SessionController
 * @copyright 2016 
 * @license   http://52.50.219.163/
 * @link      http://52.50.219.163/
 * 
 */
class SessionController extends Controller {

    /**
     * Show the Session available.
     *
     * @package SessionController
     * @param Request $request
     * @return \Illuminate\Http\Response
     */
    public function index(Request $request) {
        $session = $request->session()->all();
        dd($session);
    }

    /**
     * Clear available session.
     * 
     * @package SessionController
     * @param Request $request
     */
    public function clear(Request $request) {
        $request->session()->flush();
        Cache::flush();
    }

}
