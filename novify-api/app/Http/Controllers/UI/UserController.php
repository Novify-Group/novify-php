<?php

namespace App\Http\Controllers\UI;

use App\Http\Controllers\Controller;
use App\Models\MerchantUser;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class UserController extends Controller
{
    /**
     * Display a listing of merchant users
     */
    public function index()
    {
        $merchant = Auth::user();
        $users = MerchantUser::where('merchant_id', $merchant->id)
            ->with('branch')
            ->latest()
            ->paginate(20);
        
        return view('templates.users.index', compact('users'));
    }

    /**
     * Show the form for creating a new merchant user
     */
    public function create()
    {
        $merchant = Auth::user();
        $branches = $merchant->branches()->where('is_active', true)->get();
        
        return view('templates.users.create', compact('branches'));
    }

    /**
     * Store a newly created merchant user
     */
    public function store(Request $request)
    {
        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:merchant_users,email',
            'phone_number' => 'required|string|max:20',
            'role' => 'required|in:attendant,manager,admin',
            'branch_id' => 'nullable|exists:branches,id',
            'password' => 'required|string|min:8|confirmed',
        ]);

        $merchant = Auth::user();
        $user = MerchantUser::create([
            'merchant_id' => $merchant->id,
            'first_name' => $request->first_name,
            'last_name' => $request->last_name,
            'email' => $request->email,
            'phone_number' => $request->phone_number,
            'role' => $request->role,
            'branch_id' => $request->branch_id,
            'password' => Hash::make($request->password),
            'is_active' => true,
        ]);

        return redirect()->route('ui.users.index')
            ->with('success', 'User created successfully');
    }

    /**
     * Display the specified merchant user
     */
    public function show(MerchantUser $user)
    {
        // Ensure the user belongs to the authenticated merchant
        if ($user->merchant_id !== Auth::id()) {
            abort(403);
        }

        $user->load('branch');
        return view('templates.users.show', compact('user'));
    }

    /**
     * Show the form for editing the specified merchant user
     */
    public function edit(MerchantUser $user)
    {
        // Ensure the user belongs to the authenticated merchant
        if ($user->merchant_id !== Auth::id()) {
            abort(403);
        }

        $merchant = Auth::user();
        $branches = $merchant->branches()->where('is_active', true)->get();
        
        return view('templates.users.edit', compact('user', 'branches'));
    }

    /**
     * Update the specified merchant user
     */
    public function update(Request $request, MerchantUser $user)
    {
        // Ensure the user belongs to the authenticated merchant
        if ($user->merchant_id !== Auth::id()) {
            abort(403);
        }

        $request->validate([
            'first_name' => 'required|string|max:255',
            'last_name' => 'required|string|max:255',
            'email' => 'required|email|max:255|unique:merchant_users,email,' . $user->id,
            'phone_number' => 'required|string|max:20',
            'role' => 'required|in:attendant,manager,admin',
            'branch_id' => 'nullable|exists:branches,id',
            'password' => 'nullable|string|min:8|confirmed',
        ]);

        $data = $request->except('password');
        
        if ($request->filled('password')) {
            $data['password'] = Hash::make($request->password);
        }

        $user->update($data);

        return redirect()->route('ui.users.index')
            ->with('success', 'User updated successfully');
    }

    /**
     * Remove the specified merchant user
     */
    public function destroy(MerchantUser $user)
    {
        // Ensure the user belongs to the authenticated merchant
        if ($user->merchant_id !== Auth::id()) {
            abort(403);
        }

        // Prevent deletion of own account
        if ($user->id === Auth::id()) {
            return back()->with('error', 'Cannot delete your own account');
        }

        $user->delete();

        return redirect()->route('ui.users.index')
            ->with('success', 'User deleted successfully');
    }

    /**
     * Toggle user status (active/inactive)
     */
    public function toggleStatus(MerchantUser $user)
    {
        // Ensure the user belongs to the authenticated merchant
        if ($user->merchant_id !== Auth::id()) {
            abort(403);
        }

        // Prevent deactivating own account
        if ($user->id === Auth::id()) {
            return back()->with('error', 'Cannot deactivate your own account');
        }

        $user->update(['is_active' => !$user->is_active]);

        $status = $user->is_active ? 'activated' : 'deactivated';
        return back()->with('success', "User {$status} successfully");
    }

    /**
     * Reset user password
     */
    public function resetPassword(MerchantUser $user)
    {
        // Ensure the user belongs to the authenticated merchant
        if ($user->merchant_id !== Auth::id()) {
            abort(403);
        }

        $newPassword = 'password123'; // Default password
        $user->update(['password' => Hash::make($newPassword)]);

        return back()->with('success', "Password reset successfully. New password: {$newPassword}");
    }
}
