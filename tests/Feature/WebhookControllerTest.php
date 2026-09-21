<?php

use App\Models\TelegramUser;
use App\Models\TelegramLog;
use Illuminate\Support\Facades\Config;

beforeEach(function () {
    Config::set('telegram.webhook_secret', 'test-secret-key');
});

it('rejects requests without the correct secret token', function () {
    $response = $this->postJson('/api/telegram/webhook/wrong-secret', []);
    
    $response->assertStatus(403)
             ->assertJson(['status' => 'unauthorized']);
});

it('rejects requests with an invalid secret token', function () {
    // Correct URL secret but wrong header token
    $response = $this->withHeaders([
        'X-Telegram-Bot-Api-Secret-Token' => 'wrong-secret'
    ])->postJson('/api/telegram/webhook/test-secret-key', []);
    
    $response->assertStatus(403)
             ->assertJson(['status' => 'unauthorized']);
});

it('processes a valid incoming message and creates a log and user', function () {
    $mockApi = Mockery::mock(\App\Services\Telegram\TelegramApi::class);
    $mockApi->shouldReceive('sendMessage')->andReturn(true);
    $this->app->instance(\App\Services\Telegram\TelegramApi::class, $mockApi);
    
    $payload = [
        'update_id' => 123456789,
        'message' => [
            'message_id' => 1,
            'from' => [
                'id' => 987654321,
                'is_bot' => false,
                'first_name' => 'John',
                'last_name' => 'Doe',
                'username' => 'johndoe',
                'language_code' => 'en'
            ],
            'chat' => [
                'id' => 987654321,
                'type' => 'private'
            ],
            'date' => 1620000000,
            'text' => '/start'
        ]
    ];
    
    $response = $this->withHeaders([
        'X-Telegram-Bot-Api-Secret-Token' => 'test-secret-key'
    ])->postJson('/api/telegram/webhook/test-secret-key', $payload);
    
    $response->assertStatus(200)
             ->assertJson(['status' => 'ok']);
             
    // Verify user was created/updated
    $this->assertDatabaseHas('telegram_users', [
        'telegram_user_id' => 987654321,
        'first_name' => 'John',
        'username' => 'johndoe',
    ]);
    
    // Verify log was created
    $this->assertDatabaseHas('telegram_logs', [
        'update_id' => 123456789,
        'telegram_user_id' => 987654321,
        'event_type' => 'command',
    ]);
});
