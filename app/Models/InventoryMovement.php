<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

class InventoryMovement extends Model
{
    use HasFactory;

    protected $fillable = [
        'product_id',
        'tenant_id',
        'store_id',
        'type',
        'quantity_change',
        'quantity_before',
        'quantity_after',
        'reference_type',
        'reference_id',
        'notes',
        'user_id',
    ];

    protected $casts = [
        'quantity_change' => 'integer',
        'quantity_before' => 'integer',
        'quantity_after' => 'integer',
    ];

    public function product(): BelongsTo
    {
        return $this->belongsTo(Product::class);
    }

    public function tenant(): BelongsTo
    {
        return $this->belongsTo(Tenant::class);
    }

    public function store(): BelongsTo
    {
        return $this->belongsTo(Store::class);
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function isSale(): bool
    {
        return $this->type === 'sale';
    }

    public function isPurchase(): bool
    {
        return $this->type === 'purchase';
    }

    public function isAdjustment(): bool
    {
        return $this->type === 'adjustment';
    }

    public function isReturn(): bool
    {
        return $this->type === 'return';
    }

    public function scopeByType($query, string $type)
    {
        return $query->where('type', $type);
    }

    public function scopeForProduct($query, string $productId)
    {
        return $query->where('product_id', $productId);
    }

    public function scopeForPeriod($query, $startDate, $endDate)
    {
        return $query->whereBetween('created_at', [$startDate, $endDate]);
    }

    public static function recordSale(Product $product, int $quantity, User $user, ?string $orderId = null): self
    {
        return static::create([
            'product_id' => $product->id,
            'tenant_id' => $product->tenant_id,
            'type' => 'sale',
            'quantity_change' => -$quantity,
            'quantity_before' => $product->stock_quantity,
            'quantity_after' => $product->stock_quantity - $quantity,
            'reference_type' => 'order',
            'reference_id' => $orderId,
            'user_id' => $user->id,
        ]);
    }

    public static function recordReturn(Product $product, int $quantity, User $user, ?string $orderId = null): self
    {
        return static::create([
            'product_id' => $product->id,
            'tenant_id' => $product->tenant_id,
            'type' => 'return',
            'quantity_change' => $quantity,
            'quantity_before' => $product->stock_quantity,
            'quantity_after' => $product->stock_quantity + $quantity,
            'reference_type' => 'order',
            'reference_id' => $orderId,
            'user_id' => $user->id,
        ]);
    }

    public static function recordAdjustment(Product $product, int $newQuantity, User $user, string $notes): self
    {
        $difference = $newQuantity - $product->stock_quantity;
        
        return static::create([
            'product_id' => $product->id,
            'tenant_id' => $product->tenant_id,
            'type' => 'adjustment',
            'quantity_change' => $difference,
            'quantity_before' => $product->stock_quantity,
            'quantity_after' => $newQuantity,
            'notes' => $notes,
            'user_id' => $user->id,
        ]);
    }
}
