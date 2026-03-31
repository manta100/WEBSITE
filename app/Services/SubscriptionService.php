<?php

namespace App\Services;

use App\Models\Tenant;
use App\Models\Subscription;
use App\Models\Plan;
use Illuminate\Support\Facades\DB;

class SubscriptionService
{
    public function createTrialSubscription(Tenant $tenant): Subscription
    {
        $trialDays = config('subscription.trial_days', 3);
        
        return Subscription::create([
            'tenant_id' => $tenant->id,
            'plan_id' => null,
            'gateway' => 'trial',
            'status' => 'trialing',
            'trial_ends_at' => now()->addDays($trialDays),
            'starts_at' => now(),
        ]);
    }

    public function subscribe(Tenant $tenant, Plan $plan, string $gateway = 'stripe'): Subscription
    {
        return DB::transaction(function () use ($tenant, $plan, $gateway) {
            $subscription = Subscription::create([
                'tenant_id' => $tenant->id,
                'plan_id' => $plan->id,
                'gateway' => $gateway,
                'status' => 'active',
                'amount' => $plan->price,
                'starts_at' => now(),
                'ends_at' => $plan->isMonthly() 
                    ? now()->addMonth() 
                    : now()->addYear(),
            ]);

            $tenant->update([
                'trial_ends_at' => null,
            ]);

            return $subscription;
        });
    }

    public function cancelSubscription(Tenant $tenant): bool
    {
        $subscription = $tenant->subscription;
        
        if (!$subscription) {
            return false;
        }

        $subscription->markAsCancelled();
        
        return true;
    }

    public function renewSubscription(Tenant $tenant): ?Subscription
    {
        $subscription = $tenant->subscription;
        $plan = $subscription->plan;
        
        if (!$subscription || !$plan) {
            return null;
        }

        $newEndDate = $plan->isMonthly()
            ? $subscription->ends_at->addMonth()
            : $subscription->ends_at->addYear();

        $subscription->update([
            'status' => 'active',
            'ends_at' => $newEndDate,
        ]);

        return $subscription;
    }

    public function checkAndExpireTrials(): int
    {
        $count = 0;
        
        Tenant::whereNotNull('trial_ends_at')
            ->where('trial_ends_at', '<=', now())
            ->where('is_active', true)
            ->chunkById(100, function ($tenants) use (&$count) {
                foreach ($tenants as $tenant) {
                    $tenant->update(['is_active' => false]);
                    $count++;
                }
            });

        return $count;
    }

    public function getTrialStatus(Tenant $tenant): array
    {
        $trialEndsAt = $tenant->trial_ends_at;
        
        if (!$trialEndsAt) {
            return [
                'is_on_trial' => false,
                'days_remaining' => 0,
                'has_expired' => true,
            ];
        }

        $daysRemaining = now()->diffInDays($trialEndsAt, false);
        
        return [
            'is_on_trial' => $trialEndsAt->isFuture(),
            'days_remaining' => max(0, $daysRemaining),
            'has_expired' => $trialEndsAt->isPast(),
            'trial_ends_at' => $trialEndsAt,
        ];
    }
}
