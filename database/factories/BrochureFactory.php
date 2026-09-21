<?php

namespace Database\Factories;

use App\Models\Brochure;
use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;

/**
 * @extends Factory<Brochure>
 */
class BrochureFactory extends Factory
{
    protected $model = Brochure::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        return [
            'service_id' => Service::factory(),
            'title' => fake()->sentence(3),
            'file_path' => 'brochures/' . fake()->slug() . '.pdf',
            'telegram_file_id' => null,
            'version' => '1.0',
            'is_active' => true,
        ];
    }
}
