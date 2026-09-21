<?php

namespace App\Services\Telegram;

use App\Models\TelegramSession;
use App\Models\TelegramUser;

class CallbackHandler
{
    public function __construct(
        private TelegramApi $api,
        private MessageHandler $messageHandler,
    ) {}

    /**
     * Handle a callback query from an inline button.
     */
    public function handle(
        int $chatId,
        ?int $messageId,
        string $data,
        TelegramUser $user,
        TelegramSession $session,
    ): void {
        // Parse callback data format: "action:value"
        $parts = explode(':', $data, 2);
        $action = $parts[0] ?? '';
        $value = $parts[1] ?? '';

        match ($action) {
            'menu' => $this->handleMenuAction($chatId, $messageId, $value, $user, $session),
            'service' => $this->handleServiceAction($chatId, $messageId, $value, $user, $session),
            'brochure' => $this->handleBrochureAction($chatId, $messageId, $value, $user, $session),
            'pricing' => $this->handlePricingAction($chatId, $messageId, $value, $user, $session),
            'project' => $this->handleProjectAction($chatId, $messageId, $value, $user, $session),
            'quote' => $this->handleQuoteAction($chatId, $messageId, $value, $user, $session),
            'cyber' => $this->handleCyberAction($chatId, $messageId, $value, $user, $session),
            'lead' => $this->handleLeadAction($chatId, $messageId, $value, $user, $session),
            'faq' => $this->handleFaqAction($chatId, $messageId, $value, $user, $session),
            'admin' => $this->handleAdminAction($chatId, $messageId, $value, $user, $session),
            default => $this->messageHandler->sendMainMenu($chatId),
        };
    }

    /**
     * Handle menu navigation callbacks.
     */
    private function handleMenuAction(
        int $chatId,
        ?int $messageId,
        string $value,
        TelegramUser $user,
        TelegramSession $session,
    ): void {
        // Will be extended in feature commits
        match ($value) {
            'main' => $this->messageHandler->sendMainMenu($chatId),
            default => $this->messageHandler->sendMainMenu($chatId),
        };
    }

    /**
     * Service-related callbacks. Will be implemented in commit 13.
     */
    private function handleServiceAction(
        int $chatId,
        ?int $messageId,
        string $value,
        TelegramUser $user,
        TelegramSession $session,
    ): void {
        $this->messageHandler->sendMainMenu($chatId);
    }

    /**
     * Brochure-related callbacks. Will be implemented in commit 14.
     */
    private function handleBrochureAction(
        int $chatId,
        ?int $messageId,
        string $value,
        TelegramUser $user,
        TelegramSession $session,
    ): void {
        $this->messageHandler->sendMainMenu($chatId);
    }

    /**
     * Pricing-related callbacks. Will be implemented in commit 16.
     */
    private function handlePricingAction(
        int $chatId,
        ?int $messageId,
        string $value,
        TelegramUser $user,
        TelegramSession $session,
    ): void {
        $this->messageHandler->sendMainMenu($chatId);
    }

    /**
     * Project-related callbacks. Will be implemented in commit 16.
     */
    private function handleProjectAction(
        int $chatId,
        ?int $messageId,
        string $value,
        TelegramUser $user,
        TelegramSession $session,
    ): void {
        $this->messageHandler->sendMainMenu($chatId);
    }

    /**
     * Quote-related callbacks. Will be implemented in commit 15.
     */
    private function handleQuoteAction(
        int $chatId,
        ?int $messageId,
        string $value,
        TelegramUser $user,
        TelegramSession $session,
    ): void {
        $this->messageHandler->sendMainMenu($chatId);
    }

    /**
     * Cyber security callbacks. Will be implemented in commit 17.
     */
    private function handleCyberAction(
        int $chatId,
        ?int $messageId,
        string $value,
        TelegramUser $user,
        TelegramSession $session,
    ): void {
        $this->messageHandler->sendMainMenu($chatId);
    }

    /**
     * Lead admin callbacks (mark contacted, priority, etc.).
     */
    private function handleLeadAction(
        int $chatId,
        ?int $messageId,
        string $value,
        TelegramUser $user,
        TelegramSession $session,
    ): void {
        $this->messageHandler->sendMainMenu($chatId);
    }

    /**
     * FAQ callbacks. Will be implemented in commit 27.
     */
    private function handleFaqAction(
        int $chatId,
        ?int $messageId,
        string $value,
        TelegramUser $user,
        TelegramSession $session,
    ): void {
        $this->messageHandler->sendMainMenu($chatId);
    }

    /**
     * Admin-only callbacks. Will be implemented in commit 18.
     */
    private function handleAdminAction(
        int $chatId,
        ?int $messageId,
        string $value,
        TelegramUser $user,
        TelegramSession $session,
    ): void {
        $this->messageHandler->sendMainMenu($chatId);
    }
}
