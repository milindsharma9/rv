<?php

namespace App\Http\Middleware;
use Tymon\JWTAuth\Facades\JWTAuth;
use Tymon\JWTAuth\Exceptions\TokenInvalidException;
use Tymon\JWTAuth\Exceptions\JWTException;
use Tymon\JWTAuth\Exceptions\TokenExpiredException;
use App\Api\V1\Response\Response;
use Closure;

class authJWT {

    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next) {
        try {
            $cResponse = new Response($request); // to get customized response.
//            $user = JWTAuth::toUser($request->input('token')); // required if token is sent using post.
            if (!$user = JWTAuth::parseToken()->authenticate()) {
                $cResponse->setStatus(FALSE);
                $cResponse->setMessage(trans('messages.user_invalid'));
                $serviceResponse = $cResponse->getServiceResponse();
                return response()->make($serviceResponse);
            }
        } catch (TokenInvalidException $e) {
            // to get customized response in case of invalid token exception.
            $cResponse->setStatus(FALSE);
            $cResponse->setMessage(trans('messages.token_invalid'));
            $serviceResponse = $cResponse->getServiceResponse();
            return response()->make($serviceResponse);
        } catch (TokenExpiredException $e) {
            $cResponse->setStatus(FALSE);
            $cResponse->setMessage($e->getMessage());
            $serviceResponse = $cResponse->getServiceResponse();
            return response()->make($serviceResponse);
        } catch (JWTException $e) {
            $cResponse->setStatus(FALSE);
            $cResponse->setMessage($e->getMessage());
            $serviceResponse = $cResponse->getServiceResponse();
            return response()->make($serviceResponse);
        } catch (Exception $e) {
            $cResponse->setStatus(FALSE);
            $cResponse->setMessage($e->getMessage());
            $serviceResponse = $cResponse->getServiceResponse();
            return response()->make($serviceResponse);
        }

        return $next($request);
    }

}
