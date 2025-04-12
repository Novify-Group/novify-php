<?php

namespace App\Exceptions;

use Exception;

class ValidationException extends Exception
{
    public function __construct($message = "Validation failed", $code = 422, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function render($request)
    {
        return response()->json([
            'message' => $this->getMessage(),
            'status' => 'error',
        ], 422);
    }

    public function report()
    {
        return response()->json([
            'message' => $this->getMessage(),
            'status' => 'error',
        ], 422);
    }
}
