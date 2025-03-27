<?php

namespace App\Exceptions;

use App\Traits\ApiResponse;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Database\Eloquent\ModelNotFoundException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;
use App\Helpers\LogHelper;

class Handler extends ExceptionHandler
{
    use ApiResponse;

    /**
     * The list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     */
    public function register(): void
    {
    
    }

    /**
     * Render an exception into an HTTP response.
     */
    public function render($request, Throwable $e)
    {
        // Always return JSON for API routes
        if ($request->is('api/*') || $request->expectsJson()) {
            return $this->handleApiException($e);
        }

        return parent::render($request, $e);
    }

    /**
     * Handle API exceptions
     */
    private function handleApiException(Throwable $e): JsonResponse
    {
        if ($e instanceof ValidationException) {
            LogHelper::logError('Validation', $e, ['errors' => $e->errors()]);
            return response()->json(
                $this->errorResponse(
                    'Validation failed',
                    400,
                    null,
                     $e->errors()
                ),
                400
            );
        }

        if ($e instanceof ModelNotFoundException) {
            return response()->json(
                $this->errorResponse('Entity not found', 404),
                404
            );
        }

        if ($e instanceof NotFoundHttpException) {

             // Handle Exception raised for no results found in DB
             if(str_contains($e->getMessage(), 'results for model')) {
              
                return response()->json(
                $this->successResponse(
                    [],
                    '0 records found',
                    200
                ),
                200);
            }

            return response()->json(
                $this->errorResponse('Resource not found', 404),
                404
            );
        } 

        if ($e instanceof AuthenticationException) {
            return response()->json(
                $this->errorResponse('Unauthorized', 401),
                401
            );
        }

        // Log unexpected errors
        if (!$e instanceof HttpException) {
            LogHelper::logError('Unexpected', $e);
        }

        // Return error response
        $statusCode = $e instanceof HttpException ? $e->getStatusCode() : 500;
        $message = $e instanceof HttpException ? $e->getMessage() : 'Internal Server Error';

        if (config('app.debug')) {

                return response()->json(
                    $this->errorResponse(
                        $message,
                    $statusCode,
                    [
                        'exception' => get_class($e),
                        'message' => $e->getMessage(),
                        'file' => (app()->environment('local'))?$e->getFile():"",
                        'line' => (app()->environment('local'))?$e->getLine():"",
                        'trace' => (app()->environment('local'))?$e->getTrace():""
                    ]
                ),
                $statusCode
            );
        }

        return response()->json(
            $this->errorResponse($message, $statusCode),
            $statusCode
        );
    }
} 