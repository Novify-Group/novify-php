<?php

namespace App\Services\SMS;

use App\Contracts\Services\SMSServiceContract;
use App\Jobs\SendBulkSMS;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class EgoSMSService implements SMSServiceContract
{
    public function send(string $phoneNumber, string $message): bool
    {
        Log::info("Sending SMS to {$phoneNumber} with message {$message}");
        try {
            $response = Http::withHeaders([
                'Content-Type' => 'application/json'
            ])->post(config('egosms.api_url'), [
                'method' => 'SendSms',
                'userdata' => [
                    'username' => config('egosms.username'),
                    'password' => config('egosms.password')
                ],
                'msgdata' => [
                    [
                        'number' => $phoneNumber,
                        'message' => $message,
                        'senderid' => config('egosms.sender_id'),
                        'priority' => '0'
                    ]
                ]
            ]);

            if ($response->successful()) {
                $result = $response->json();
                
                if ($result['Status'] === 'OK') {
                    Log::info("SMS sent successfully to {$phoneNumber}", [
                        'cost' => $result['Cost'],
                        'reference' => $result['MsgFollowUpUniqueCode']
                    ]);
                    return true;
                }

                Log::error("Failed to send SMS to {$phoneNumber}", [
                    'error' => $result['Message'] ?? 'Unknown error'
                ]);
                return false;
            }

            Log::error("HTTP request failed for SMS to {$phoneNumber}", [
                'error' => $response->body()
            ]);
            return false;

        } catch (\Exception $e) {
            Log::error("SMS sending failed", [
                'error' => $e->getMessage(),
                'phone' => $phoneNumber
            ]);
            return false;
        }
    }

    public function sendBulk(array $messages): array
    {
        // ... existing code ...
    }

    public function queueBulkSend(array $messages, int $batchSize = 100): void
    {
        // ... existing code ...
    }
} 