<?php

namespace App\Http\Controllers\API\Merchant;

use App\Services\MerchantUserService;
use App\Http\Requests\BranchUser\LoginBranchUserRequest;
use App\Http\Controllers\API\BaseApiController;

class BranchUserAuthController extends BaseApiController
{
    protected $merchantUserService;

    public function __construct(MerchantUserService $merchantUserService)
    {
        $this->merchantUserService = $merchantUserService;
    }

    public function login(LoginBranchUserRequest $request)
    {
        return $this->execute(function () use ($request) {
            return $this->merchantUserService->login($request->validated());
        });
      
    }
} 