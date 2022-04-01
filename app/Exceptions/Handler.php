<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->renderable(function (\Throwable $e, $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'message' => 'Verification Failed'
                ], 404);
            }
        });

        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, $e)
    {
        if ($this->isHttpException($e)) {

            $statusCode = $e->getStatusCode();

            return response()->make(view('partials/404_page'),$statusCode);

//            return match ($statusCode) {
//                404 => response()->make(view('partials/404_page'),$statusCode),
//                default =>  response()->make(view('partials/404_page'),$statusCode)
//            };
        }
        return parent::render($request, $e);
    }

}
