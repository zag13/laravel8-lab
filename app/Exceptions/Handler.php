<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
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
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    public function render($request, Throwable $e)
    {
        if ($e instanceof AuthenticationException) {
            return response()->json(['message' => $e->getMessage() ?: 'Unauthenticated'], 401);
        } elseif ($e instanceof AccessDeniedHttpException) {
            return response()->json(['message' => $e->getMessage() ?: '未通过接口校验'], 403);
        } else {
            return response()->json([
                'code' => $e->getCode() ?: 0,
                'msg' => $e->getMessage() ?: 'not found',
            ]);
        }
    }
}
