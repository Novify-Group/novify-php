<?php

namespace App\Services;

use App\Models\Branch;
use App\Models\Merchant;
use App\Models\MerchantUser;
use App\Traits\ApiResponse;
use Illuminate\Support\Facades\Hash;
use App\Helpers\ImageHelper;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Auth;

class MerchantUserService
{
    use ApiResponse;

    public function createUser(Merchant $merchant, array $data): array
    {
        // Handle image uploads
        $photoPath = ImageHelper::saveBase64Image($data['photo'] ?? null, 'user_photos');
        $idPicturePath = ImageHelper::saveBase64Image($data['id_picture'] ?? null, 'user_ids');

        $user = $merchant->users()->create([
            'branch_id' => $data['branch_id'] ?? null,
            'gender' => $data['gender'] ?? null,
            'dob' => $data['dob'] ?? null,  
            'first_name' => $data['first_name'],
            'last_name' => $data['last_name'],
            'phone_number' => $data['phone_number'],
            'email' => $data['email'],
            'password' => Hash::make($data['password']),
            'photo_path' => $photoPath,
            'id_picture_path' => $idPicturePath,
            'role' =>$data['role'],
            'force_password_change' => true // New users must change password on first login
        ]);

        return $this->successResponse(
            ['user' => $user],
            'User created successfully',
            201
        );
    }

    public function updateUser(MerchantUser $user, array $data): array
    {
        // Handle image uploads if provided
        if (isset($data['photo'])) {
            $data['photo_path'] = ImageHelper::saveBase64Image($data['photo'], 'user_photos');
            unset($data['photo']);
        }

        if (isset($data['id_picture'])) {
            $data['id_picture_path'] = ImageHelper::saveBase64Image($data['id_picture'], 'user_ids');
            unset($data['id_picture']);
        }

        if (isset($data['password'])) {
            $data['password'] = Hash::make($data['password']);
            $data['force_password_change'] = false; // Reset force_password_change when user changes password
        }

        $user->update($data);

        return $this->successResponse([
            'user' => $user->fresh()
        ], 'User updated successfully');
    }

    public function listUsers(Merchant $merchant, string $role=null, int $branchId = null,int $perPage = 20): array
    {
        $users = $merchant->users()
            ->when($role, function ($query) use ($role) {
                $query->where('role', $role);
            })
            ->when($branchId, function ($query) use ($branchId) {
                $query->where('branch_id', $branchId);
            })
            ->with('branch')
            ->paginate($perPage);

        return $this->successResponse($users);
    }

    public function listUsersByBranch(Branch $branch, ?string $role = null, int $perPage = 20): array
    {
        $query = $branch->users()->with('branch');
        
        if ($role) {
            $query->where('role', $role);
        }

        $users = $query->paginate($perPage);

        return $this->successResponse([
            'users' => $users,
            'branch' => $branch->only(['id', 'name', 'city'])
        ]);
    }

    public function toggleStatus(MerchantUser $user): array
    {
        if (!$user->exists) {
            throw new \Exception('User not found');
        }

        $user->update([
            'is_active' => !$user->is_active
        ]);

        return $this->successResponse([
            'user' => $user->fresh()
        ], 'User status updated successfully');
    }

    public function resetPassword(MerchantUser $user): array
    {
        if (!$user->exists) {
            throw new \Exception('User not found');
        }

        $newPassword = Str::random(8); // Generate a random password
        
        $user->update([
            'password' => Hash::make($newPassword),
            'force_password_change' => true // Force user to change password after reset
        ]);

        return $this->successResponse([
            'password' => $newPassword
        ], 'Password reset successfully');
    }

    public function login(array $credentials): array
    {
        $loginField = filter_var($credentials['username'], FILTER_VALIDATE_EMAIL) ? 'email' : 'phone_number';
    
        $credentials = [
            $loginField => $credentials['username'],
            'password' => $credentials['password']
        ];

        if (!$token = auth('branch_user')->attempt($credentials)) {
            return $this->errorResponse('Login Failed. Invalid credentials', 401);
        }
    
        $user = auth('branch_user')->user();

        if (!$user) {
            return $this->errorResponse('No user found with such credentials', 401);
        }

        if (!$user->is_active) {
            return $this->errorResponse('Account is inactive', 401);
        }

        if (!Hash::check($credentials['password'], $user->password)) {
            return $this->errorResponse('Invalid credentials', 401);
        }

        $user['branch'] = $user->branch->only(['id','name','city']);
        $user['merchant'] = $user->merchant->only(['id','store_name','store_logo_path','merchant_number','country_id','market_area_id','wallets']);
       
        return $this->successResponse([
            'user' => $user,
            'token' => $token,
            'force_password_change' => $user->force_password_change
        ], 'Login successful');
    }
} 