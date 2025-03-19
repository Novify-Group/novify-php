<?php

namespace App\Services\SMS;

use App\Contracts\Services\SMSServiceContract;
use App\Jobs\SendBulkSMS;
use Illuminate\Support\Facades\Log;

class AfricasTalkingSMSService implements SMSServiceContract
{
    public function send(string $phoneNumber, string $message): bool
    {
        Log::emergency("TESTING SMS LOG");  // This should definitely show up
        Log::error("SMS to {$phoneNumber}: {$message}");
        // TODO: Implement actual SMS sending logic
        return true;
    }

    public function sendBulk(array $messages): array
    {
        $results = [];
        foreach ($messages as $message) {
            try {
                $success = $this->send($message['phone'], $message['message']);
                $results[] = [
                    'phone' => $message['phone'],
                    'success' => $success,
                    'error' => $success ? null : 'Failed to send'
                ];
            } catch (\Exception $e) {
                $results[] = [
                    'phone' => $message['phone'],
                    'success' => false,
                    'error' => $e->getMessage()
                ];
            }
        }
        return $results;
    }

    public function queueBulkSend(array $messages, int $batchSize = 100): void
    {
        SendBulkSMS::dispatch($messages, $batchSize);
    }
} 