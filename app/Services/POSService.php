<?php

namespace App\Services;

use App\Models\Order;
use App\Models\OrderItem;
use App\Models\Product;
use App\Models\Payment;
use App\Models\InventoryMovement;
use App\Models\Discount;
use App\Models\User;
use Illuminate\Support\Collection;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class POSService
{
    protected float $taxRate;

    public function __construct()
    {
        $this->taxRate = (float) config('subscription.tax_rate', 10);
    }

    public function setTaxRate(float $rate): void
    {
        $this->taxRate = $rate;
    }

    public function getTaxRate(): float
    {
        return $this->taxRate;
    }

    public function calculateItemTotals(OrderItem $item): OrderItem
    {
        $subtotal = $item->quantity * $item->unit_price;
        $item->tax_amount = $subtotal * ($this->taxRate / 100);
        $item->total = $subtotal + $item->tax_amount - $item->discount_amount;
        
        return $item;
    }

    public function calculateOrderTotals(Order $order): Order
    {
        $items = $order->items;
        
        $order->subtotal = $items->sum(fn ($item) => $item->quantity * $item->unit_price);
        $order->tax_amount = $items->sum('tax_amount');
        $order->discount_amount = $items->sum('discount_amount');
        $order->total = $order->subtotal + $order->tax_amount - $order->discount_amount;
        
        return $order;
    }

    public function processPayment(Order $order, string $method, float $amountPaid): Payment
    {
        return DB::transaction(function () use ($order, $method, $amountPaid) {
            $changeAmount = $amountPaid - $order->total;
            
            $payment = Payment::create([
                'order_id' => $order->id,
                'tenant_id' => $order->tenant_id,
                'gateway' => $method,
                'amount' => $amountPaid,
                'status' => 'completed',
                'processed_at' => now(),
            ]);

            $order->update([
                'payment_status' => 'paid',
                'payment_method' => $method,
                'amount_paid' => $amountPaid,
                'change_amount' => max(0, $changeAmount),
                'status' => 'completed',
                'completed_at' => now(),
            ]);

            $this->updateInventory($order);
            
            return $payment;
        });
    }

    protected function updateInventory(Order $order): void
    {
        foreach ($order->items as $item) {
            $product = $item->product;
            
            if ($product && $product->track_inventory) {
                $product->decreaseStock($item->quantity);
                
                InventoryMovement::recordSale(
                    $product,
                    $item->quantity,
                    $order->user,
                    $order->id
                );
            }
        }
    }

    public function applyDiscount(Order $order, ?string $discountCode): ?Discount
    {
        if (!$discountCode) {
            return null;
        }

        $discount = Discount::byCode($discountCode)
            ->valid()
            ->where('tenant_id', $order->tenant_id)
            ->first();

        if (!$discount) {
            return null;
        }

        $discountAmount = $discount->calculateDiscount($order->subtotal);
        
        $order->update([
            'discount_amount' => $discountAmount,
        ]);

        $discount->incrementUsage();

        return $discount;
    }

    public function createOrder(User $user, array $items, array $options = []): Order
    {
        return DB::transaction(function () use ($user, $items, $options) {
            $order = Order::create([
                'tenant_id' => $user->tenant_id,
                'user_id' => $user->id,
                'status' => 'pending',
                'payment_status' => 'pending',
                'payment_method' => null,
                'currency' => config('subscription.currency', 'USD'),
                'notes' => $options['notes'] ?? null,
            ]);

            foreach ($items as $item) {
                $product = Product::find($item['product_id']);
                
                if (!$product) {
                    continue;
                }

                $orderItem = OrderItem::create([
                    'order_id' => $order->id,
                    'product_id' => $product->id,
                    'product_name' => $product->name,
                    'sku' => $product->sku,
                    'quantity' => $item['quantity'],
                    'unit_price' => $product->price,
                    'tax_rate' => $this->taxRate,
                    'discount_amount' => $item['discount_amount'] ?? 0,
                ]);

                $this->calculateItemTotals($orderItem);
                $orderItem->save();
            }

            $this->calculateOrderTotals($order);
            $order->save();

            if (!empty($options['discount_code'])) {
                $this->applyDiscount($order, $options['discount_code']);
            }

            return $order->fresh(['items']);
        });
    }

    public function voidOrder(Order $order): bool
    {
        if (!$order->isPending()) {
            return false;
        }

        return DB::transaction(function () use ($order) {
            $order->items()->delete();
            $order->update(['status' => 'cancelled']);
            
            return true;
        });
    }

    public function refundOrder(Order $order, float $amount = null): bool
    {
        if (!$order->isCompleted()) {
            return false;
        }

        $refundAmount = $amount ?? $order->total;

        return DB::transaction(function () use ($order, $refundAmount) {
            foreach ($order->items as $item) {
                if ($item->product && $item->product->track_inventory) {
                    $item->product->increaseStock($item->quantity);
                    
                    InventoryMovement::recordReturn(
                        $item->product,
                        $item->quantity,
                        $order->user,
                        $order->id
                    );
                }
            }

            $order->update([
                'status' => 'refunded',
                'payment_status' => 'refunded',
            ]);

            Payment::create([
                'order_id' => $order->id,
                'tenant_id' => $order->tenant_id,
                'gateway' => 'refund',
                'amount' => -$refundAmount,
                'status' => 'completed',
                'processed_at' => now(),
            ]);

            return true;
        });
    }
}
