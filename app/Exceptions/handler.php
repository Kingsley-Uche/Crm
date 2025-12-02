<?php

namespace App\Exceptions;

use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;

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
    protected function unauthenticated($request, AuthenticationException $exception)
    {
        dd($request);
        // Check if the route prefix is 'admin'
        if ($request->is('admin') || $request->is('admin/*')) {
            return redirect()->guest(route('admin.login'));
        }

        // Default fallback
        return redirect()->guest('/login');
    }
}
