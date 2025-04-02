<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Http\Requests\Merchant\StoreMerchantRequest;
use App\Http\Requests\Merchant\UpdateMerchantRequest;
use App\Services\MerchantService;
use App\Models\Merchant;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class MerchantController extends BaseApiController
{
    protected MerchantService $merchantService;

    public function __construct(MerchantService $merchantService)
    {
        $this->merchantService = $merchantService;
    }

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

    public function verifyOtp(Request $request): JsonResponse
    {
        return $this->execute(function () use ($request) {
            $request->validate([
                'otp' => 'required|string|size:4'
            ]);

            return $this->merchantService->verifyOtp($request->user(), $request->otp);
        });
    }

    public function resendOtp(Request $request): JsonResponse
    {
        return $this->execute(function () use ($request) {
            return $this->merchantService->resendOtp($request->user());
        });
    }

    public function logout(): JsonResponse
    {
        return $this->execute(function () {
            auth('api')->logout();
            return $this->successResponse(null, 'Successfully logged out');
        });
    }

    public function refresh(): JsonResponse
    {
        return $this->execute(function () {
            return $this->successResponse([
                'token' => auth('api')->refresh()
            ]);
        });
    }

    public function getMerchants(Request $request): JsonResponse
    {
        return $this->execute(function () use ($request) {
            return $this->merchantService->getMerchants($request);
        });
    }

    public function getMerchantByWalletNumber(Request $request): JsonResponse
    {
        return $this->execute(function () use ($request) {
            return $this->merchantService->getMerchantByWalletNumber($request->wallet_number);
        });
    }
} 