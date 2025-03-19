<?php

namespace App\Helpers;

use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Auth;

class LogHelper
{
    /**
     * Log an API request
     */
    public static function logRequest(string $action, array $data = [], ?string $userId = null): void
    {
        try {
            Log::info("API Request: {$action}", [
                'user_id' => $userId ?? self::getUserId(),
                'data' => self::sanitizeData($data),
                'ip' => request()->ip(),
                'user_agent' => request()->userAgent()
            ]);
        } catch (\Throwable $e) {
            // Fail silently - logging should not break the application
            Log::error("Failed to log request", ['error' => $e->getMessage()]);
        }
    }

    /**
     * Log an API response
     */
    public static function logResponse(string $action, array $response = [], ?string $userId = null): void
    {
        try {
            Log::info("API Response: {$action}", [
                'user_id' => $userId ?? self::getUserId(),
                'data' => self::sanitizeData($response),
                'execution_time' => microtime(true) - LARAVEL_START
            ]);
        } catch (\Throwable $e) {
            // Fail silently
            Log::error("Failed to log response", ['error' => $e->getMessage()]);
        }
    }

    /**
     * Log an error
     */
    public static function logError(string $action, \Throwable $error, array $context = []): void
    {
        try {
            Log::error("API Error: {$action}", [
                'user_id' => self::getUserId(),
                'error' => $error->getMessage(),
                'file' => $error->getFile(),
                'line' => $error->getLine(),
                'context' => $context
            ]);
        } catch (\Throwable $e) {
            // Last resort logging
            Log::error("Failed to log error", [
                'original_error' => $error->getMessage(),
                'logging_error' => $e->getMessage()
            ]);
        }
    }

    /**
     * Get current user ID safely
     */
    private static function getUserId(): string
    {
        try {
            if (Auth::hasUser() && ($user = Auth::user())) {
                return $user->id;
            }
        } catch (\Throwable $e) {
            // If anything fails, return guest
            Log::error("Failed to get user ID", ['error' => $e->getMessage()]);
        }
        return 'guest';
    }

    /**
     * Sanitize sensitive data
     */
    private static function sanitizeData(array $data): array
    {
        $sensitiveFields = [
            'password',
            'password_confirmation',
            'current_password',
            'otp',
            'token',
            'access_token',
            'refresh_token'
        ];

        return collect($data)->map(function ($value, $key) use ($sensitiveFields) {
            if (in_array($key, $sensitiveFields)) {
                return '******';
            }
            if (is_array($value)) {
                return self::sanitizeData($value);
            }
            return $value;
        })->toArray();
    }
} 