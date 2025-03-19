<?php

namespace App\Http\Controllers\API;

use App\Http\Controllers\Controller;
use App\Services\MerchantService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TestController extends Controller
{
    public function __construct()
    {
    }

    public function register(Request $request): JsonResponse
    {

        return response()->json(['message' => 'Test']);
    }
} 