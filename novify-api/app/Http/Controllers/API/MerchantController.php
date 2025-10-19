<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Merchant\StoreMerchantRequest;
use App\Http\Requests\Merchant\UpdateMerchantRequest;
use App\Services\MerchantService;
use App\Models\Merchant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use OpenApi\Annotations as OA;

class MerchantController extends BaseApiController
{
    protected MerchantService $merchantService;

    public function __construct(MerchantService $merchantService)
    {
        $this->merchantService = $merchantService;
    }

    /**
     * @OA\Post(
     *     path="/api/v1/auth/register",
     *     tags={"Authentication"},
     *     summary="Register a new merchant",
     *     description="Register a new merchant with complete business information and KYC",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"first_name", "last_name", "email", "phone_number", "password", "store_name", "country_id"},
     *             @OA\Property(property="first_name", type="string", example="John"),
     *             @OA\Property(property="middle_name", type="string", example="Michael"),
     *             @OA\Property(property="last_name", type="string", example="Doe"),
     *             @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *             @OA\Property(property="phone_number", type="string", example="1234567890"),
     *             @OA\Property(property="password", type="string", format="password", example="password123"),
     *             @OA\Property(property="store_name", type="string", example="John's Store"),
     *             @OA\Property(property="store_description", type="string", example="A great store"),
     *             @OA\Property(property="country_id", type="integer", example=1),
     *             @OA\Property(property="dob", type="string", format="date", example="1990-01-01"),
     *             @OA\Property(property="id_type", type="string", example="passport"),
     *             @OA\Property(property="id_number", type="string", example="A1234567"),
     *             @OA\Property(property="id_picture", type="string", example="base64_encoded_image"),
     *             @OA\Property(property="passport_photo", type="string", example="base64_encoded_image"),
     *             @OA\Property(property="store_logo", type="string", example="base64_encoded_image")
     *         )
     *     ),
     *     @OA\Response(
     *         response=201,
     *         description="Merchant registered successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Registration successful. Please verify your phone number with the OTP sent."),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=422,
     *         description="Validation error",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="The given data was invalid."),
     *             @OA\Property(property="errors", type="object")
     *         )
     *     )
     * )
     */
    public function register(StoreMerchantRequest $request): JsonResponse
    {
        return $this->execute(function () use ($request) {
            return $this->merchantService->register($request->validated());
        });
    }

    public function update(UpdateMerchantRequest $request, Merchant $merchant)
    {
        return $this->execute(function () use ($request, $merchant) {
            return $this->merchantService->update($merchant, $request->validated());
        });
    }

    /**
     * @OA\Post(
     *     path="/api/v1/auth/login",
     *     tags={"Authentication"},
     *     summary="Merchant login",
     *     description="Authenticate a merchant with username/email and password",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"username", "password"},
     *             @OA\Property(property="username", type="string", example="john@example.com", description="Email or phone number"),
     *             @OA\Property(property="password", type="string", format="password", example="password123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Login successful",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="OTP sent to your phone number"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="requires_otp", type="boolean", example=true),
     *                 @OA\Property(property="token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...")
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
    public function login(Request $request): JsonResponse
    {
        return $this->execute(function () use ($request) {
            $request->validate([
                'username' => 'required|string',
                'password' => 'required|string',
            ]);

            return $this->merchantService->login($request->username, $request->password);
        });
    }

    /**
     * @OA\Post(
     *     path="/api/v1/auth/verify-otp",
     *     tags={"Authentication"},
     *     summary="Verify OTP",
     *     description="Verify the OTP sent to merchant's phone number",
     *     security={{"jwt": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"otp"},
     *             @OA\Property(property="otp", type="string", example="1234", description="4-digit OTP code")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="OTP verified successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Phone number verified successfully"),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="merchant", type="object"),
     *                 @OA\Property(property="token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=400,
     *         description="Invalid OTP",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Invalid or expired OTP")
     *         )
     *     )
     * )
     */
    public function verifyOtp(Request $request): JsonResponse
    {
        return $this->execute(function () use ($request) {
            $request->validate([
                'otp' => 'required|string|size:4'
            ]);

            return $this->merchantService->verifyOtp($request->user(), $request->otp);
        });
    }

    /**
     * @OA\Post(
     *     path="/api/v1/auth/resend-otp",
     *     tags={"Authentication"},
     *     summary="Resend OTP",
     *     description="Resend OTP to merchant's phone number",
     *     security={{"jwt": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="OTP resent successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="OTP resent to your phone number")
     *         )
     *     )
     * )
     */
    public function resendOtp(Request $request): JsonResponse
    {
        return $this->execute(function () use ($request) {
            return $this->merchantService->resendOtp($request->user());
        });
    }

    /**
     * @OA\Post(
     *     path="/api/v1/auth/logout",
     *     tags={"Authentication"},
     *     summary="Logout merchant",
     *     description="Logout the authenticated merchant",
     *     security={{"jwt": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Logged out successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Successfully logged out")
     *         )
     *     )
     * )
     */
    public function logout(): JsonResponse
    {
        return $this->execute(function () {
            auth('api')->logout();
            return $this->successResponse(null, 'Successfully logged out');
        });
    }

    /**
     * @OA\Post(
     *     path="/api/v1/auth/refresh",
     *     tags={"Authentication"},
     *     summary="Refresh JWT token",
     *     description="Refresh the JWT token for the authenticated merchant",
     *     security={{"jwt": {}}},
     *     @OA\Response(
     *         response=200,
     *         description="Token refreshed successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="token", type="string", example="eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9...")
     *             )
     *         )
     *     )
     * )
     */
    public function refresh(): JsonResponse
    {
        return $this->execute(function () {
            return $this->successResponse([
                'token' => auth('api')->refresh()
            ]);
        });
    }

    /**
     * @OA\Get(
     *     path="/api/v1/merchant/",
     *     tags={"Merchants"},
     *     summary="Get merchants list",
     *     description="Get a list of merchants with filtering options",
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search by name or email",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="status",
     *         in="query",
     *         description="Filter by active status",
     *         @OA\Schema(type="boolean")
     *     ),
     *     @OA\Parameter(
     *         name="country_id",
     *         in="query",
     *         description="Filter by country",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Merchants retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="merchants", type="array", @OA\Items(type="object")),
     *                 @OA\Property(property="pagination", type="object")
     *             )
     *         )
     *     )
     * )
     */
    public function getMerchants(Request $request): JsonResponse
    {
        return $this->execute(function () use ($request) {
            return $this->merchantService->getMerchants($request);
        });
    }

    /**
     * @OA\Get(
     *     path="/api/v1/merchant/wallet/{wallet_number}",
     *     tags={"Merchants"},
     *     summary="Get merchant by wallet number",
     *     description="Get merchant information using wallet number",
     *     @OA\Parameter(
     *         name="wallet_number",
     *         in="path",
     *         required=true,
     *         description="Wallet number",
     *         @OA\Schema(type="string", example="123456789012")
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Merchant retrieved successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="data", type="object",
     *                 @OA\Property(property="merchant", type="object")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response=404,
     *         description="Merchant not found",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Merchant not found")
     *         )
     *     )
     * )
     */
    public function getMerchantByWalletNumber(Request $request): JsonResponse
    {
        return $this->execute(function () use ($request) {
            return $this->merchantService->getMerchantByWalletNumber($request->wallet_number);
        });
    }

    /**
     * @OA\Post(
     *     path="/api/v1/auth/forgot-password",
     *     tags={"Authentication"},
     *     summary="Forgot password",
     *     description="Request password reset for merchant account",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"email"},
     *             @OA\Property(property="email", type="string", format="email", example="john@example.com")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Password reset link sent",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Password reset link sent to your email")
     *         )
     *     )
     * )
     */
    public function forgotPassword(Request $request): JsonResponse
    {
        return $this->execute(function () use ($request) {
            $request->validate([
                'email' => 'required|email|exists:merchants,email'
            ]);

            return $this->merchantService->forgotPassword($request->email);
        });
    }

    /**
     * @OA\Post(
     *     path="/api/v1/auth/reset-password",
     *     tags={"Authentication"},
     *     summary="Reset password",
     *     description="Reset merchant password using token",
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"token", "email", "password", "password_confirmation"},
     *             @OA\Property(property="token", type="string", example="reset_token_here"),
     *             @OA\Property(property="email", type="string", format="email", example="john@example.com"),
     *             @OA\Property(property="password", type="string", format="password", example="newpassword123"),
     *             @OA\Property(property="password_confirmation", type="string", format="password", example="newpassword123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Password reset successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Password reset successfully")
     *         )
     *     )
     * )
     */
    public function resetPassword(Request $request): JsonResponse
    {
        return $this->execute(function () use ($request) {
            $request->validate([
                'token' => 'required|string',
                'email' => 'required|email|exists:merchants,email',
                'password' => 'required|min:8|confirmed'
            ]);

            return $this->merchantService->resetPassword($request->all());
        });
    }

    /**
     * @OA\Post(
     *     path="/api/v1/auth/change-password",
     *     tags={"Authentication"},
     *     summary="Change password",
     *     description="Change merchant password",
     *     security={{"jwt": {}}},
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"current_password", "password", "password_confirmation"},
     *             @OA\Property(property="current_password", type="string", format="password", example="currentpassword"),
     *             @OA\Property(property="password", type="string", format="password", example="newpassword123"),
     *             @OA\Property(property="password_confirmation", type="string", format="password", example="newpassword123")
     *         )
     *     ),
     *     @OA\Response(
     *         response=200,
     *         description="Password changed successfully",
     *         @OA\JsonContent(
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Password changed successfully")
     *         )
     *     )
     * )
     */
    public function changePassword(Request $request): JsonResponse
    {
        return $this->execute(function () use ($request) {
            $request->validate([
                'current_password' => 'required|string',
                'password' => 'required|min:8|confirmed'
            ]);

            return $this->merchantService->changePassword($request->user(), $request->all());
        });
    }
} 