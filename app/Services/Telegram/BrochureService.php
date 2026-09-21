<?php

namespace App\Services\Telegram;

use App\Models\Brochure;
use App\Models\BrochureDownload;
use App\Models\Service;
use Illuminate\Support\Facades\Log;

class BrochureService
{
    public function __construct(
        private TelegramApi $api,
    ) {}

    /**
     * Show the brochure selection menu.
     */
    public function showBrochureMenu(int $chatId): void
    {
        $services = Service::active()->get();

        $text = "📄 <b>Service Brochures</b>\n\n"
            . "Select a service to download its brochure:";

        $buttons = [];
        foreach ($services as $service) {
            $buttons[] = [TelegramApi::inlineButton(
                $service->button_label,
                "brochure:download:{$service->id}"
            )];
        }

        $buttons[] = [TelegramApi::inlineButton('⬅️ Back', 'menu:main')];

        $this->api->sendMessage($chatId, $text, TelegramApi::inlineKeyboard($buttons));
    }

    /**
     * Download and send a brochure PDF for a given service.
     */
    public function sendBrochure(int $chatId, int $serviceId, int $telegramUserId): void
    {
        $service = Service::find($serviceId);

        if (! $service) {
            $this->api->sendMessage($chatId, '❌ Service not found.');

            return;
        }

        $brochure = Brochure::where('service_id', $serviceId)
            ->active()
            ->latest()
            ->first();

        if (! $brochure) {
            $this->api->sendMessage(
                $chatId,
                "📄 <b>{$service->name} Brochure</b>\n\n⚠️ Brochure is currently being updated. Please check back later.",
                TelegramApi::inlineKeyboard([
                    [TelegramApi::inlineButton('📋 Get Quote', 'menu:quote')],
                    [TelegramApi::inlineButton('⬅️ Back', 'menu:brochures')],
                ])
            );

            return;
        }

        $caption = "📄 <b>{$service->name} Brochure</b>\n\n"
            . "Here is the complete AmanProjects\n"
            . "{$service->name} service brochure.";

        $replyMarkup = TelegramApi::inlineKeyboard([
            [TelegramApi::inlineButton('📋 Get Quote', 'menu:quote')],
            [TelegramApi::inlineButton('⬅️ Back', 'menu:brochures')],
        ]);

        $result = null;

        // If we have a cached Telegram file_id, use it (faster, no re-upload)
        if ($brochure->hasTelegramFileId()) {
            $result = $this->api->sendDocument(
                $chatId,
                $brochure->telegram_file_id,
                $caption,
                $replyMarkup,
            );
        } else {
            // Upload the file from disk
            $filePath = $brochure->full_path;

            if (! file_exists($filePath)) {
                Log::error('Brochure file not found', [
                    'brochure_id' => $brochure->id,
                    'file_path' => $filePath,
                ]);

                $this->api->sendMessage(
                    $chatId,
                    '⚠️ Brochure file is currently unavailable. Please try again later.',
                );

                return;
            }

            $result = $this->api->sendDocumentFile(
                $chatId,
                $filePath,
                $caption,
                $replyMarkup,
            );

            // Cache the Telegram file_id for future use
            if ($result && isset($result['document']['file_id'])) {
                $brochure->update([
                    'telegram_file_id' => $result['document']['file_id'],
                ]);
            }
        }

        // Track the download
        if ($result) {
            BrochureDownload::create([
                'telegram_user_id' => $telegramUserId,
                'brochure_id' => $brochure->id,
                'telegram_file_id' => $result['document']['file_id'] ?? null,
                'downloaded_at' => now(),
            ]);
        }
    }
}
