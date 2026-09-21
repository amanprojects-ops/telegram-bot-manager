<?php

namespace Database\Seeders;

use App\Models\Service;
use Illuminate\Database\Seeder;

class ServiceSeeder extends Seeder
{
    public function run(): void
    {
        $services = [
            [
                'name' => 'Web Development',
                'slug' => 'web-development',
                'emoji' => '🌐',
                'short_description' => 'We build modern, responsive and scalable web applications for businesses.',
                'description' => 'We build modern, responsive and scalable web applications for businesses. From simple landing pages to complex enterprise systems.',
                'technologies' => ['Laravel', 'PHP', 'React', 'Node.js', 'MySQL', 'Tailwind CSS'],
                'suitable_for' => ['Business Websites', 'Web Applications', 'Admin Panels', 'Customer Portals', 'Enterprise Systems'],
                'website_url' => 'https://amanprojects.com',
                'sort_order' => 1,
            ],
            [
                'name' => 'SaaS Development',
                'slug' => 'saas-development',
                'emoji' => '☁️',
                'short_description' => 'Build scalable SaaS products with multi-tenancy, billing & analytics.',
                'description' => 'End-to-end SaaS product development with multi-tenant architecture, subscription billing, analytics dashboards and API integrations.',
                'technologies' => ['Laravel', 'React', 'Stripe', 'AWS', 'Docker'],
                'suitable_for' => ['Startups', 'SaaS Products', 'B2B Platforms', 'Subscription Services'],
                'website_url' => 'https://amanprojects.com',
                'sort_order' => 2,
            ],
            [
                'name' => 'Ethical Hacking & Security',
                'slug' => 'ethical-hacking-security',
                'emoji' => '🛡',
                'short_description' => 'Protect your digital assets with professional security audits.',
                'description' => 'Comprehensive security assessments including penetration testing, vulnerability scanning, code review and security hardening.',
                'technologies' => ['Kali Linux', 'Burp Suite', 'OWASP', 'Nmap', 'Metasploit'],
                'suitable_for' => ['Websites', 'APIs', 'Mobile Apps', 'Servers', 'Networks'],
                'website_url' => 'https://amanprojects.com',
                'sort_order' => 3,
            ],
            [
                'name' => 'API Development',
                'slug' => 'api-development',
                'emoji' => '🔌',
                'short_description' => 'Design and build robust REST & GraphQL APIs.',
                'description' => 'Scalable API development with authentication, rate limiting, documentation and versioning. REST and GraphQL supported.',
                'technologies' => ['Laravel', 'Node.js', 'GraphQL', 'REST', 'OAuth2'],
                'suitable_for' => ['Mobile Backends', 'Third-party Integrations', 'Microservices', 'Data APIs'],
                'website_url' => 'https://amanprojects.com',
                'sort_order' => 4,
            ],
            [
                'name' => 'Custom Software',
                'slug' => 'custom-software',
                'emoji' => '⚙️',
                'short_description' => 'Tailored software solutions for unique business needs.',
                'description' => 'Custom ERP, CRM, inventory management, billing systems and automation tools built specifically for your business workflows.',
                'technologies' => ['Laravel', 'PHP', 'MySQL', 'JavaScript', 'Python'],
                'suitable_for' => ['ERP Systems', 'CRM', 'Inventory Management', 'Billing Systems', 'Automation Tools'],
                'website_url' => 'https://amanprojects.com',
                'sort_order' => 5,
            ],
            [
                'name' => 'UI/UX Design',
                'slug' => 'ui-ux-design',
                'emoji' => '🎨',
                'short_description' => 'Beautiful, intuitive designs that users love.',
                'description' => 'User-centered design with wireframing, prototyping, UI design and usability testing. Figma, Adobe XD and modern design tools.',
                'technologies' => ['Figma', 'Adobe XD', 'Tailwind CSS', 'Bootstrap'],
                'suitable_for' => ['Web Apps', 'Mobile Apps', 'Landing Pages', 'Dashboards', 'Branding'],
                'website_url' => 'https://amanprojects.com',
                'sort_order' => 6,
            ],
            [
                'name' => 'Mobile App Development',
                'slug' => 'mobile-app-development',
                'emoji' => '📱',
                'short_description' => 'Cross-platform mobile apps for iOS & Android.',
                'description' => 'Native and cross-platform mobile application development with Flutter, React Native or native technologies.',
                'technologies' => ['Flutter', 'React Native', 'Firebase', 'REST APIs'],
                'suitable_for' => ['iOS Apps', 'Android Apps', 'Cross-platform Apps', 'MVP Development'],
                'website_url' => 'https://amanprojects.com',
                'sort_order' => 7,
            ],
            [
                'name' => 'Laravel & PHP Solutions',
                'slug' => 'laravel-php-solutions',
                'emoji' => '🔧',
                'short_description' => 'Expert Laravel development and PHP consulting.',
                'description' => 'Specialized Laravel development including package creation, performance optimization, legacy system modernization and consulting.',
                'technologies' => ['Laravel', 'PHP 8.x', 'Livewire', 'Filament', 'Inertia.js'],
                'suitable_for' => ['Laravel Projects', 'PHP Modernization', 'Package Development', 'Performance Optimization'],
                'website_url' => 'https://amanprojects.com',
                'sort_order' => 8,
            ],
        ];

        foreach ($services as $service) {
            Service::updateOrCreate(
                ['slug' => $service['slug']],
                $service
            );
        }
    }
}
