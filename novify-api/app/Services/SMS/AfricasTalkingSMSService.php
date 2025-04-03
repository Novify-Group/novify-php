<?php

namespace App\Services\SMS;

use App\Contracts\Services\SMSServiceContract;
use App\Jobs\SendBulkSMS;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Http;

class AfricasTalkingSMSService implements SMSServiceContract
{
    public function send(string $phoneNumber, string $message): bool
    {
        Log::info("Sending SMS to {$phoneNumber} with message {$message}");
        try {
            $response = Http::withHeaders([
                'Accept' => 'application/json',
                'Content-Type' => 'application/json',
                'apiKey' => config('africatkg.api_key')
            ])->post(config('africatkg.sms_url'), [
                'username' => config('africatkg.sms_username'),
                'message' => $message,
                //'senderId' => config('africatkg.sms_username'),
                'phoneNumbers' => [$phoneNumber]
            ]);

            if ($response->successful()) {
                Log::info("SMS response: " . $response->body());
                Log::info("SMS sent successfully to {$phoneNumber}");
                return true;
            }

            Log::error("Failed to send SMS to {$phoneNumber}", [
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