<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Support\Facades\Auth;

class AuthenticateApi
{
    /*
     * Suggested Header Values :
     * Content-Type = application/json,
     * Accept = application/json
     */
    
    protected $requiredHeaders = [
        'alchemy-device-id',
        'alchemy-device-type',
        'alchemy-auth-token',
    ];

    /**
     * Handle an incoming request.
     * 
     * API Routes which needs Authenticated users.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @param  string|null  $guard
     * @return mixed
     */
    public function handle($request, Closure $next, $guard = null)
    {
        if (Auth::guard($guard)->guest()) {
            if ($request->ajax() || $request->wantsJson()) {
                return response('Unauthorized.', 401);
            } else {
                return redirect()->guest('login');
            }
        }
        if ($request->wantsJson()) {
            $aRequestHeadersKeys = array_keys($request->header());
            foreach ($this->requiredHeaders as $header) {
                if (!in_array($header, $aRequestHeadersKeys)) {
                    return response('Unauthorized. Required Headers Missing', 401);
                }
            }
        }
        return $next($request);
    }
}
