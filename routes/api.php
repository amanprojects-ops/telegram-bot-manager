<?php

use App\Http\Controllers\Telegram\WebhookController;
use Illuminate\Support\Facades\Route;

/*
|--------------------------------------------------------------------------
| API Routes
|--------------------------------------------------------------------------
*/

// Telegram Bot Webhook
Route::post('/telegram/webhook/{secret}', [WebhookController::class, 'handle'])
    ->name('telegram.webhook');
