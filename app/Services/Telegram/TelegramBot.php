<?php

namespace App\Services\Telegram;

use App\Models\TelegramSession;
use App\Models\TelegramUser;
use Illuminate\Support\Facades\Log;

class TelegramBot
{
    public function __construct(
        private TelegramApi $api,
        private MessageHandler $messageHandler,
        private CallbackHandler $callbackHandler,
    ) {}

    /**
     * Handle an incoming Telegram update.
     *
     * @param  array<string, mixed>  $update
     */
    public function handleUpdate(array $update): void
    {
        if (isset($update['callback_query'])) {
            $this->handleCallbackQuery($update['callback_query']);

            return;
        }

        if (isset($update['message'])) {
            $this->handleMessage($update['message']);

            return;
        }

        Log::info('Telegram: unhandled update type', ['update' => $update]);
    }

    /**
     * Handle a message update.
     *
     * @param  array<string, mixed>  $message
     */
    private function handleMessage(array $message): void
    {
        $from = $message['from'] ?? null;
        if (! $from) {
            return;
        }

        $chatId = $message['chat']['id'];
        $text = $message['text'] ?? '';

        // Track the user
        $telegramUser = TelegramUser::findOrCreateFromTelegram($from);

        // Get or create session
        $session = TelegramSession::findOrCreateForUser($telegramUser->telegram_user_id, $chatId);

        // Check if it's a command
        if (str_starts_with($text, '/')) {
            $this->messageHandler->handleCommand($chatId, $text, $telegramUser, $session);

            return;
        }

        // If session has an active state, process through state machine
        if ($session->state !== TelegramSession::STATE_IDLE) {
            $this->messageHandler->handleStatefulMessage($chatId, $text, $telegramUser, $session);

            return;
        }

        // Default: show main menu
        $this->messageHandler->sendMainMenu($chatId);
    }

    /**
     * Handle a callback query (inline button press).
     *
     * @param  array<string, mixed>  $callbackQuery
     */
    private function handleCallbackQuery(array $callbackQuery): void
    {
        $from = $callbackQuery['from'] ?? null;
        if (! $from) {
            return;
        }

        $callbackQueryId = $callbackQuery['id'];
        $chatId = $callbackQuery['message']['chat']['id'] ?? null;
        $messageId = $callbackQuery['message']['message_id'] ?? null;
        $data = $callbackQuery['data'] ?? '';

        if (! $chatId) {
            return;
        }

        // Track the user
        $telegramUser = TelegramUser::findOrCreateFromTelegram($from);

        // Acknowledge the callback
        $this->api->answerCallbackQuery($callbackQueryId);

        // Get session
        $session = TelegramSession::findOrCreateForUser($telegramUser->telegram_user_id, $chatId);

        // Route to callback handler
        $this->callbackHandler->handle($chatId, $messageId, $data, $telegramUser, $session);
    }
}
