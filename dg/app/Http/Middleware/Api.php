<?php

namespace App\Http\Middleware;

use Closure;

use App\Api\V1\Response\Response;

class Api
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
     * Handle an incoming request for API.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $aRequestHeadersKeys = array_keys($request->header());
        foreach ($this->requiredHeaders as $header) {
            if (!in_array($header, $aRequestHeadersKeys)) {
                $apiResponse = new Response($request);
                $apiResponse->setMessage('Unauthorized. Required Headers Missing.');
                return response()->make($apiResponse->getServiceResponse()); 
            }
        }
        return $next($request);
    }
}
