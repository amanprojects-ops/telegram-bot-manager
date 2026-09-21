<?php

namespace App\Jobs;

use App\Models\BroadcastMessage;
use App\Services\Telegram\BroadcastService;
use Illuminate\Bus\Queueable;
use Illuminate\Contracts\Queue\ShouldQueue;
use Illuminate\Foundation\Bus\Dispatchable;
use Illuminate\Queue\InteractsWithQueue;
use Illuminate\Queue\SerializesModels;

class SendBroadcastMessage implements ShouldQueue
{
    use Dispatchable, InteractsWithQueue, Queueable, SerializesModels;

    /**
     * Create a new job instance.
     */
    public function __construct(
        public BroadcastMessage $message
    ) {}

    /**
     * Execute the job.
     */
    public function handle(BroadcastService $service): void
    {
        if ($this->message->status !== 'pending') {
            return;
        }

        $service->sendMessage($this->message);
        
        // Optional: Check if this was the last message, and complete the broadcast.
        // In a production app, use Job Batching instead.
        $pendingCount = BroadcastMessage::where('broadcast_id', $this->message->broadcast_id)
            ->where('status', 'pending')
            ->count();
            
        if ($pendingCount === 0) {
            $service->completeBroadcast($this->message->broadcast);
        }
    }
}
