<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Stancl\Tenancy\DatabaseConcerns\HasDomains;
use Stancl\Tenancy\DatabaseConcerns\HasDatabase;
use Stancl\Tenancy\Contracts\Tenant as TenantContract;

class Tenant extends Model implements TenantContract
{
    use HasFactory, HasUuids, SoftDeletes, HasDomains, HasDatabase;

    protected $fillable = [
        'name',
        'slug',
        'domain',
        'database',
        'settings',
        'is_active',
        'trial_ends_at',
    ];

    protected $casts = [
        'settings' => 'array',
        'is_active' => 'boolean',
        'trial_ends_at' => 'datetime',
    ];

    public function users()
    {
        return $this->hasMany(User::class);
    }

    public function subscription()
    {
        return $this->hasOne(Subscription::class)->latest();
    }

    public function categories()
    {
        return $this->hasMany(Category::class);
    }

    public function products()
    {
        return $this->hasMany(Product::class);
    }

    public function orders()
    {
        return $this->hasMany(Order::class);
    }

    public function stores()
    {
        return $this->hasMany(Store::class);
    }

    public function discounts()
    {
        return $this->hasMany(Discount::class);
    }

    public function owner()
    {
        return $this->users()->where('role', 'owner')->first();
    }

    public function isOnTrial(): bool
    {
        return $this->trial_ends_at && $this->trial_ends_at->isFuture();
    }

    public function hasActiveSubscription(): bool
    {
        return $this->subscription && 
               in_array($this->subscription->status, ['active', 'trialing']) &&
               ($this->subscription->ends_at === null || $this->subscription->ends_at->isFuture());
    }

    public function canUseFeature(string $feature): bool
    {
        if (!$this->subscription?->plan) {
            return false;
        }

        $plan = $this->subscription->plan;
        
        return match($feature) {
            'products' => $plan->product_limit === -1 || $this->products()->count() < $plan->product_limit,
            'staff' => $plan->staff_limit === -1 || $this->users()->count() < $plan->staff_limit,
            'stores' => $plan->store_limit === -1 || $this->stores()->count() < $plan->store_limit,
            'analytics' => $plan->has_analytics,
            'multi_payment' => $plan->has_multi_payment,
            default => false,
        };
    }

    public static function findByDomain(string $domain): ?self
    {
        return static::where('domain', $domain)->first();
    }
}
