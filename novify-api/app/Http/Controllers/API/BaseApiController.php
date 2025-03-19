<?php

namespace App\Http\Controllers\API;

use App\Helpers\LogHelper;
use App\Http\Controllers\Controller;
use App\Traits\ApiResponse;
use Illuminate\Http\JsonResponse;
use Illuminate\Validation\ValidationException;

class BaseApiController extends Controller
{
    use ApiResponse;

    /**
     * Execute an action and handle any errors
     */
    protected function execute(callable $action, ?string $endpoint = null): JsonResponse
    {
            // Bind the closure to $this to maintain context
            if ($action instanceof \Closure) {
                $action = $action->bindTo($this);
            }
            return response()->json($action());
    }
} 