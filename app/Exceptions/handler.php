<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Http\Request;

class Handler extends ExceptionHandler
{
    protected $levels = [
        // 
    ];

    protected $dontReport = [
        //
    ];

    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    // This is the key method to customize
   protected function unauthenticated(
    Request $request,
    AuthenticationException $exception
) {
    if ($request->is('admin') || $request->is('admin/*')) {
        return redirect()->guest(route('admin.login'));
    }

    return redirect()->guest('/login');
}
}
