<?php

namespace App\Http\Controllers\Telegram;

use App\Http\Controllers\Controller;
use App\Models\TelegramLog;
use App\Services\Telegram\TelegramBot;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;

class WebhookController extends Controller
{
    public function __construct(
        private TelegramBot $bot,
    ) {}

    /**
     * Handle incoming Telegram webhook updates.
     */
    public function handle(Request $request, string $secret): JsonResponse
    {
        // Validate webhook secret from URL
        if ($secret !== config('telegram.webhook_secret')) {
            Log::warning('Telegram webhook: invalid URL secret', [
                'ip' => $request->ip(),
            ]);

            return response()->json(['status' => 'unauthorized'], 403);
        }

        // Validate X-Telegram-Bot-Api-Secret-Token header
        $headerSecret = $request->header('X-Telegram-Bot-Api-Secret-Token');
        if ($headerSecret && $headerSecret !== config('telegram.webhook_secret')) {
            Log::warning('Telegram webhook: invalid header secret', [
                'ip' => $request->ip(),
            ]);

            return response()->json(['status' => 'unauthorized'], 403);
        }

        $update = $request->all();
        $updateId = $update['update_id'] ?? null;

        if (! $updateId) {
            return response()->json(['status' => 'invalid'], 400);
        }

        // Prevent duplicate processing
        if (TelegramLog::isDuplicate($updateId)) {
            return response()->json(['status' => 'duplicate']);
        }

        // Determine event type and user ID
        $eventType = $this->determineEventType($update);
        $telegramUserId = $this->extractUserId($update);

        // Log the update
        $log = TelegramLog::logUpdate($updateId, $telegramUserId, $eventType, $update);

        try {
            $this->bot->handleUpdate($update);

            $log->update([
                'status' => TelegramLog::STATUS_PROCESSED,
            ]);
        } catch (\Throwable $e) {
            Log::error('Telegram webhook processing error', [
                'update_id' => $updateId,
                'error' => $e->getMessage(),
                'trace' => $e->getTraceAsString(),
            ]);

            $log->update([
                'status' => TelegramLog::STATUS_FAILED,
                'error' => $e->getMessage(),
            ]);
        }

        // Always return 200 to prevent Telegram from retrying
        return response()->json(['status' => 'ok']);
    }

    /**
     * Determine the event type from the update payload.
     */
    private function determineEventType(array $update): string
    {
        if (isset($update['callback_query'])) {
            return TelegramLog::EVENT_CALLBACK;
        }

        if (isset($update['message']['text'])) {
            $text = $update['message']['text'];
            if (str_starts_with($text, '/')) {
                return TelegramLog::EVENT_COMMAND;
            }
        }

        return TelegramLog::EVENT_MESSAGE;
    }

    /**
     * Extract the Telegram user ID from the update payload.
     */
    private function extractUserId(array $update): ?int
    {
        if (isset($update['callback_query']['from']['id'])) {
            return $update['callback_query']['from']['id'];
        }

        if (isset($update['message']['from']['id'])) {
            return $update['message']['from']['id'];
        }

        return null;
    }
}
