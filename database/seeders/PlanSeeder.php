<?php

namespace Database\Seeders;

use App\Models\Plan;
use Illuminate\Database\Seeder;

class PlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            [
                'name' => 'Starter',
                'slug' => 'starter',
                'price' => 29.99,
                'interval' => 'monthly',
                'description' => 'Perfect for small businesses just getting started',
                'features' => ['products' => 500, 'staff' => 3, 'stores' => 1],
                'product_limit' => 500,
                'staff_limit' => 3,
                'store_limit' => 1,
                'has_analytics' => true,
                'has_multi_payment' => false,
                'is_active' => true,
                'sort_order' => 1,
            ],
            [
                'name' => 'Professional',
                'slug' => 'professional',
                'price' => 79.99,
                'interval' => 'monthly',
                'description' => 'Ideal for growing businesses with more needs',
                'features' => ['products' => 5000, 'staff' => 10, 'stores' => 3],
                'product_limit' => 5000,
                'staff_limit' => 10,
                'store_limit' => 3,
                'has_analytics' => true,
                'has_multi_payment' => true,
                'is_active' => true,
                'sort_order' => 2,
            ],
            [
                'name' => 'Enterprise',
                'slug' => 'enterprise',
                'price' => 199.99,
                'interval' => 'monthly',
                'description' => 'For large businesses requiring unlimited access',
                'features' => ['products' => -1, 'staff' => -1, 'stores' => -1],
                'product_limit' => -1,
                'staff_limit' => -1,
                'store_limit' => -1,
                'has_analytics' => true,
                'has_multi_payment' => true,
                'is_active' => true,
                'sort_order' => 3,
            ],
        ];

        foreach ($plans as $plan) {
            Plan::updateOrCreate(['slug' => $plan['slug']], $plan);
        }
    }
}
