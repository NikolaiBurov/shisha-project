<?php

namespace App\Exceptions;

use Illuminate\Http\Response;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Request;
use Illuminate\Support\Arr;
use App\Http\Services\ErrorService;
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
        $this->renderable(function (\Throwable $e, Request $request) {
            if ($request->is('api/*')) {
                return response()->json([
                    'status_code' => (new Response())->status(),
                    'error_message' => isset($e->validator) ? ErrorService::combineErrors(
                        Arr::flatten($e->validator->messages()->get('*'))
                    )
                        : $e->getMessage(),
                ]);
            }
        });
    }

    public function render($request, $e)
    {
        if ($this->isHttpException($e)) {
            $statusCode = $e->getStatusCode();

            return response()->make(view('partials/404_page'), $statusCode);
        }
        return parent::render($request, $e);
    }

}
