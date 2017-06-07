<?php

namespace App;

use Illuminate\Database\Eloquent\Model;
use Log;
use Illuminate\Http\Request;
/**
 * Session Model
 */
class SessionModel extends Model {

    /**
     * Check if varible exist in session.
     * 
     * @package Session Model
     * @param string $sessionVariable
     * @return boolean.
     */
    public static function checkInSession($sessionVariable) {
        if (\Session::has($sessionVariable)) {
            Log::info('SessionModel::Variable Found in Session checkInSession()');
            return TRUE;
        }
        Log::error('SessionModel::Variable Not Found checkInSession()');
        return FALSE;
    }

    /**
     * Return session value of variable.
     * 
     * @package Session Model
     * @param string $sessionVariable
     * @return boolean
     */
    public static function getSessionVariable($sessionVariable) {
        if (self::checkInSession($sessionVariable)) {
            Log::info('SessionModel::getSessionVariable()');
            return \Session::get($sessionVariable);
        }
        return FALSE;
    }

    /**
     * Forget Session variable.
     * 
     * @package Session Model
     * @param string $sessionVariable
     * @return boolean
     */
    public static function forgetSessionVariable($sessionVariable) {
        if (self::checkInSession($sessionVariable)) {
            Log::info('SessionModel::forgetSessionVariable()');
            return \Session::forget($sessionVariable);
        }
        return FALSE;
    }

    /**
     * Setting a variable in session
     * 
     * @package Session Model
     * @param string $sessionKey
     * @param string|array $sessionValue
     * @return void
     */
    public static function putSessionVariable($sessionKey, $sessionValue) {
        \Session::put($sessionKey, $sessionValue);
        return;
    }

}
