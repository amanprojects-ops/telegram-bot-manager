<?php

namespace Database\Factories;

use App\Models\Service;
use App\Models\TelegramLead;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<TelegramLead>
 */
class TelegramLeadFactory extends Factory
{
    protected $model = TelegramLead::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'lead_id' => TelegramLead::generateLeadId(),
            'telegram_user_id' => fake()->numberBetween(100000, 999999999),
            'service_id' => Service::factory(),
            'name' => fake()->name(),
            'phone' => fake()->phoneNumber(),
            'email' => fake()->optional()->safeEmail(),
            'budget' => fake()->randomElement(['< ₹10K', '₹10K–₹25K', '₹25K–₹50K', '₹50K–₹1L', '₹1L+', 'Not Sure']),
            'requirement' => fake()->paragraph(),
            'source' => 'telegram',
            'status' => TelegramLead::STATUS_NEW,
            'is_priority' => false,
            'is_contacted' => false,
            'score' => 0,
        ];
    }

    public function fromWebsite(): static
    {
        return $this->state(fn (array $attributes) => [
            'source' => 'website',
            'telegram_user_id' => null,
        ]);
    }

    public function priority(): static
    {
        return $this->state(fn (array $attributes) => [
            'is_priority' => true,
        ]);
    }
}
