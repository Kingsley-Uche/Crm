<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Mail;
use App\Models\AdminModel;
use App\Mail\AdminPasswordReset;
use App\Models\BrandModel;

class AdminController extends Controller
{
    protected $redirectTo = 'admin/dashboard';

  public function __construct()
{
    // Load brand details and cache for 5 hours, store in session

    if (!session()->has('brand_details')) {

        $brand = BrandModel::first();
    

        if ($brand) {

            $brandData = [
                'name' => $brand->name,
                'description' => $brand->description,
                'logo_url' => $brand->logo_url,
                'brand_color' => $brand->brand_color,
                'website_url' => $brand->website_url,
                'contact_email' => $brand->contact_email,
                'contact_phone' => $brand->contact_phone,
                'address' => $brand->address,

                // SEO
                'meta_title' => $brand->meta_title,
                'meta_description' => $brand->meta_description,
                'meta_keywords' => $brand->meta_keywords,

                // OG
                'og_title' => $brand->og_title,
                'og_description' => $brand->og_description,
                'og_image' => $brand->og_image,

                // Twitter
                'twitter_title' => $brand->twitter_title,
                'twitter_description' => $brand->twitter_description,
                'twitter_image' => $brand->twitter_image,
            ];
        

            // cache for 5 hours (18000 seconds)
            cache()->put('brand_details', $brandData, 18000);

            // store in session for blade access
            session(['brand_details' => $brandData]);
        }
    }

    // Keep admin auth middleware
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
    protected function BrandDetails()
    {
        $brand = BrandModel::first();

        return $brand;
}
}