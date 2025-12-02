<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Models\AdminModel;
use App\Mail\AdminPasswordReset;

class AdminController extends Controller
{
    protected $redirectTo = 'admin/dashboard';

    public function __construct()
    {
        //$this->middleware('guest:admin')->only(['showLoginForm', 'login', 'passwordReset', 'emailPassword']);
        $this->middleware('auth:admin')->only('logout');
    }

    /**
     * Show admin login form.
     */
    public function showLoginForm()
    {
        if (Auth::guard('admin')->check()) {
            return redirect()->route('admin.dashboard');
        }

        return view('auth.login');
    }

    /**
     * Handle login request.
     */
    public function login(Request $request)
    {
        $request->validate([
            'email'    => 'required|email',
            'password' => 'required|min:6',
        ]);

        if (Auth::guard('admin')->attempt(
            ['email' => $request->email, 'password' => $request->password],
            $request->filled('remember')
        )) {
            return redirect()->intended($this->redirectTo);
        }

        return back()->withInput($request->only('email', 'remember'))->withErrors([
            'email' => 'The provided credentials do not match our records.',
        ]);
    }

    /**
     * Handle logout.
     */
    public function logout(Request $request)
    {
        Auth::guard('admin')->logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();

        return redirect()->route('admin.login');
    }

    /**
     * Show password reset form.
     */
    public function passwordReset(Request $request)
    {
        return view('auth.password_reset');
    }

    /**
     * Handle password reset email request.
     */
    public function emailPassword(Request $request)
    {
        $request->validate([
            'email' => 'required|email',
        ]);

        $admin = AdminModel::where('email', $request->email)->first();

        if ($admin) {
            $password = $this->generateRandomPassword();
            $admin->update(['password' => Hash::make($password)]);

            try {
                Mail::to($admin->email)->send(new AdminPasswordReset($admin, $password));
            } catch (\Exception $e) {
                \Log::error("Password reset email failed: " . $e->getMessage());
            }

            return redirect()->route('admin.login');
        }

        return redirect()->route('admin.login');
    }

    /**
     * Generate a random password.
     */
    protected function generateRandomPassword($length = 10)
    {
        $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789!@#$%^&*()-_=+[]{}|;:,.<>?';
        return substr(str_shuffle(str_repeat($chars, $length)), 0, $length);
    }
}
