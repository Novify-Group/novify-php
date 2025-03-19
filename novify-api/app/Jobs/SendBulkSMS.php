<?php

namespace App\Jobs;

use App\Contracts\Services\SMSServiceInterface;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;
use Illuminate\Support\Facades\Log;

class SendBulkSMS implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    private array $messages;
    private int $batchSize;

    /**
     * Create a new job instance.
     */
    public function __construct(array $messages, int $batchSize = 100)
    {
        $this->messages = $messages;
        $this->batchSize = $batchSize;
    }

    /**
     * Execute the job.
     */
    public function handle(SMSServiceInterface $smsService): void
    {
        // Process messages in batches
        foreach (array_chunk($this->messages, $this->batchSize) as $batch) {
            try {
                $results = $smsService->sendBulk($batch);
                
                // Log results
                foreach ($results as $result) {
                    if (!$result['success']) {
                        Log::error("Failed to send SMS to {$result['phone']}", [
                            'error' => $result['error'] ?? 'Unknown error'
                        ]);
                    }
                }
            } catch (\Exception $e) {
                Log::error("Bulk SMS sending failed", [
                    'error' => $e->getMessage(),
                    'batch_size' => count($batch)
                ]);
                
                // You might want to retry the job or handle the error differently
                throw $e;
            }
        }
    }
} 