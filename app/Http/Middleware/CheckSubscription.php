<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Support\Facades\Auth;

class CheckSubscription
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        // 1. Ensure the user is authenticated via the admin guard
        if (!Auth::guard('admin')->check()) {
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Unauthorized.'], 401);
            }

            return redirect()->route('admin.login');
        }

        // 2. Safely grab the authenticated admin user using the correct guard
        $user = Auth::guard('admin')->user();
        
        // 3. Safely fetch session data using the null-coalescing operator
        $subscription = session('subscription_status');
    
        $status = $subscription['status'] ?? null;


        // 4. Validate Site Admin status AND active subscription
        if ((int) $user->is_site_admin !== 1 && (bool) $status !== true) {
             if ($request->expectsJson()) {
                return response()->json(['message' => 'Subscription required.'], 403);
            }
            if ($request->expectsJson()) {
                return response()->json(['message' => 'Subscription required.'], 403);
            }

             // Redirect API clients to a JSON response
             return response()->json(['message' => 'An active subscription is required.'], 403);
             
             // Redirect web browser users to a friendly page (adjust route name as needed)
             return redirect()->route('admin.dashboard')->with('error', 'An active subscription is required.');
            // Redirect web browser users to a friendly page (adjust route name as needed)
            return redirect()->route('admin.dashboard')->with('error', 'An active subscription is required.');
        }

        return $next($request);
    }
}