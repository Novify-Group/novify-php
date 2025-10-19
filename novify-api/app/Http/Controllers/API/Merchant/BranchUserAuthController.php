<?php

namespace App\Http\Controllers\API\Merchant;

use App\Services\MerchantUserService;
use App\Http\Requests\BranchUser\LoginBranchUserRequest;
use App\Http\Controllers\API\BaseApiController;
use OpenApi\Annotations as OA;

class BranchUserAuthController extends BaseApiController
{
    protected $merchantUserService;

    public function __construct(MerchantUserService $merchantUserService)
    {
        $this->merchantUserService = $merchantUserService;
    }

    /**
     * @OA\Post(
     *     path="/api/v1/auth/attendant-login",
     *     tags={"Authentication"},
     *     summary="Staff/Attendant login",
     *     description="Authenticate staff members (attendants, distributors) with phone number and password",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"phone_number", "password"},
     *             @OA\Property(property="phone_number", type="string", example="1234567890"),
     *             @OA\Property(property="password", type="string", format="password", example="password123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Staff login successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Login successful"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="user", type="object"),
     *                 @OA\Property(property="token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9..."),
     *                 @OA\Property(property="merchant", type="object"),
     *                 @OA\Property(property="branch", type="object")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=401,
     *         description="Invalid credentials",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Invalid credentials")
     *         )
     *     )
     * )
     */
    public function login(LoginBranchUserRequest $request)
    {
        return $this->execute(function () use ($request) {
            return $this->merchantUserService->login($request->validated());
        });
      
    }
} 