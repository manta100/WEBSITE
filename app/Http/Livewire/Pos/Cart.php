<?php

namespace App\Http\Livewire\Pos;

use Livewire\Component;
use App\Models\Product;
use App\Models\Order;
use App\Services\POSService;
use Illuminate\Support\Facades\DB;

class Cart extends Component
{
    public array $items = [];
    public float $subtotal = 0;
    public float $taxRate = 10;
    public float $taxAmount = 0;
    public float $discountAmount = 0;
    public ?string $discountCode = null;
    public float $total = 0;
    public string $paymentMethod = 'cash';
    public float $amountPaid = 0;
    public float $changeAmount = 0;
    public bool $showPaymentModal = false;
    public ?Order $lastOrder = null;

    protected $listeners = [
        'cartUpdated' => 'updateFromCart',
        'refreshCart' => '$refresh',
    ];

    public function mount(): void
    {
        $this->taxRate = (float) config('subscription.tax_rate', 10);
    }

    public function updateFromCart(array $cart): void
    {
        $this->items = $cart;
        $this->calculateTotals();
    }

    public function calculateTotals(): void
    {
        $this->subtotal = collect($this->items)->sum(function ($item) {
            return ($item['price'] ?? 0) * ($item['quantity'] ?? 0);
        });

        $this->taxAmount = $this->subtotal * ($this->taxRate / 100);
        $this->total = $this->subtotal + $this->taxAmount - $this->discountAmount;
    }

    public function applyDiscount(): void
    {
        if (!$this->discountCode) {
            $this->discountAmount = 0;
            $this->calculateTotals();
            return;
        }

        $discount = \App\Models\Discount::byCode($this->discountCode)
            ->valid()
            ->where('tenant_id', tenant()->id)
            ->first();

        if ($discount) {
            $this->discountAmount = $discount->calculateDiscount($this->subtotal);
        } else {
            $this->discountAmount = 0;
        }

        $this->calculateTotals();
    }

    public function calculateChange(): void
    {
        $this->changeAmount = max(0, $this->amountPaid - $this->total);
    }

    public function openPaymentModal(): void
    {
        $this->showPaymentModal = true;
        $this->amountPaid = $this->total;
        $this->calculateChange();
    }

    public function closePaymentModal(): void
    {
        $this->showPaymentModal = false;
        $this->amountPaid = 0;
        $this->changeAmount = 0;
    }

    public function processPayment(): void
    {
        if ($this->amountPaid < $this->total) {
            $this->addError('amountPaid', 'Amount paid must be greater than or equal to total.');
            return;
        }

        try {
            $posService = app(POSService::class);
            
            $order = $posService->createOrder(
                auth()->user(),
                $this->items,
                ['discount_code' => $this->discountCode]
            );

            $posService->processPayment($order, $this->paymentMethod, $this->amountPaid);

            $this->lastOrder = $order->fresh(['items']);
            $this->emit('orderCompleted', $order->id);
            $this->closePaymentModal();
            $this->resetCart();
            
            session()->flash('success', 'Order completed successfully! Order #' . $order->order_number);

        } catch (\Exception $e) {
            $this->addError('payment', 'Payment processing failed: ' . $e->getMessage());
        }
    }

    public function resetCart(): void
    {
        $this->items = [];
        $this->subtotal = 0;
        $this->taxAmount = 0;
        $this->discountAmount = 0;
        $this->discountCode = null;
        $this->total = 0;
        $this->paymentMethod = 'cash';
        $this->amountPaid = 0;
        $this->changeAmount = 0;
        
        $this->emit('cartCleared');
    }

    public function getItemCountAttribute(): int
    {
        return collect($this->items)->sum('quantity');
    }

    public function getFormattedSubtotalAttribute(): string
    {
        return number_format($this->subtotal, 2);
    }

    public function getFormattedTaxAttribute(): string
    {
        return number_format($this->taxAmount, 2);
    }

    public function getFormattedTotalAttribute(): string
    {
        return number_format($this->total, 2);
    }

    public function getFormattedChangeAttribute(): string
    {
        return number_format($this->changeAmount, 2);
    }

    public function render()
    {
        return view('livewire.pos.cart');
    }
}
