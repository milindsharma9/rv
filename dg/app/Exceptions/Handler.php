<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Validation\ValidationException;
use Illuminate\Auth\Access\AuthorizationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Response;
use App\Api\V1\Response\Response as ApiResponse;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that should not be reported.
     *
     * @var array
     */
    protected $dontReport = [
        AuthorizationException::class,
        HttpException::class,
        ModelNotFoundException::class,
        ValidationException::class,
    ];

    /**
     * Report or log an exception.
     *
     * This is a great spot to send exceptions to Sentry, Bugsnag, etc.
     *
     * @param  \Exception  $e
     * @return void
     */
    public function report(Exception $e)
    {
        parent::report($e);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $e
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $e)
    {
        if ($request->is('api/*') && $request->wantsJson()) {
            $cResponse = new ApiResponse($request); // to get customized response.
            $response = [
                'message' => (string) $e->getMessage(),
                'status' => 400,
                'debug' => [],
            ];
 
            if ($e instanceof HttpException) {
                $response['message'] = Response::$statusTexts[$e->getStatusCode()];
                $response['status'] = $e->getStatusCode();
            } else if ($e instanceof ModelNotFoundException) {
                $response['message'] = Response::$statusTexts[Response::HTTP_NOT_FOUND];
                $response['status'] = Response::HTTP_NOT_FOUND;
            }
 
            if ($this->isDebugMode()) {
                $response['debug'] = [
                    'exception' => get_class($e),
                    'trace' => $e->getTrace()
                ];
            }
            //return response()->json(['status' => false, 'error' => $response], $response['status']);
            $cResponse->setStatus(FALSE);
            $cResponse->setMessage(trans('messages.common_error'));
            $cResponse->setDeveloperMessage($response['message']);
            $cResponse->setDebugTraceMessage($response['debug']);
            $serviceResponse = $cResponse->getServiceResponse();
            return response()->make($serviceResponse);
        }
        return parent::render($request, $e);
    }
    
    /**
     * Determine if the application is in debug mode.
     *
     * @return Boolean
     */
    public function isDebugMode()
    {
        return (boolean) env('APP_DEBUG');
    }
}
