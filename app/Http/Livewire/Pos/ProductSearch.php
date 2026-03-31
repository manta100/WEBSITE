<?php

namespace App\Http\Livewire\Pos;

use Livewire\Component;
use App\Models\Product;
use App\Models\Category;
use Illuminate\Support\Collection;

class ProductSearch extends Component
{
    public string $search = '';
    public ?string $selectedCategory = null;
    public Collection $products;
    public Collection $categories;
    public Collection $filteredProducts;
    public array $cart = [];
    public float $subtotal = 0;
    public float $tax = 0;
    public float $total = 0;
    public ?string $discountCode = null;
    public float $discountAmount = 0;

    protected $listeners = [
        'cartUpdated' => 'updateCartTotals',
    ];

    public function mount(): void
    {
        $this->loadCategories();
        $this->loadProducts();
    }

    public function loadCategories(): void
    {
        $this->categories = Category::active()
            ->root()
            ->with('children')
            ->orderBy('sort_order')
            ->get();
    }

    public function loadProducts(): void
    {
        $this->products = Product::active()
            ->inStock()
            ->with('category')
            ->orderBy('name')
            ->get();

        $this->filteredProducts = $this->products;
    }

    public function updatedSearch(): void
    {
        $this->filterProducts();
    }

    public function updatedSelectedCategory(): void
    {
        $this->filterProducts();
    }

    public function filterProducts(): void
    {
        $query = $this->products->query();

        if ($this->search) {
            $query = $query->filter(function ($product) {
                return stripos($product->name, $this->search) !== false ||
                       stripos($product->sku, $this->search) !== false ||
                       stripos($product->barcode, $this->search) !== false;
            });
        }

        if ($this->selectedCategory) {
            $categoryIds = $this->getCategoryAndChildrenIds($this->selectedCategory);
            $query = $query->filter(function ($product) use ($categoryIds) {
                return in_array($product->category_id, $categoryIds);
            });
        }

        $this->filteredProducts = $query->values();
    }

    protected function getCategoryAndChildrenIds(string $categoryId): array
    {
        $ids = [$categoryId];
        
        $category = $this->categories->find($categoryId);
        if ($category && $category->children) {
            foreach ($category->children as $child) {
                $ids[] = $child->id;
            }
        }

        return $ids;
    }

    public function addToCart(Product $product): void
    {
        if (!isset($this->cart[$product->id])) {
            $this->cart[$product->id] = [
                'id' => $product->id,
                'name' => $product->name,
                'price' => (float) $product->price,
                'quantity' => 0,
                'max_quantity' => $product->track_inventory ? $product->stock_quantity : 999,
            ];
        }

        if ($this->cart[$product->id]['quantity'] < $this->cart[$product->id]['max_quantity']) {
            $this->cart[$product->id]['quantity']++;
        }

        $this->updateCartTotals();
        $this->emit('cartUpdated', $this->cart);
    }

    public function incrementQuantity(string $productId): void
    {
        if (isset($this->cart[$productId])) {
            if ($this->cart[$productId]['quantity'] < $this->cart[$productId]['max_quantity']) {
                $this->cart[$productId]['quantity']++;
                $this->updateCartTotals();
                $this->emit('cartUpdated', $this->cart);
            }
        }
    }

    public function decrementQuantity(string $productId): void
    {
        if (isset($this->cart[$productId])) {
            $this->cart[$productId]['quantity']--;
            
            if ($this->cart[$productId]['quantity'] <= 0) {
                unset($this->cart[$productId]);
            }

            $this->updateCartTotals();
            $this->emit('cartUpdated', $this->cart);
        }
    }

    public function removeFromCart(string $productId): void
    {
        unset($this->cart[$productId]);
        $this->updateCartTotals();
        $this->emit('cartUpdated', $this->cart);
    }

    public function clearCart(): void
    {
        $this->cart = [];
        $this->updateCartTotals();
        $this->emit('cartUpdated', $this->cart);
    }

    public function updateCartTotals(): void
    {
        $this->subtotal = collect($this->cart)->sum(function ($item) {
            return $item['price'] * $item['quantity'];
        });

        $this->tax = $this->subtotal * (config('subscription.tax_rate', 10) / 100);
        $this->total = $this->subtotal + $this->tax - $this->discountAmount;
    }

    public function applyDiscount(): void
    {
        if (!$this->discountCode) {
            return;
        }

        $discount = \App\Models\Discount::byCode($this->discountCode)
            ->valid()
            ->where('tenant_id', tenant()->id)
            ->first();

        if ($discount) {
            $this->discountAmount = $discount->calculateDiscount($this->subtotal);
            $this->updateCartTotals();
        }
    }

    public function getCartCountAttribute(): int
    {
        return collect($this->cart)->sum('quantity');
    }

    public function render()
    {
        return view('livewire.pos.product-search');
    }
}
