<?php

namespace Database\Factories;

use App\Models\Brochure;
use App\Models\BrochureDownload;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<BrochureDownload>
 */
class BrochureDownloadFactory extends Factory
{
    protected $model = BrochureDownload::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'telegram_user_id' => fake()->numberBetween(100000, 999999999),
            'brochure_id' => Brochure::factory(),
            'telegram_file_id' => null,
            'downloaded_at' => fake()->dateTimeBetween('-3 months'),
        ];
    }
}
