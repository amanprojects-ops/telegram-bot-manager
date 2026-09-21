<?php

namespace Database\Factories;

use App\Models\Service;
use Illuminate\Database\Eloquent\Factories\Factory;
use Illuminate\Support\Str;

/**
 * @extends Factory<Service>
 */
class ServiceFactory extends Factory
{
    protected $model = Service::class;

    /**
     * @return array<string, mixed>
     */
    public function definition(): array
    {
        $name = fake()->unique()->randomElement([
            'Web Development',
            'SaaS Development',
            'Ethical Hacking & Security',
            'API Development',
            'Custom Software',
            'UI/UX Design',
            'Mobile App Development',
            'Laravel & PHP Solutions',
        ]);

        return [
            'name' => $name,
            'slug' => Str::slug($name),
            'emoji' => '🛠',
            'short_description' => fake()->sentence(),
            'description' => fake()->paragraph(),
            'technologies' => ['Laravel', 'PHP', 'MySQL'],
            'suitable_for' => ['Business Websites', 'Web Applications'],
            'website_url' => 'https://amanprojects.com',
            'is_active' => true,
            'sort_order' => 0,
        ];
    }
}
