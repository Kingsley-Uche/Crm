<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Foundation\Auth\AuthenticatesUsers;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class LoginController extends Controller
{
    use AuthenticatesUsers;

    /**
     * Where to redirect users after login.
     *
     * @var string
     */
    protected $redirectTo = '/user/dashboard';

    /**
     * Create a new controller instance.
     */
    public function __construct()
    {
        $this->middleware('guest')->except('logout');
    }

    /**
     * Specify the authentication guard.
     */
    protected function guard()
    {
        return Auth::guard('web');
    }

    /**
     * Show the login form.
     */
    public function showLoginForm()
    {
        return view('user.login');
    }

    /**
     * Handle login request.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
            'password' => 'required|string|min:6',
        ]);

        $credentials = $request->only('email', 'password');

        if ($this->guard()->attempt($credentials, $request->filled('remember'))) {
            $user = $this->guard()->user();
        
             Log::info('User logged in: ' . $user->email);

             // Mark email as verified if not already
             if (is_null($user->email_verified_at)) {
                $user->update(['email_verified_at' => now()]);
            }

            if (is_null($user->email_verified_at)) {
                $user->update(['email_verified_at' => now()]);
            }

            return redirect()->intended($this->redirectTo);
        }


        return back()->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ])->withInput($request->only('email', 'remember'));
    }

    /**
     * Log the user out of the application.
     */
    public function logout(Request $request)
    {
        $this->guard()->logout();

        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('user.login');

        //return redirect()->route('user.login')->with('status', 'You have been logged out successfully.');
    }
    protected function redirectTo()
{
    return route('user.login');
}
}
