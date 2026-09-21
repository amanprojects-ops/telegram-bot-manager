<?php

namespace App\Jobs;

use App\Models\Broadcast;
use App\Models\BroadcastMessage;
use App\Services\Telegram\BroadcastService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class ProcessBroadcast implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public Broadcast $broadcast
    ) {}

    /**
     * Execute the job.
     */
    public function handle(BroadcastService $service): void
    {
        // 1. Prepare broadcast messages
        $service->prepareBroadcast($this->broadcast);

        // 2. Dispatch individual message jobs
        // We chunk them to avoid loading too many models into memory at once
        $delay = now();
        $rateLimitSeconds = config('telegram.broadcast_rate_limit', 1); // e.g. 1 second per X messages
        
        BroadcastMessage::where('broadcast_id', $this->broadcast->id)
            ->where('status', 'pending')
            ->chunkById(100, function ($messages) use (&$delay, $rateLimitSeconds) {
                foreach ($messages as $index => $message) {
                    // Spread the load to respect rate limits (e.g., 25 msgs/sec for Telegram)
                    // We dispatch them with a calculated delay
                    $batchDelay = $delay->copy()->addSeconds(floor($index / 20) * $rateLimitSeconds);
                    
                    SendBroadcastMessage::dispatch($message)->delay($batchDelay);
                }
                
                // Add some buffer delay between chunks
                $delay->addSeconds(10);
            });
            
        // Note: The completion of the broadcast could be handled by checking if all messages are processed,
        // or using Laravel job batching. For simplicity, we just mark it completed after dispatching here, 
        // but a real implementation would use Bus::batch()
        // We will just let the individual jobs run. 
        // We will update the completeBroadcast logic to be triggered by a scheduled command or the last job.
    }
}
