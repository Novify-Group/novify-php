<?php

namespace App\Exceptions;

use Exception;

class PaymentException extends Exception
{
    public function __construct($message = "Payment failed", $code = 400, Exception $previous = null)
    {
        parent::__construct($message, $code, $previous);
    }

    public function render($request)
    {
        return response()->json([
            'message' => $this->getMessage(),
            'status' => 'error',
            'success' => false,
        ], 400);
    }

    public function report()
    {
        return response()->json([
            'message' => $this->getMessage(),
            'status' => 'error',
            'success' => false,
        ], 400);
    }
}
