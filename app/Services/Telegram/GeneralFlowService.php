<?php

namespace App\Services\Telegram;

use App\Models\PricingPlan;
use App\Models\Project;

class GeneralFlowService
{
    public function __construct(
        private TelegramApi $api,
    ) {}

    /**
     * Show pricing plans.
     */
    public function showPricing(int $chatId): void
    {
        $plans = PricingPlan::active()->get();

        $text = "💰 <b>Pricing Plans</b>\n\n"
            . "Select a plan to view details:";

        $buttons = [];
        foreach ($plans as $plan) {
            $buttons[] = [TelegramApi::inlineButton(
                $plan->button_label,
                "pricing:view:{$plan->id}"
            )];
        }

        $buttons[] = [TelegramApi::inlineButton('📝 Custom Quote', 'menu:quote')];
        $buttons[] = [TelegramApi::inlineButton('⬅️ Back', 'menu:main')];

        $this->api->sendMessage($chatId, $text, TelegramApi::inlineKeyboard($buttons));
    }

    /**
     * Show pricing plan details.
     */
    public function showPricingDetail(int $chatId, int $planId): void
    {
        $plan = PricingPlan::find($planId);

        if (! $plan) {
            $this->showPricing($chatId);
            return;
        }

        $text = "{$plan->emoji} <b>" . strtoupper($plan->name) . "</b>\n\n"
            . "<b>{$plan->formatted_price}</b>\n\n";

        if ($plan->features) {
            foreach ($plan->features as $feature) {
                $text .= "✓ {$feature}\n";
            }
        }

        $buttons = [
            [TelegramApi::inlineButton('Get Started', 'menu:quote')],
            [TelegramApi::inlineButton('📝 Get Custom Quote', 'menu:quote')],
            [TelegramApi::inlineButton('⬅️ Back', 'pricing:list')],
        ];

        $this->api->sendMessage($chatId, $text, TelegramApi::inlineKeyboard($buttons));
    }

    /**
     * Show projects list.
     */
    public function showProjects(int $chatId): void
    {
        $projects = Project::active()->get();

        $text = "🚀 <b>Projects & Demos</b>\n\n"
            . "Explore our recent work:";

        $buttons = [];
        foreach ($projects->chunk(2) as $chunk) {
            $row = [];
            foreach ($chunk as $project) {
                $row[] = TelegramApi::inlineButton(
                    $project->button_label,
                    "project:view:{$project->id}"
                );
            }
            $buttons[] = $row;
        }

        $buttons[] = [TelegramApi::inlineButton('⬅️ Back', 'menu:main')];

        $this->api->sendMessage($chatId, $text, TelegramApi::inlineKeyboard($buttons));
    }

    /**
     * Show project details.
     */
    public function showProjectDetail(int $chatId, int $projectId): void
    {
        $project = Project::find($projectId);

        if (! $project) {
            $this->showProjects($chatId);
            return;
        }

        $text = "{$project->emoji} <b>{$project->name}</b>\n\n"
            . "{$project->short_description}\n\n";
            
        if ($project->description) {
            $text .= "{$project->description}\n\n";
        }

        if ($project->technologies) {
            $text .= "<b>Tech:</b>\n"
                . implode(' • ', $project->technologies) . "\n\n";
        }

        $buttons = [];
        
        $linkButtons = [];
        if ($project->website_url) {
            $linkButtons[] = TelegramApi::inlineButton('🌐 Details', null, $project->website_url);
        }
        if ($project->source_code_url) {
            $linkButtons[] = TelegramApi::inlineButton('💻 Source Code', null, $project->source_code_url);
        }
        
        if (!empty($linkButtons)) {
            $buttons[] = $linkButtons;
        }

        $buttons[] = [TelegramApi::inlineButton('📋 Build Similar Project', 'menu:quote')];
        $buttons[] = [TelegramApi::inlineButton('⬅️ Back', 'project:list')];

        $this->api->sendMessage($chatId, $text, TelegramApi::inlineKeyboard($buttons));
    }

    /**
     * Show contact information.
     */
    public function showContact(int $chatId): void
    {
        $text = "📞 <b>Contact AmanProjects</b>\n\n"
            . "📱 WhatsApp: +91 7061029304\n"
            . "📧 Email: admin@amanprojects.com\n"
            . "🌐 Website: amanprojects.com\n"
            . "📍 Location: Saharsa, Bihar\n\n"
            . "Choose how you'd like to reach us:";

        $buttons = [
            [
                TelegramApi::inlineButton('💬 WhatsApp', null, 'https://wa.me/917061029304'),
                TelegramApi::inlineButton('📧 Email', null, 'mailto:admin@amanprojects.com'),
            ],
            [
                TelegramApi::inlineButton('🌐 Website', null, 'https://amanprojects.com'),
                TelegramApi::inlineButton('📍 Google Maps', 'menu:location'),
            ],
            [TelegramApi::inlineButton('⬅️ Back', 'menu:main')],
        ];

        $this->api->sendMessage($chatId, $text, TelegramApi::inlineKeyboard($buttons));
    }

    /**
     * Show location information.
     */
    public function showLocation(int $chatId): void
    {
        $text = "📍 <b>AmanProjects Office</b>\n\n"
            . "Menha Chowk, Main Road,\n"
            . "Sattar Kataiya,\n"
            . "Saharsa, Bihar – 852124\n"
            . "India";

        $buttons = [
            [TelegramApi::inlineButton('🗺 Open Google Maps', null, 'https://maps.google.com/?q=AmanProjects+Saharsa')],
            [TelegramApi::inlineButton('📞 Contact Us', 'menu:contact')],
            [TelegramApi::inlineButton('⬅️ Back', 'menu:main')],
        ];

        // Send location coordinates first
        $this->api->sendLocation($chatId, 25.8833, 86.6000); // Approximate coords for Saharsa

        // Then send the text message with buttons
        $this->api->sendMessage($chatId, $text, TelegramApi::inlineKeyboard($buttons));
    }
}
