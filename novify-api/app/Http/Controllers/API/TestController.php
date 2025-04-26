<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\MerchantService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use App\Contracts\Services\SMSServiceContract;

class TestController extends Controller
{
    protected $smsService;
    public function __construct(SMSServiceContract $smsService)
    {
        $this->smsService = $smsService;
    }

    public function register(Request $request): JsonResponse
    {
        $response = $this->smsService->send('256777245670', 'Hello, this is a test message');
        return response()->json(['message' => $response]);
    }
} 