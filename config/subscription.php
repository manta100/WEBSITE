<?php

return [
    'trial_days' => 3,
    
    'default_plan' => 'trial',
    
    'plans' => [
        'starter' => [
            'name' => 'Starter',
            'price' => 29.99,
            'interval' => 'monthly',
            'features' => [
                'products' => 500,
                'staff' => 3,
                'stores' => 1,
                'reports' => true,
            ],
        ],
        'professional' => [
            'name' => 'Professional',
            'price' => 79.99,
            'interval' => 'monthly',
            'features' => [
                'products' => 5000,
                'staff' => 10,
                'stores' => 3,
                'reports' => true,
            ],
        ],
        'enterprise' => [
            'name' => 'Enterprise',
            'price' => 199.99,
            'interval' => 'monthly',
            'features' => [
                'products' => -1,
                'staff' => -1,
                'stores' => -1,
                'reports' => true,
            ],
        ],
    ],
    
    'gateways' => [
        'stripe' => [
            'enabled' => env('STRIPE_ENABLED', false),
            'key' => env('STRIPE_KEY'),
            'secret' => env('STRIPE_SECRET'),
            'webhook_secret' => env('STRIPE_WEBHOOK_SECRET'),
        ],
        'paypal' => [
            'enabled' => env('PAYPAL_ENABLED', false),
            'client_id' => env('PAYPAL_CLIENT_ID'),
            'secret' => env('PAYPAL_SECRET'),
            'webhook_id' => env('PAYPAL_WEBHOOK_ID'),
        ],
        'paystack' => [
            'enabled' => env('PAYSTACK_ENABLED', false),
            'public_key' => env('PAYSTACK_PUBLIC_KEY'),
            'secret_key' => env('PAYSTACK_SECRET_KEY'),
        ],
    ],
    
    'currency' => 'USD',
    'currency_symbol' => '$',
    
    'tax_rate' => env('DEFAULT_TAX_RATE', 10),
];
