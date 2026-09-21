<?php

namespace App\Console\Commands;

use App\Jobs\ProcessBroadcast;
use App\Models\Broadcast;
use Illuminate\Console\Command;

class DispatchScheduledBroadcasts extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'broadcast:dispatch-scheduled';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Dispatch scheduled broadcast campaigns that are due.';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $broadcasts = Broadcast::where('status', 'queued')
            ->whereNotNull('scheduled_at')
            ->where('scheduled_at', '<=', now())
            ->get();

        $count = $broadcasts->count();
        $this->info("Found {$count} broadcasts to dispatch.");

        foreach ($broadcasts as $broadcast) {
            ProcessBroadcast::dispatch($broadcast);
            $this->info("Dispatched broadcast ID: {$broadcast->id}");
        }
    }
}
