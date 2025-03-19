<?php

namespace App\Traits;

use Illuminate\Pagination\LengthAwarePaginator;

trait ApiResponse
{
    /**
     * Success response format
     */
    protected function successResponse($data = null, string $message = 'OK', int $code = 200): array
    {
        // If data contains pagination, format it
        if (isset($data) && $data instanceof LengthAwarePaginator)
             $data = $this->formatPagination($data);
        
        return [
            'success' => true,
            'data' => $data,
            'message' => $message,
            'code' => $code
        ];
    }

    /**
     * Error response format
     */
    protected function errorResponse(string $message, int $code = 400, $data = null,$errors = null): array
    {
        return [
            'success' => false,
            'code' => $code,
            'data' => $data,
            'message' => $message,
            'errors'=>$errors
        ];
    }

    /**
     * Format pagination to remove unwanted fields
     */
    private function formatPagination(LengthAwarePaginator $paginator): array
    {
        return [
            'current_page' => $paginator->currentPage(),
            'per_page' => $paginator->perPage(),
            'total' => $paginator->total(),
            'last_page' => $paginator->lastPage(),
            'data' => $paginator->items()
        ];
    }
} 