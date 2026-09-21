<?php

namespace App\Services\Telegram;

use App\Models\Service;
use App\Models\TelegramSession;
use App\Models\TelegramUser;

class CallbackHandler
{
    public function __construct(
        private TelegramApi $api,
        private MessageHandler $messageHandler,
        private BrochureService $brochureService,
        private LeadService $leadService,
        private GeneralFlowService $generalFlowService,
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
        match ($value) {
            'main' => $this->messageHandler->sendMainMenu($chatId),
            'services' => $this->showServicesList($chatId),
            'brochures' => $this->brochureService->showBrochureMenu($chatId),
            'pricing' => $this->generalFlowService->showPricing($chatId),
            'projects' => $this->generalFlowService->showProjects($chatId),
            'quote' => $this->leadService->startQuoteFlow($chatId, $session),
            'contact' => $this->generalFlowService->showContact($chatId),
            'location' => $this->generalFlowService->showLocation($chatId),
            default => $this->messageHandler->sendMainMenu($chatId),
        };
    }

    /**
     * Show the list of all active services.
     */
    private function showServicesList(int $chatId): void
    {
        $services = Service::active()->get();

        $text = "🛠 <b>Our Services</b>\n\n"
            . "Select a service to learn more:";

        $buttons = [];
        foreach ($services->chunk(2) as $chunk) {
            $row = [];
            foreach ($chunk as $service) {
                $row[] = TelegramApi::inlineButton(
                    $service->button_label,
                    "service:view:{$service->id}"
                );
            }
            $buttons[] = $row;
        }

        $buttons[] = [TelegramApi::inlineButton('⬅️ Back', 'menu:main')];

        $this->api->sendMessage($chatId, $text, TelegramApi::inlineKeyboard($buttons));
    }

    /**
     * Handle service-related callbacks.
     */
    private function handleServiceAction(
        int $chatId,
        ?int $messageId,
        string $value,
        TelegramUser $user,
        TelegramSession $session,
    ): void {
        // Parse sub-action: "view:ID"
        $parts = explode(':', $value, 2);
        $subAction = $parts[0] ?? '';
        $serviceId = $parts[1] ?? '';

        match ($subAction) {
            'view' => $this->showServiceDetail($chatId, (int) $serviceId),
            'list' => $this->showServicesList($chatId),
            default => $this->showServicesList($chatId),
        };
    }

    /**
     * Show detailed information about a specific service.
     */
    private function showServiceDetail(int $chatId, int $serviceId): void
    {
        $service = Service::find($serviceId);

        if (! $service) {
            $this->showServicesList($chatId);
            return;
        }

        $text = "{$service->emoji} <b>{$service->name}</b>\n\n"
            . "{$service->description}\n\n";

        if ($service->technologies) {
            $text .= "<b>Technology:</b>\n"
                . implode(' • ', $service->technologies) . "\n\n";
        }

        if ($service->suitable_for) {
            $text .= "<b>Suitable for:</b>\n";
            foreach ($service->suitable_for as $item) {
                $text .= "✓ {$item}\n";
            }
        }

        $buttons = [
            [TelegramApi::inlineButton('📄 Download Service PDF', "brochure:download:{$service->id}")],
            [
                TelegramApi::inlineButton('💰 Pricing', 'menu:pricing'),
                TelegramApi::inlineButton('📋 Get Quote', 'menu:quote'),
            ],
        ];

        if ($service->website_url) {
            $buttons[] = [TelegramApi::inlineButton('🌐 View Website', null, $service->website_url)];
        }

        $buttons[] = [TelegramApi::inlineButton('⬅️ Back', 'service:list')];

        $this->api->sendMessage($chatId, $text, TelegramApi::inlineKeyboard($buttons));
    }

    /**
     * Handle brochure-related callbacks.
     */
    private function handleBrochureAction(
        int $chatId,
        ?int $messageId,
        string $value,
        TelegramUser $user,
        TelegramSession $session,
    ): void {
        // Parse: "download:ID" or "menu"
        $parts = explode(':', $value, 2);
        $subAction = $parts[0] ?? '';
        $serviceId = $parts[1] ?? '';

        match ($subAction) {
            'download' => $this->brochureService->sendBrochure($chatId, (int) $serviceId, $user->telegram_user_id),
            'menu' => $this->brochureService->showBrochureMenu($chatId),
            default => $this->brochureService->showBrochureMenu($chatId),
        };
    }

    /**
     * Handle pricing-related callbacks.
     */
    private function handlePricingAction(
        int $chatId,
        ?int $messageId,
        string $value,
        TelegramUser $user,
        TelegramSession $session,
    ): void {
        $parts = explode(':', $value, 2);
        $subAction = $parts[0] ?? '';
        $subValue = $parts[1] ?? '';

        match ($subAction) {
            'list' => $this->generalFlowService->showPricing($chatId),
            'view' => $this->generalFlowService->showPricingDetail($chatId, (int) $subValue),
            default => $this->generalFlowService->showPricing($chatId),
        };
    }

    /**
     * Handle project-related callbacks.
     */
    private function handleProjectAction(
        int $chatId,
        ?int $messageId,
        string $value,
        TelegramUser $user,
        TelegramSession $session,
    ): void {
        $parts = explode(':', $value, 2);
        $subAction = $parts[0] ?? '';
        $subValue = $parts[1] ?? '';

        match ($subAction) {
            'list' => $this->generalFlowService->showProjects($chatId),
            'view' => $this->generalFlowService->showProjectDetail($chatId, (int) $subValue),
            default => $this->generalFlowService->showProjects($chatId),
        };
    }

    /**
     * Handle quote-related callbacks.
     */
    private function handleQuoteAction(
        int $chatId,
        ?int $messageId,
        string $value,
        TelegramUser $user,
        TelegramSession $session,
    ): void {
        $parts = explode(':', $value, 2);
        $subAction = $parts[0] ?? '';
        $subValue = $parts[1] ?? '';

        match ($subAction) {
            'service' => $this->leadService->handleServiceSelection($chatId, $subValue, $session),
            'budget' => $this->leadService->handleBudgetSelection($chatId, $subValue, $session),
            'submit' => $this->leadService->submitQuote($chatId, $user, $session),
            'edit' => $this->leadService->startQuoteFlow($chatId, $session),
            'cancel' => $this->leadService->cancelQuote($chatId, $session),
            default => $this->leadService->startQuoteFlow($chatId, $session),
        };
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
     * Handle lead admin callbacks (mark contacted, priority).
     */
    private function handleLeadAction(
        int $chatId,
        ?int $messageId,
        string $value,
        TelegramUser $user,
        TelegramSession $session,
    ): void {
        $parts = explode(':', $value, 2);
        $subAction = $parts[0] ?? '';
        $leadId = (int) ($parts[1] ?? 0);

        $lead = \App\Models\TelegramLead::find($leadId);
        if (! $lead) {
            $this->api->sendMessage($chatId, '❌ Lead not found.');
            return;
        }

        match ($subAction) {
            'contacted' => $this->markLeadContacted($chatId, $lead),
            'priority' => $this->markLeadPriority($chatId, $lead),
            default => null,
        };
    }

    private function markLeadContacted(int $chatId, TelegramLead $lead): void
    {
        $lead->markContacted();
        $this->api->sendMessage($chatId, "✅ Lead <b>{$lead->lead_id}</b> marked as contacted.");
    }

    private function markLeadPriority(int $chatId, TelegramLead $lead): void
    {
        $lead->markPriority();
        $this->api->sendMessage($chatId, "⭐ Lead <b>{$lead->lead_id}</b> marked as high priority.");
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
