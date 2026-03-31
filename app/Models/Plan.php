<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Plan extends Model
{
    use HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'price',
        'interval',
        'description',
        'features',
        'product_limit',
        'staff_limit',
        'store_limit',
        'has_analytics',
        'has_multi_payment',
        'is_active',
        'sort_order',
    ];

    protected $casts = [
        'price' => 'decimal:2',
        'features' => 'array',
        'product_limit' => 'integer',
        'staff_limit' => 'integer',
        'store_limit' => 'integer',
        'has_analytics' => 'boolean',
        'has_multi_payment' => 'boolean',
        'is_active' => 'boolean',
        'sort_order' => 'integer',
    ];

    public function subscriptions(): HasMany
    {
        return $this->hasMany(Subscription::class);
    }

    public function isMonthly(): bool
    {
        return $this->interval === 'monthly';
    }

    public function isYearly(): bool
    {
        return $this->interval === 'yearly';
    }

    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    public function scopeOrdered($query)
    {
        return $query->orderBy('sort_order');
    }

    public static function getBySlug(string $slug): ?self
    {
        return static::where('slug', $slug)->first();
    }
}
