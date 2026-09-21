<?php

namespace App\Services\Telegram;

use App\Models\TelegramSession;
use App\Models\TelegramUser;

class MessageHandler
{
    public function __construct(
        private TelegramApi $api,
        private LeadService $leadService,
    ) {}

    /**
     * Handle a bot command.
     */
    public function handleCommand(
        int $chatId,
        string $text,
        TelegramUser $user,
        TelegramSession $session,
    ): void {
        // Parse command and arguments
        $parts = explode(' ', $text, 2);
        $command = strtolower($parts[0]);
        $args = $parts[1] ?? '';

        match ($command) {
            '/start' => $this->handleStart($chatId, $args, $user, $session),
            '/help' => $this->sendMainMenu($chatId),
            '/menu' => $this->sendMainMenu($chatId),
            '/cancel' => $this->handleCancel($chatId, $session),
            default => $this->sendMainMenu($chatId),
        };
    }

    /**
     * Handle /start command with optional deep link parameter.
     */
    public function handleStart(
        int $chatId,
        string $args,
        TelegramUser $user,
        TelegramSession $session,
    ): void {
        // Reset any active session
        $session->reset();

        // If deep link parameter provided, handle it
        if (! empty($args)) {
            $this->handleDeepLink($chatId, $args, $user, $session);

            return;
        }

        $this->sendMainMenu($chatId);
    }

    /**
     * Handle deep link parameters (e.g., /start=web).
     */
    public function handleDeepLink(
        int $chatId,
        string $parameter,
        TelegramUser $user,
        TelegramSession $session,
    ): void {
        // Log the referral or source if it's a campaign link
        if (str_starts_with($parameter, 'campaign_')) {
            $campaign = str_replace('campaign_', '', $parameter);
            // Example: User::where('id', $user->id)->update(['source' => $campaign]);
            // For now, we'll just acknowledge it and go to main menu
            $this->api->sendMessage($chatId, "🎉 Welcome from the {$campaign} campaign!");
            $this->sendMainMenu($chatId);
            return;
        }

        // Direct service quote deep links
        if (str_starts_with($parameter, 'quote_')) {
            $serviceName = str_replace('quote_', '', $parameter);
            // Simulate pressing the quote button for a service
            $this->leadService->startQuoteFlow($chatId, $session);
            return;
        }

        // Default behavior
        $this->sendMainMenu($chatId);
    }

    /**
     * Handle stateful messages (conversation state machine).
     */
    public function handleStatefulMessage(
        int $chatId,
        string $text,
        TelegramUser $user,
        TelegramSession $session,
    ): void {
        $handled = $this->leadService->handleStatefulInput($chatId, $text, $user, $session);

        if (! $handled) {
            $this->sendMainMenu($chatId);
        }
    }

    /**
     * Handle /cancel command.
     */
    public function handleCancel(int $chatId, TelegramSession $session): void
    {
        $session->reset();

        $this->api->sendMessage(
            $chatId,
            "❌ <b>Cancelled</b>\n\nOperation cancelled. You can start again anytime.",
            TelegramApi::inlineKeyboard([
                [TelegramApi::inlineButton('🏠 Main Menu', 'menu:main')],
            ])
        );
    }

    /**
     * Send the main menu.
     */
    public function sendMainMenu(int $chatId): void
    {
        $text = "🤖 <b>AmanProjects</b>\n\n"
            . "👋 Welcome!\n\n"
            . "<b>We Build. We Scale. We Secure.</b>\n\n"
            . "━━━━━━━━━━━━━━━━\n\n"
            . "Choose an option below:";

        $keyboard = TelegramApi::inlineKeyboard([
            [
                TelegramApi::inlineButton('🛠 Services', 'menu:services'),
                TelegramApi::inlineButton('💰 Pricing', 'menu:pricing'),
            ],
            [
                TelegramApi::inlineButton('🚀 Projects', 'menu:projects'),
                TelegramApi::inlineButton('📄 Brochures', 'menu:brochures'),
            ],
            [
                TelegramApi::inlineButton('💻 Technologies', 'menu:technologies'),
                TelegramApi::inlineButton('🛡 Cyber Security', 'menu:cybersecurity'),
            ],
            [
                TelegramApi::inlineButton('📋 Get a Quote', 'menu:quote'),
                TelegramApi::inlineButton('📞 Contact Us', 'menu:contact'),
            ],
            [
                TelegramApi::inlineButton('📍 Location', 'menu:location'),
                TelegramApi::inlineButton('📰 Blog', 'menu:blog'),
            ],
            [
                TelegramApi::inlineButton('❓ FAQ', 'menu:faq'),
                TelegramApi::inlineButton('ℹ️ About Us', 'menu:about'),
            ],
            [
                TelegramApi::inlineButton('🌐 amanprojects.com', null, 'https://amanprojects.com'),
            ],
        ]);

        $this->api->sendMessage($chatId, $text, $keyboard);
    }
}
