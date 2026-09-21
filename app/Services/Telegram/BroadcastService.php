<?php

namespace App\Services\Telegram;

use App\Models\Broadcast;
use App\Models\BroadcastMessage;
use App\Models\TelegramUser;
use Illuminate\Support\Facades\Log;

class BroadcastService
{
    public function __construct(
        private TelegramApi $api,
    ) {}

    /**
     * Prepare a broadcast by creating message records for all active users.
     */
    public function prepareBroadcast(Broadcast $broadcast): void
    {
        $broadcast->update(['status' => 'processing']);

        // In a real application, you might want to chunk this for memory efficiency
        // if there are hundreds of thousands of users.
        TelegramUser::chunk(1000, function ($users) use ($broadcast) {
            $messages = [];
            foreach ($users as $user) {
                $messages[] = [
                    'broadcast_id' => $broadcast->id,
                    'telegram_user_id' => $user->telegram_user_id,
                    'status' => 'pending',
                    'created_at' => now(),
                    'updated_at' => now(),
                ];
            }
            BroadcastMessage::insert($messages);
        });
    }

    /**
     * Send a single broadcast message.
     */
    public function sendMessage(BroadcastMessage $message): void
    {
        $broadcast = $message->broadcast;
        
        try {
            $replyMarkup = null;
            if (!empty($broadcast->keyboard_buttons)) {
                $replyMarkup = TelegramApi::inlineKeyboard($broadcast->keyboard_buttons);
            }

            $this->api->sendMessage(
                $message->telegram_user_id,
                $broadcast->message,
                $replyMarkup
            );

            $message->update(['status' => 'sent']);
            
            // Increment success counter safely
            $broadcast->increment('successful_sends');
            
        } catch (\Throwable $e) {
            Log::error('Failed to send broadcast message', [
                'broadcast_message_id' => $message->id,
                'telegram_user_id' => $message->telegram_user_id,
                'error' => $e->getMessage(),
            ]);

            $message->update([
                'status' => 'failed',
                'error' => substr($e->getMessage(), 0, 255),
            ]);
            
            // Increment failure counter safely
            $broadcast->increment('failed_sends');
        }
    }

    /**
     * Complete a broadcast process.
     */
    public function completeBroadcast(Broadcast $broadcast): void
    {
        $broadcast->update([
            'status' => 'completed',
            'completed_at' => now(),
        ]);
        
        // Notify admin about completion
        $adminChatId = config('telegram.admin_chat_id');
        if ($adminChatId) {
            $text = "📢 <b>Broadcast Completed</b>\n\n"
                . "<b>Name:</b> {$broadcast->name}\n"
                . "<b>Total:</b> {$broadcast->total_targets}\n"
                . "<b>Success:</b> {$broadcast->successful_sends}\n"
                . "<b>Failed:</b> {$broadcast->failed_sends}";
                
            $this->api->sendMessage($adminChatId, $text);
        }
    }
}
