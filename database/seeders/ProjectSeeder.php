<?php

namespace Database\Seeders;

use App\Models\Project;
use Illuminate\Database\Seeder;

class ProjectSeeder extends Seeder
{
    public function run(): void
    {
        $projects = [
            [
                'name' => 'Quick File Transfer',
                'slug' => 'quick-file-transfer',
                'emoji' => '📁',
                'short_description' => 'Fast and secure file sharing platform.',
                'technologies' => ['Laravel', 'PHP', 'JavaScript'],
                'website_url' => 'https://amanprojects.com/project/quick-file-transfer',
                'sort_order' => 1,
            ],
            [
                'name' => 'OmniKiosk',
                'slug' => 'omnikiosk',
                'emoji' => '🖥',
                'short_description' => 'Self-service kiosk management system.',
                'technologies' => ['Laravel', 'PHP', 'MySQL'],
                'website_url' => 'https://amanprojects.com/project/omnikiosk',
                'sort_order' => 2,
            ],
            [
                'name' => 'CreditIndia',
                'slug' => 'creditindia',
                'emoji' => '💳',
                'short_description' => 'Credit and financial services platform.',
                'technologies' => ['Laravel', 'PHP', 'MySQL'],
                'website_url' => 'https://amanprojects.com/project/creditindia',
                'sort_order' => 3,
            ],
            [
                'name' => 'Printing Press Website',
                'slug' => 'printing-press-website',
                'emoji' => '🖨',
                'short_description' => 'Complete printing press management website.',
                'technologies' => ['Laravel', 'PHP', 'Tailwind CSS'],
                'website_url' => 'https://amanprojects.com/project/printing-press-website',
                'sort_order' => 4,
            ],
            [
                'name' => 'AP PayOrbit',
                'slug' => 'ap-payorbit',
                'emoji' => '💰',
                'short_description' => 'Payment routing & gateway aggregation platform.',
                'technologies' => ['Laravel', 'MySQL', 'Axios', 'JavaScript', 'Tailwind CSS'],
                'website_url' => 'https://amanprojects.com/project/ap-payorbit',
                'sort_order' => 5,
            ],
            [
                'name' => 'Laravel Modern News',
                'slug' => 'laravel-modern-news',
                'emoji' => '📰',
                'short_description' => 'Modern news CMS built with Laravel.',
                'technologies' => ['Laravel', 'PHP', 'MySQL', 'Tailwind CSS'],
                'website_url' => 'https://amanprojects.com/project/laravel-modern-news',
                'sort_order' => 6,
            ],
            [
                'name' => 'PHPStart',
                'slug' => 'phpstart',
                'emoji' => '🚀',
                'short_description' => 'PHP project starter and scaffolding tool.',
                'technologies' => ['PHP', 'Composer'],
                'website_url' => 'https://amanprojects.com/project/phpstart',
                'sort_order' => 7,
            ],
            [
                'name' => 'PHP Installer',
                'slug' => 'php-installer',
                'emoji' => '⚙️',
                'short_description' => 'PHP project installer wizard.',
                'technologies' => ['PHP'],
                'website_url' => 'https://amanprojects.com/project/php-installer',
                'sort_order' => 8,
            ],
        ];

        foreach ($projects as $project) {
            Project::updateOrCreate(
                ['slug' => $project['slug']],
                $project
            );
        }
    }
}
