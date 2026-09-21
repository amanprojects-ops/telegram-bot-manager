<?php

namespace App\Services\Telegram;

use App\Models\Service;
use App\Models\TelegramLead;
use App\Models\TelegramSession;
use App\Models\TelegramUser;
use Illuminate\Support\Facades\Log;

class LeadService
{
    public function __construct(
        private TelegramApi $api,
    ) {}

    /**
     * Start the quote flow — show service selection.
     */
    public function startQuoteFlow(int $chatId, TelegramSession $session): void
    {
        $session->transitionTo(TelegramSession::STATE_QUOTE_SERVICE);

        $text = "📋 <b>Get a Quote</b>\n\n"
            . "Great! Let's understand your requirement.\n\n"
            . "What do you need?";

        $services = Service::active()->get();
        $buttons = [];

        foreach ($services as $service) {
            $buttons[] = [TelegramApi::inlineButton(
                $service->button_label,
                "quote:service:{$service->id}"
            )];
        }

        $buttons[] = [TelegramApi::inlineButton('🔧 Other', 'quote:service:other')];
        $buttons[] = [TelegramApi::inlineButton('❌ Cancel', 'quote:cancel')];

        $this->api->sendMessage($chatId, $text, TelegramApi::inlineKeyboard($buttons));
    }

    /**
     * Handle service selection in the quote flow.
     */
    public function handleServiceSelection(int $chatId, string $serviceValue, TelegramSession $session): void
    {
        $serviceName = 'Other';
        $serviceId = null;

        if ($serviceValue !== 'other') {
            $service = Service::find((int) $serviceValue);
            if ($service) {
                $serviceName = $service->name;
                $serviceId = $service->id;
            }
        }

        $session->transitionTo(TelegramSession::STATE_QUOTE_BUDGET, [
            'service_id' => $serviceId,
            'service_name' => $serviceName,
        ]);

        $text = "💰 <b>Budget</b>\n\n"
            . "What is your estimated budget?";

        $buttons = TelegramApi::inlineKeyboard([
            [TelegramApi::inlineButton('< ₹10K', 'quote:budget:< ₹10K')],
            [TelegramApi::inlineButton('₹10K–₹25K', 'quote:budget:₹10K–₹25K')],
            [TelegramApi::inlineButton('₹25K–₹50K', 'quote:budget:₹25K–₹50K')],
            [TelegramApi::inlineButton('₹50K–₹1L', 'quote:budget:₹50K–₹1L')],
            [TelegramApi::inlineButton('₹1L+', 'quote:budget:₹1L+')],
            [TelegramApi::inlineButton('Not Sure', 'quote:budget:Not Sure')],
            [TelegramApi::inlineButton('❌ Cancel', 'quote:cancel')],
        ]);

        $this->api->sendMessage($chatId, $text, $buttons);
    }

    /**
     * Handle budget selection.
     */
    public function handleBudgetSelection(int $chatId, string $budget, TelegramSession $session): void
    {
        $session->transitionTo(TelegramSession::STATE_QUOTE_REQUIREMENT, [
            'budget' => $budget,
        ]);

        $text = "📝 <b>Requirement</b>\n\n"
            . "Tell us briefly about your project.\n\n"
            . "<i>Type your requirement below:</i>";

        $this->api->sendMessage($chatId, $text);
    }

    /**
     * Handle requirement text input.
     */
    public function handleRequirement(int $chatId, string $requirement, TelegramSession $session): void
    {
        $session->transitionTo(TelegramSession::STATE_QUOTE_NAME, [
            'requirement' => $requirement,
        ]);

        $text = "👤 <b>Your Name</b>\n\n"
            . "What is your name?";

        $this->api->sendMessage($chatId, $text);
    }

    /**
     * Handle name input.
     */
    public function handleName(int $chatId, string $name, TelegramSession $session): void
    {
        $session->transitionTo(TelegramSession::STATE_QUOTE_PHONE, [
            'name' => $name,
        ]);

        $text = "📱 <b>Contact</b>\n\n"
            . "Phone / WhatsApp number?";

        $this->api->sendMessage($chatId, $text);
    }

    /**
     * Handle phone input and show summary.
     */
    public function handlePhone(int $chatId, string $phone, TelegramSession $session): void
    {
        $session->transitionTo(TelegramSession::STATE_QUOTE_CONFIRM, [
            'phone' => $phone,
        ]);

        $payload = $session->payload;

        $text = "📋 <b>Requirement Summary</b>\n\n"
            . "👤 <b>Name:</b> {$payload['name']}\n"
            . "🛠 <b>Service:</b> {$payload['service_name']}\n"
            . "💰 <b>Budget:</b> {$payload['budget']}\n"
            . "📝 <b>Requirement:</b> {$payload['requirement']}\n"
            . "📱 <b>Phone:</b> {$payload['phone']}";

        $buttons = TelegramApi::inlineKeyboard([
            [TelegramApi::inlineButton('✅ Submit', 'quote:submit')],
            [TelegramApi::inlineButton('✏️ Edit', 'quote:edit')],
            [TelegramApi::inlineButton('❌ Cancel', 'quote:cancel')],
        ]);

        $this->api->sendMessage($chatId, $text, $buttons);
    }

    /**
     * Submit the quote — create lead, notify admin.
     */
    public function submitQuote(int $chatId, TelegramUser $user, TelegramSession $session): void
    {
        $payload = $session->payload;

        try {
            $lead = TelegramLead::create([
                'lead_id' => TelegramLead::generateLeadId(),
                'telegram_user_id' => $user->telegram_user_id,
                'service_id' => $payload['service_id'] ?? null,
                'name' => $payload['name'] ?? null,
                'phone' => $payload['phone'] ?? null,
                'budget' => $payload['budget'] ?? null,
                'requirement' => $payload['requirement'] ?? null,
                'source' => 'telegram',
                'status' => TelegramLead::STATUS_NEW,
            ]);

            // Update user's phone if provided
            if (! empty($payload['phone']) && empty($user->phone)) {
                $user->update(['phone' => $payload['phone']]);
            }

            // Reset session
            $session->reset();

            // Send confirmation to user
            $text = "✅ <b>Requirement received!</b>\n\n"
                . "Our team will review your requirement\n"
                . "and contact you.\n\n"
                . "🆔 <b>Lead ID:</b> {$lead->lead_id}";

            $this->api->sendMessage(
                $chatId,
                $text,
                TelegramApi::inlineKeyboard([
                    [TelegramApi::inlineButton('🏠 Main Menu', 'menu:main')],
                ])
            );

            // Notify admin
            $this->notifyAdminNewLead($lead, $user);

        } catch (\Throwable $e) {
            Log::error('Failed to submit quote', [
                'error' => $e->getMessage(),
                'payload' => $payload,
            ]);

            $this->api->sendMessage($chatId, '❌ Something went wrong. Please try again.');
            $session->reset();
        }
    }

    /**
     * Cancel the quote flow.
     */
    public function cancelQuote(int $chatId, TelegramSession $session): void
    {
        $session->reset();

        $this->api->sendMessage(
            $chatId,
            "❌ <b>Quote cancelled.</b>\n\nYou can start again anytime.",
            TelegramApi::inlineKeyboard([
                [TelegramApi::inlineButton('🏠 Main Menu', 'menu:main')],
            ])
        );
    }

    /**
     * Handle stateful message input during quote flow.
     */
    public function handleStatefulInput(
        int $chatId,
        string $text,
        TelegramUser $user,
        TelegramSession $session,
    ): bool {
        return match ($session->state) {
            TelegramSession::STATE_QUOTE_REQUIREMENT => $this->processRequirement($chatId, $text, $session),
            TelegramSession::STATE_QUOTE_NAME => $this->processName($chatId, $text, $session),
            TelegramSession::STATE_QUOTE_PHONE => $this->processPhone($chatId, $text, $session),
            default => false,
        };
    }

    private function processRequirement(int $chatId, string $text, TelegramSession $session): bool
    {
        $this->handleRequirement($chatId, $text, $session);

        return true;
    }

    private function processName(int $chatId, string $text, TelegramSession $session): bool
    {
        $this->handleName($chatId, $text, $session);

        return true;
    }

    private function processPhone(int $chatId, string $text, TelegramSession $session): bool
    {
        $this->handlePhone($chatId, $text, $session);

        return true;
    }

    /**
     * Notify admin about a new lead.
     */
    public function notifyAdminNewLead(TelegramLead $lead, TelegramUser $user): void
    {
        $adminChatId = config('telegram.admin_chat_id');

        if (! $adminChatId) {
            return;
        }

        $serviceName = $lead->service ? $lead->service->name : 'Other';

        $text = "🚨 <b>NEW LEAD</b>\n\n"
            . "🆔 <b>Lead:</b> {$lead->lead_id}\n\n"
            . "👤 <b>Name:</b> {$lead->name}\n"
            . "📱 <b>Phone:</b> {$lead->phone}\n"
            . "🛠 <b>Service:</b> {$serviceName}\n"
            . "💰 <b>Budget:</b> {$lead->budget}\n"
            . "📝 <b>Requirement:</b> {$lead->requirement}\n"
            . "📅 <b>Date:</b> " . now()->format('d M Y, h:i A') . "\n"
            . "📍 <b>Source:</b> Telegram";

        $buttons = TelegramApi::inlineKeyboard([
            [TelegramApi::inlineButton('📞 Contact', null, "tel:{$lead->phone}")],
            [
                TelegramApi::inlineButton('✅ Mark Contacted', "lead:contacted:{$lead->id}"),
                TelegramApi::inlineButton('⭐ High Priority', "lead:priority:{$lead->id}"),
            ],
        ]);

        $this->api->sendMessage($adminChatId, $text, $buttons);
    }
}
