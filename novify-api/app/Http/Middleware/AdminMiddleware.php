<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class AdminMiddleware
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        // Check if user is authenticated
        if (!Auth::check()) {
            return redirect()->route('login');
        }

        // Check if user is an admin
        $user = Auth::user();
        
        if (!$this->isAdmin($user)) {
            abort(403, 'Access denied. Admin privileges required.');
        }

        return $next($request);
    }

    /**
     * Check if user has admin privileges
     */
    private function isAdmin($user)
    {
        // Check if user is a super admin or has admin privileges
        // For now, we'll check if the user is a super admin
        
        // Option 1: Check if user has admin role
        if (method_exists($user, 'hasRole')) {
            return $user->hasRole('admin') || $user->hasRole('super_admin');
        }
        
        // Option 2: Check if user is a super admin (first user or specific admin)
        if (property_exists($user, 'is_super_admin')) {
            return $user->is_super_admin;
        }
        
        // Option 3: Check if user ID is 1 (first user - super admin)
        if ($user->id === 1) {
            return true;
        }
        
        // Option 4: Check if user email is admin email
        $adminEmails = ['admin@novify.com', 'superadmin@novify.com'];
        if (in_array($user->email, $adminEmails)) {
            return true;
        }
        
        // Option 5: Check if user is a merchant with admin privileges
        if (method_exists($user, 'merchant') && $user->merchant) {
            // You can add merchant-specific admin logic here
            // For example, check if merchant has admin subscription
            return false; // Merchants are not admins by default
        }
        
        return false;
    }
}
