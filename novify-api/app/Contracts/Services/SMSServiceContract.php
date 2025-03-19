<?php

namespace App\Contracts\Services;

interface SMSServiceContract
{
    /**
     * Send a single SMS
     *
     * @param string $phoneNumber
     * @param string $message
     * @return bool
     */
    public function send(string $phoneNumber, string $message): bool;

    /**
     * Send bulk SMS messages synchronously
     * 
     * @param array $messages Array of ['phone' => string, 'message' => string]
     * @return array Array of ['phone' => string, 'success' => bool, 'error' => string|null]
     */
    public function sendBulk(array $messages): array;

    /**
     * Queue bulk SMS messages for background processing
     */
    public function queueBulkSend(array $messages, int $batchSize = 100): void;
} 