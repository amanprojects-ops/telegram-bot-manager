<?php

namespace Database\Factories;

use App\Models\TelegramSession;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TelegramSession>
 */
class TelegramSessionFactory extends Factory
{
    protected $model = TelegramSession::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'telegram_user_id' => fake()->numberBetween(100000, 999999999),
            'chat_id' => fake()->numberBetween(100000, 999999999),
            'state' => TelegramSession::STATE_IDLE,
            'payload' => null,
            'expires_at' => now()->addMinutes(30),
        ];
    }

    public function quoting(): static
    {
        return $this->state(fn (array $attributes) => [
            'state' => TelegramSession::STATE_QUOTE_SERVICE,
            'payload' => [],
        ]);
    }
}
