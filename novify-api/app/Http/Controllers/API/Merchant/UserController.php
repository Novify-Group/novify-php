<?php

namespace App\Http\Controllers\API\Merchant;

use App\Http\Controllers\API\BaseApiController;
use App\Models\MerchantUser;
use App\Services\MerchantUserService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Models\Branch;
use OpenApi\Annotations as OA;

class UserController extends BaseApiController
{
    protected $userService;

    public function __construct(MerchantUserService $userService)
    {
        $this->userService = $userService;
    }

    /**
     * @OA\Post(
     *     path="/api/v1/merchant/users",
     *     tags={"Merchants"},
     *     summary="Create staff member",
     *     description="Create a new staff member (attendant or distributor) for the merchant",
     *     security={{"jwt": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"branch_id", "first_name", "last_name", "phone_number", "password", "role"},
     *             @OA\Property(property="branch_id", type="integer", example=1),
     *             @OA\Property(property="first_name", type="string", example="John"),
     *             @OA\Property(property="last_name", type="string", example="Doe"),
     *             @OA\Property(property="phone_number", type="string", example="1234567890"),
     *             @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="password123"),
     *             @OA\Property(property="role", type="string", enum={"ATTENDANT", "DISTRIBUTOR"}, example="ATTENDANT"),
     *             @OA\Property(property="gender", type="string", enum={"Male", "Female", "Other"}, example="Male"),
     *             @OA\Property(property="dob", type="string", format="date", example="1990-01-01"),
     *             @OA\Property(property="photo", type="string", example="base64_encoded_image"),
     *             @OA\Property(property="id_picture", type="string", example="base64_encoded_image")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Staff member created successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Staff member created successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="user", type="object")
     *             )
     *         )
     *     )
     * )
     */
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
                'id' => 'nullable|exists:merchant_users,id',
            ]);

            return $this->userService->createUser($request->user(), $request->all());
        });
    }

    /**
     * @OA\Get(
     *     path="/api/v1/merchant/users",
     *     tags={"Merchants"},
     *     summary="List staff members",
     *     description="Get list of staff members (attendants and distributors) for the merchant",
     *     security={{"jwt": {}}},
     *     @OA\Parameter(
     *         name="role",
     *         in="query",
     *         description="Filter by role",
     *         @OA\Schema(type="string", enum={"ATTENDANT", "DISTRIBUTOR"})
     *     ),
     *     @OA\Parameter(
     *         name="branchId",
     *         in="query",
     *         description="Filter by branch ID",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Number of users per page",
     *         @OA\Schema(type="integer", example=20)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Staff members retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="users", type="array", @OA\Items(type="object")),
     *                 @OA\Property(property="pagination", type="object")
     *             )
     *         )
     *     )
     * )
     */
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

    /**
     * @OA\Put(
     *     path="/api/v1/merchant/users/{user}",
     *     tags={"Merchants"},
     *     summary="Update staff member",
     *     description="Update details of a staff member",
     *     security={{"jwt": {}}},
     *     @OA\Parameter(
     *         name="user",
     *         in="path",
     *         required=true,
     *         description="User ID",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             @OA\Property(property="branch_id", type="integer", example=1),
     *             @OA\Property(property="first_name", type="string", example="John"),
     *             @OA\Property(property="last_name", type="string", example="Doe"),
     *             @OA\Property(property="phone_number", type="string", example="1234567890"),
     *             @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="newpassword123"),
     *             @OA\Property(property="role", type="string", enum={"ATTENDANT", "DISTRIBUTOR"}, example="ATTENDANT"),
     *             @OA\Property(property="gender", type="string", enum={"Male", "Female", "Other"}, example="Male"),
     *             @OA\Property(property="dob", type="string", format="date", example="1990-01-01"),
     *             @OA\Property(property="photo", type="string", example="base64_encoded_image"),
     *             @OA\Property(property="id_picture", type="string", example="base64_encoded_image")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Staff member updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Staff member updated successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="user", type="object")
     *             )
     *         )
     *     )
     * )
     */
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

    /**
     * @OA\Delete(
     *     path="/api/v1/merchant/users/{user}",
     *     tags={"Merchants"},
     *     summary="Delete staff member",
     *     description="Delete a staff member from the merchant",
     *     security={{"jwt": {}}},
     *     @OA\Parameter(
     *         name="user",
     *         in="path",
     *         required=true,
     *         description="User ID",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Staff member deleted successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="User deleted successfully")
     *         )
     *     )
     * )
     */
    public function destroy(MerchantUser $user): JsonResponse
    {
        return $this->execute(function () use ($user) {
            $user->delete();
            return $this->successResponse(null, 'User deleted successfully');
        });
    }

    /**
     * @OA\Post(
     *     path="/api/v1/merchant/users/{user}/toggle-status",
     *     tags={"Merchants"},
     *     summary="Toggle staff member status",
     *     description="Activate or deactivate a staff member",
     *     security={{"jwt": {}}},
     *     @OA\Parameter(
     *         name="user",
     *         in="path",
     *         required=true,
     *         description="User ID",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Staff member status updated successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Staff member status updated successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="user", type="object")
     *             )
     *         )
     *     )
     * )
     */
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

    /**
     * @OA\Post(
     *     path="/api/v1/merchant/users/{user}/reset-password",
     *     tags={"Merchants"},
     *     summary="Reset staff member password",
     *     description="Reset password for a staff member",
     *     security={{"jwt": {}}},
     *     @OA\Parameter(
     *         name="user",
     *         in="path",
     *         required=true,
     *         description="User ID",
     *         @OA\Schema(type="integer", example=1)
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Password reset successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Password reset successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="user", type="object")
     *             )
     *         )
     *     )
     * )
     */
    public function resetPassword(MerchantUser $user): JsonResponse
    {
        return $this->execute(function () use ($user) {
            return $this->userService->resetPassword($user);
        });
    }

    
} 