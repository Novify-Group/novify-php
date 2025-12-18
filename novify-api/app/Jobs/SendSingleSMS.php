<?php

namespace App\Jobs;

use App\Contracts\Services\SMSServiceContract;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendSingleSMS implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private string $phoneNumber;
    private string $message;

    /**
     * Create a new job instance.
     */
    public function __construct(string $phoneNumber, string $message)
    {
        $this->phoneNumber = $phoneNumber;
        $this->message = $message;
    }

    /**
     * Execute the job.
     */
    public function handle(SMSServiceContract $smsService): void
    {
        try {
            $success = $smsService->send($this->phoneNumber, $this->message);
            
            if (!$success) {
                Log::error("Failed to send SMS to {$this->phoneNumber}", [
                    'message' => $this->message
                ]);
                
                // You might want to retry the job or handle the error differently
                throw new \Exception("Failed to send SMS to {$this->phoneNumber}");
            }
            
            Log::info("SMS sent successfully to {$this->phoneNumber}");
        } catch (\Exception $e) {
            Log::error("SMS sending job failed", [
                'error' => $e->getMessage(),
                'phone' => $this->phoneNumber,
                'message' => $this->message
            ]);
            
            throw $e;
        }
    }
}
