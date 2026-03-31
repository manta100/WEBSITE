<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Services\SubscriptionService;
use App\Services\POSService;
use App\Services\AnalyticsService;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        $this->app->singleton(SubscriptionService::class, function ($app) {
            return new SubscriptionService();
        });

        $this->app->singleton(POSService::class, function ($app) {
            return new POSService();
        });

        $this->app->singleton(AnalyticsService::class, function ($app) {
            return new AnalyticsService();
        });
    }

    public function boot(): void
    {
        //
    }
}
