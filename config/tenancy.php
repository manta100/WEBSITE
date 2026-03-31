<?php

return [
    'central_domains' => [
        env('APP_URL'),
    ],
    
    'tenants_model' => App\Models\Tenant::class,
    
    'database' => [
        'managers' => [
            'sqlite' => Stancl\Tenancy\DatabaseManager\SQLiteDatabaseManager::class,
            'mysql' => Stancl\Tenancy\DatabaseManager\MySQLDatabaseManager::class,
            'pgsql' => Stancl\Tenancy\DatabaseManager\PostgreSQLDatabaseManager::class,
        ],
    ],
    
    'features' => [
        Stancl\Tenancy\Features\TenantConfig::class,
        Stancl\Tenancy\Features\TenantAssets::class,
        Stancl\Tenancy\Features\UniversalRoutes::class,
        Stancl\Tenancy\Features\DomainRedirect::class,
    ],
    
    'domain_model' => App\Models\Domain::class,
    
    'route' => [
        'middleware' => [
            'web',
            Stancl\Tenancy\Middleware\InitializeTenancyByDomain::class,
        ],
    ],
];
