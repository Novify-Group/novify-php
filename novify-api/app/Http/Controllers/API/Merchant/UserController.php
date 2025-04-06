<?php

namespace App\Http\Controllers\API\Merchant;

use App\Http\Controllers\API\BaseApiController;
use App\Models\MerchantUser;
use App\Services\MerchantUserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Branch;

class UserController extends BaseApiController
{
    protected $userService;

    public function __construct(MerchantUserService $userService)
    {
        $this->userService = $userService;
    }

    public function storeAttendant(Request $request): JsonResponse
    {
        return $this->execute(function () use ($request) {
            $request->validate([
                'branch_id' => 'required|exists:branches,id',
                'first_name' => 'required|string|max:255',
                'last_name' => 'required|string|max:255',
                'phone_number' => 'required|string|unique:merchant_users',
                'email' => 'nullable|email|unique:merchant_users',
                'password' => 'required|min:8',
                'role' => 'required|in:ATTENDANT,DISTRIBUTOR',
                'gender' => 'nullable|in:Male,Female,Other',
                'dob' => 'nullable|date',
                'photo' => 'nullable|string', // base64
                'id_picture' => 'nullable|string', // base64
            ]);

            return $this->userService->createUser($request->user(), $request->all());
        });
    }

    public function listAttendants(Request $request): JsonResponse
    {

        return $this->execute(function () use ($request) {
            $perPage = $request->input('per_page', 20);
            return $this->userService->listUsers($request->user(), $request->role, $request->branchId, $perPage);
        });
    }

    public function listDistributors(Request $request): JsonResponse
    {
        return $this->execute(function () use ($request) {
            $perPage = $request->input('per_page', 20);
            return $this->userService->listUsers($request->user(), 'DISTRIBUTOR', $perPage);
        });
    }

    public function update(Request $request, MerchantUser $user): JsonResponse
    {
        return $this->execute(function () use ($request, $user) {
            $request->validate([
                'branch_id' => 'sometimes|exists:branches,id',
                'first_name' => 'sometimes|string|max:255',
                'last_name' => 'sometimes|string|max:255',
                'phone_number' => 'sometimes|string|unique:merchant_users,phone_number,' . $user->id,
                'email' => 'sometimes|nullable|email|unique:merchant_users,email,' . $user->id,
                'password' => 'sometimes|min:8',
                'gender' => 'required|in:Male,Female,Other',
                'dob' => 'nullable|date',
                'role' => 'required|in:ATTENDANT,DISTRIBUTOR',
                'photo' => 'nullable|string', // base64
                'id_picture' => 'nullable|string', // base64
            ]);

            return $this->userService->updateUser($user, $request->all());
        });
    }

    public function destroy(MerchantUser $user): JsonResponse
    {
        return $this->execute(function () use ($user) {
            $user->delete();
            return $this->successResponse(null, 'User deleted successfully');
        });
    }

    public function toggleStatus(MerchantUser $user): JsonResponse
    {
        return $this->execute(function () use ($user) {
            return $this->userService->toggleStatus($user);
        });
    }

    public function listByBranch(Request $request, Branch $branch): JsonResponse
    {
        return $this->execute(function () use ($request, $branch) {
            $perPage = $request->input('per_page', 20);
            $role = $request->input('role'); // Optional role filter
            return $this->userService->listUsersByBranch($branch, $role, $perPage);
        });
    }

    public function resetPassword(MerchantUser $user): JsonResponse
    {
        return $this->execute(function () use ($user) {
            return $this->userService->resetPassword($user);
        });
    }

    
} 