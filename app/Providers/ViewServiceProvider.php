<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use App\Http\Controllers\NoficationController;
use App\Http\Controllers\UserManagerController;

use Illuminate\Support\Facades\Session;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Compose all views
        View::composer('*', function ($view) {
            $user = Auth::user();

            if ($user) {
                // Get user permissions
                $permissionsController = new UserManagerController();
                $userPermissions = $permissionsController->accessControl($user);
                Session::put('permissions', $userPermissions);

                // Get notifications
                $notificationController = new NoficationController();
                $notifications = [
                    'repairs' => $notificationController->checkDueRepairs(),
                    'park_permits' => $notificationController->checkParkPermits(),
                    'pest_control' => $notificationController->checkPestControl(),
                ];

                // Share data with all views
                $view->with([
                    'user_logged_in' => $user,
                    'notifications' => $notifications,
                    'userPermissions' => $userPermissions,
                ]);
            }
        });
    }

    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }
}
