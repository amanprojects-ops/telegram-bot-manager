<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Telegram Bot Token
    |--------------------------------------------------------------------------
    |
    | The bot token provided by @BotFather on Telegram.
    |
    */
    'bot_token' => env('TELEGRAM_BOT_TOKEN'),

    /*
    |--------------------------------------------------------------------------
    | Telegram Bot Username
    |--------------------------------------------------------------------------
    |
    | The bot's username without the @ symbol.
    |
    */
    'bot_username' => env('TELEGRAM_BOT_USERNAME'),

    /*
    |--------------------------------------------------------------------------
    | Webhook Secret
    |--------------------------------------------------------------------------
    |
    | A secret token used in the webhook URL path and as the
    | X-Telegram-Bot-Api-Secret-Token header for verification.
    |
    */
    'webhook_secret' => env('TELEGRAM_WEBHOOK_SECRET'),

    /*
    |--------------------------------------------------------------------------
    | Admin Chat ID
    |--------------------------------------------------------------------------
    |
    | The Telegram chat ID of the admin who receives lead notifications,
    | new quote alerts, and can use admin-only commands.
    |
    */
    'admin_chat_id' => env('TELEGRAM_ADMIN_CHAT_ID'),

    /*
    |--------------------------------------------------------------------------
    | Telegram Bot API Base URL
    |--------------------------------------------------------------------------
    */
    'api_base_url' => 'https://api.telegram.org/bot',

    /*
    |--------------------------------------------------------------------------
    | AmanProjects Website API
    |--------------------------------------------------------------------------
    |
    | Base URL for the AmanProjects website API to fetch technologies,
    | blogs, and other dynamic content.
    |
    */
    'amanprojects_api_url' => env('AMANPROJECTS_API_URL', 'https://amanprojects.com/api'),

    /*
    |--------------------------------------------------------------------------
    | Broadcast Rate Limit
    |--------------------------------------------------------------------------
    |
    | Maximum number of messages to send per second during broadcasts.
    | Telegram allows up to 30 messages/second to different users.
    |
    */
    'broadcast_rate_limit' => 25,

    /*
    |--------------------------------------------------------------------------
    | Follow-up Schedule (in hours)
    |--------------------------------------------------------------------------
    */
    'followup_first' => 24,
    'followup_second' => 72,

    /*
    |--------------------------------------------------------------------------
    | Lead ID Prefix
    |--------------------------------------------------------------------------
    */
    'lead_prefix' => 'AP',

];
