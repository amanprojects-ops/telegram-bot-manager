<?php

namespace App\Services\Telegram;

use Illuminate\Http\Client\PendingRequest;
use Illuminate\Http\Client\Response;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Log;

class TelegramApi
{
    protected string $baseUrl;

    protected string $token;

    public function __construct()
    {
        $this->token = config('telegram.bot_token');
        $this->baseUrl = config('telegram.api_base_url') . $this->token;
    }

    /**
     * Send a text message.
     *
     * @param  array<string, mixed>|null  $replyMarkup
     */
    public function sendMessage(
        int|string $chatId,
        string $text,
        ?array $replyMarkup = null,
        string $parseMode = 'HTML',
        ?bool $disableWebPagePreview = true,
    ): ?array {
        $params = [
            'chat_id' => $chatId,
            'text' => $text,
            'parse_mode' => $parseMode,
        ];

        if ($disableWebPagePreview) {
            $params['disable_web_page_preview'] = true;
        }

        if ($replyMarkup) {
            $params['reply_markup'] = json_encode($replyMarkup);
        }

        return $this->post('sendMessage', $params);
    }

    /**
     * Send a document (PDF, file, etc.).
     */
    public function sendDocument(
        int|string $chatId,
        string $document,
        ?string $caption = null,
        ?array $replyMarkup = null,
        string $parseMode = 'HTML',
    ): ?array {
        $params = [
            'chat_id' => $chatId,
            'document' => $document,
            'parse_mode' => $parseMode,
        ];

        if ($caption) {
            $params['caption'] = $caption;
        }

        if ($replyMarkup) {
            $params['reply_markup'] = json_encode($replyMarkup);
        }

        return $this->post('sendDocument', $params);
    }

    /**
     * Send a document by uploading a file from disk.
     */
    public function sendDocumentFile(
        int|string $chatId,
        string $filePath,
        ?string $caption = null,
        ?array $replyMarkup = null,
        string $parseMode = 'HTML',
    ): ?array {
        $params = [
            'chat_id' => $chatId,
            'parse_mode' => $parseMode,
        ];

        if ($caption) {
            $params['caption'] = $caption;
        }

        if ($replyMarkup) {
            $params['reply_markup'] = json_encode($replyMarkup);
        }

        $response = Http::timeout(30)
            ->attach('document', file_get_contents($filePath), basename($filePath))
            ->post("{$this->baseUrl}/sendDocument", $params);

        return $this->handleResponse($response, 'sendDocument (file upload)');
    }

    /**
     * Send a photo.
     */
    public function sendPhoto(
        int|string $chatId,
        string $photo,
        ?string $caption = null,
        ?array $replyMarkup = null,
        string $parseMode = 'HTML',
    ): ?array {
        $params = [
            'chat_id' => $chatId,
            'photo' => $photo,
            'parse_mode' => $parseMode,
        ];

        if ($caption) {
            $params['caption'] = $caption;
        }

        if ($replyMarkup) {
            $params['reply_markup'] = json_encode($replyMarkup);
        }

        return $this->post('sendPhoto', $params);
    }

    /**
     * Send a location.
     */
    public function sendLocation(
        int|string $chatId,
        float $latitude,
        float $longitude,
        ?array $replyMarkup = null,
    ): ?array {
        $params = [
            'chat_id' => $chatId,
            'latitude' => $latitude,
            'longitude' => $longitude,
        ];

        if ($replyMarkup) {
            $params['reply_markup'] = json_encode($replyMarkup);
        }

        return $this->post('sendLocation', $params);
    }

    /**
     * Answer a callback query (acknowledge button press).
     */
    public function answerCallbackQuery(
        string $callbackQueryId,
        ?string $text = null,
        bool $showAlert = false,
    ): ?array {
        $params = [
            'callback_query_id' => $callbackQueryId,
            'show_alert' => $showAlert,
        ];

        if ($text) {
            $params['text'] = $text;
        }

        return $this->post('answerCallbackQuery', $params);
    }

    /**
     * Edit a message's text.
     *
     * @param  array<string, mixed>|null  $replyMarkup
     */
    public function editMessageText(
        int|string $chatId,
        int $messageId,
        string $text,
        ?array $replyMarkup = null,
        string $parseMode = 'HTML',
    ): ?array {
        $params = [
            'chat_id' => $chatId,
            'message_id' => $messageId,
            'text' => $text,
            'parse_mode' => $parseMode,
        ];

        if ($replyMarkup) {
            $params['reply_markup'] = json_encode($replyMarkup);
        }

        return $this->post('editMessageText', $params);
    }

    /**
     * Delete a message.
     */
    public function deleteMessage(int|string $chatId, int $messageId): ?array
    {
        return $this->post('deleteMessage', [
            'chat_id' => $chatId,
            'message_id' => $messageId,
        ]);
    }

    /**
     * Set the webhook URL.
     */
    public function setWebhook(string $url, ?string $secretToken = null): ?array
    {
        $params = ['url' => $url];

        if ($secretToken) {
            $params['secret_token'] = $secretToken;
        }

        return $this->post('setWebhook', $params);
    }

    /**
     * Delete the webhook.
     */
    public function deleteWebhook(bool $dropPendingUpdates = false): ?array
    {
        return $this->post('deleteWebhook', [
            'drop_pending_updates' => $dropPendingUpdates,
        ]);
    }

    /**
     * Get webhook info.
     */
    public function getWebhookInfo(): ?array
    {
        return $this->post('getWebhookInfo');
    }

    /**
     * Get bot information.
     */
    public function getMe(): ?array
    {
        return $this->post('getMe');
    }

    /**
     * Set bot commands menu.
     *
     * @param  array<int, array{command: string, description: string}>  $commands
     */
    public function setMyCommands(array $commands): ?array
    {
        return $this->post('setMyCommands', [
            'commands' => json_encode($commands),
        ]);
    }

    /**
     * Build an inline keyboard markup.
     *
     * @param  array<int, array<int, array{text: string, callback_data?: string, url?: string}>>  $buttons
     * @return array{inline_keyboard: array<int, array<int, array{text: string, callback_data?: string, url?: string}>>}
     */
    public static function inlineKeyboard(array $buttons): array
    {
        return ['inline_keyboard' => $buttons];
    }

    /**
     * Build a single inline button.
     *
     * @return array{text: string, callback_data?: string, url?: string}
     */
    public static function inlineButton(string $text, ?string $callbackData = null, ?string $url = null): array
    {
        $button = ['text' => $text];

        if ($callbackData !== null) {
            $button['callback_data'] = $callbackData;
        }

        if ($url !== null) {
            $button['url'] = $url;
        }

        return $button;
    }

    /**
     * Make a POST request to the Telegram Bot API.
     *
     * @param  array<string, mixed>  $params
     */
    protected function post(string $method, array $params = []): ?array
    {
        $response = $this->httpClient()
            ->post("{$this->baseUrl}/{$method}", $params);

        return $this->handleResponse($response, $method);
    }

    /**
     * Handle the Telegram API response.
     */
    protected function handleResponse(Response $response, string $method): ?array
    {
        $data = $response->json();

        if (! $response->successful() || ! ($data['ok'] ?? false)) {
            Log::error("Telegram API error [{$method}]", [
                'status' => $response->status(),
                'response' => $data,
            ]);

            return null;
        }

        return $data['result'] ?? $data;
    }

    /**
     * Get the HTTP client instance.
     */
    protected function httpClient(): PendingRequest
    {
        return Http::timeout(30)->retry(2, 100);
    }
}
