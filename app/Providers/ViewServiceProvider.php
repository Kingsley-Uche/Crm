<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\View;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\NoficationController;
use App\Http\Controllers\UserManagerController;

class ViewServiceProvider extends ServiceProvider
{
    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        View::composer(['layouts.dashboard.landpage'], function ($view) {
            $user = Auth::user();

            if ($user) {
                // 1. Optimize Permissions & Subscription: Check session first
                $userPermissions = session('permissions');
                $subscriptionStatus = session('subscription_status');

                // If either is missing from the session, fetch them both
                if (!$userPermissions || !$subscriptionStatus) {
                    $permissionsController = app(UserManagerController::class);
                    
                    $userPermissions = $permissionsController->accessControl($user);
                    $subscriptionStatus = $permissionsController->checkSubscriptionStatus($user);
                    
                    session([
                        'permissions' => $userPermissions,
                        'subscription_status' => $subscriptionStatus
                    ]);
                }

                // 2. Optimize Notifications: Cache queries for 5 minutes
                $notifications = Cache::remember("user_notifications_{$user->id}", 300, function () {
                    $notificationController = app(NoficationController::class);
                    return [
                        'repairs'      => $notificationController->checkDueRepairs(),
                        'park_permits' => $notificationController->checkParkPermits(),
                        'pest_control' => $notificationController->checkPestControl(),
                    ];
                });

                // 3. Share data cleanly (Safely handle the ['data'] array key)
                $view->with([
                    'user_logged_in'      => $user,
                    'notifications'       => $notifications,
                    'userPermissions'     => $userPermissions,
                    'subscription_status' => $subscriptionStatus,
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