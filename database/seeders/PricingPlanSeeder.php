<?php

namespace Database\Seeders;

use App\Models\PricingPlan;
use Illuminate\Database\Seeder;

class PricingPlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Basic',
                'slug' => 'basic',
                'emoji' => '🟢',
                'price' => 4999,
                'billing_period' => 'month',
                'features' => [
                    '1 Web Application',
                    'Up to 5 Pages',
                    'Basic SEO',
                    'Mobile Responsive',
                    'SSL',
                    '1 Month Support',
                ],
                'is_popular' => false,
                'sort_order' => 1,
            ],
            [
                'name' => 'Standard',
                'slug' => 'standard',
                'emoji' => '🔵',
                'price' => 9999,
                'billing_period' => 'month',
                'features' => [
                    '1 Web Application',
                    'Up to 15 Pages',
                    'Advanced SEO',
                    'Mobile Responsive',
                    'SSL',
                    'Admin Panel',
                    'Database Integration',
                    '3 Months Support',
                ],
                'is_popular' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Pro',
                'slug' => 'pro',
                'emoji' => '🟣',
                'price' => 24999,
                'billing_period' => 'month',
                'features' => [
                    'Unlimited Pages',
                    'Custom Design',
                    'Full SEO Suite',
                    'Mobile Responsive',
                    'SSL',
                    'Admin Panel',
                    'API Integration',
                    'Database & CMS',
                    'Payment Gateway',
                    'Priority Support',
                    '6 Months Support',
                ],
                'is_popular' => false,
                'sort_order' => 3,
            ],
        ];

        foreach ($plans as $plan) {
            PricingPlan::updateOrCreate(
                ['slug' => $plan['slug']],
                $plan
            );
        }
    }
}
