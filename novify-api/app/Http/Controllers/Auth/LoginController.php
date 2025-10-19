<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\Merchant;
use App\Models\MerchantUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\ValidationException;

class LoginController extends Controller
{
    /**
     * Show the login form
     */
    public function showLoginForm()
    {
        if (Auth::check()) {
            return redirect()->route('ui.dashboard');
        }
        
        return view('auth.login');
    }

    /**
     * Handle login attempt
     */
    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
            'user_type' => 'required|in:merchant,user',
        ]);

        $credentials = $request->only(['username', 'password']);
        $userType = $request->user_type;

        if ($userType === 'merchant') {
            return $this->attemptMerchantLogin($credentials);
        } else {
            return $this->attemptUserLogin($credentials);
        }
    }

    /**
     * Attempt merchant login
     */
    private function attemptMerchantLogin(array $credentials)
    {
        $loginField = filter_var($credentials['username'], FILTER_VALIDATE_EMAIL) ? 'email' : 'phone_number';
        
        $merchant = Merchant::where($loginField, $credentials['username'])
            ->where('is_active', true)
            ->first();

        if (!$merchant || !Hash::check($credentials['password'], $merchant->password)) {
            throw ValidationException::withMessages([
                'username' => ['The provided credentials are incorrect.'],
            ]);
        }

        if (!$merchant->is_verified) {
            throw ValidationException::withMessages([
                'username' => ['Your account is not verified yet. Please contact support.'],
            ]);
        }

        Auth::login($merchant);
        return redirect()->intended(route('ui.dashboard'));
    }

    /**
     * Attempt user (attendant/manager) login
     */
    private function attemptUserLogin(array $credentials)
    {
        $loginField = filter_var($credentials['username'], FILTER_VALIDATE_EMAIL) ? 'email' : 'phone_number';
        
        $user = MerchantUser::where($loginField, $credentials['username'])
            ->where('is_active', true)
            ->first();

        if (!$user || !Hash::check($credentials['password'], $user->password)) {
            throw ValidationException::withMessages([
                'username' => ['The provided credentials are incorrect.'],
            ]);
        }

        if (!$user->merchant->is_active) {
            throw ValidationException::withMessages([
                'username' => ['Your merchant account is not active. Please contact support.'],
            ]);
        }

        Auth::login($user);
        return redirect()->intended(route('ui.dashboard'));
    }

    /**
     * Logout the user
     */
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        
        return redirect()->route('login');
    }
}
