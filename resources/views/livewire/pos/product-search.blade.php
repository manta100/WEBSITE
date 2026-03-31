<div>
    <div class="mb-6">
        <div class="relative">
            <input type="text" wire:model.live.debounce.300ms="search" placeholder="Search products by name, SKU, or barcode..."
                class="w-full px-4 py-3 pl-10 border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500 focus:border-blue-500">
            <svg class="absolute left-3 top-3.5 w-5 h-5 text-gray-400" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M21 21l-6-6m2-5a7 7 0 11-14 0 7 7 0 0114 0z"/>
            </svg>
        </div>
    </div>

    <div class="mb-6">
        <div class="flex flex-wrap gap-2">
            <button wire:click="$set('selectedCategory', null)"
                class="px-4 py-2 rounded-full text-sm font-medium {{ !$selectedCategory ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                All
            </button>
            @foreach($categories as $category)
                <button wire:click="$set('selectedCategory', '{{ $category->id }}')"
                    class="px-4 py-2 rounded-full text-sm font-medium {{ $selectedCategory === $category->id ? 'bg-blue-600 text-white' : 'bg-gray-100 text-gray-700 hover:bg-gray-200' }}">
                    {{ $category->name }}
                </button>
            @endforeach
        </div>
    </div>

    <div class="grid grid-cols-2 md:grid-cols-3 lg:grid-cols-4 gap-4">
        @forelse($filteredProducts as $product)
            <button wire:click="addToCart({{ $product->id }})"
                class="bg-white rounded-lg shadow-sm border border-gray-200 p-4 hover:shadow-md hover:border-blue-300 transition text-left">
                <div class="aspect-square bg-gray-100 rounded-lg mb-3 flex items-center justify-center">
                    @if($product->images && count($product->images) > 0)
                        <img src="{{ Storage::url($product->images[0]) }}" alt="{{ $product->name }}" class="w-full h-full object-cover rounded-lg">
                    @else
                        <svg class="w-12 h-12 text-gray-300" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                            <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 7l-8-4-8 4m16 0l-8 4m8-4v10l-8 4m0-10L4 7m8 4v10M4 7v10l8 4"/>
                        </svg>
                    @endif
                </div>
                <h3 class="font-medium text-gray-900 truncate">{{ $product->name }}</h3>
                @if($product->sku)
                    <p class="text-xs text-gray-500">SKU: {{ $product->sku }}</p>
                @endif
                <p class="text-lg font-bold text-blue-600 mt-1">${{ number_format($product->price, 2) }}</p>
                @if($product->track_inventory)
                    <p class="text-xs text-gray-500 mt-1">{{ $product->stock_quantity }} in stock</p>
                @endif
            </button>
        @empty
            <div class="col-span-full text-center py-12 text-gray-500">
                <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 13V6a2 2 0 00-2-2H6a2 2 0 00-2 2v7m16 0v5a2 2 0 01-2 2H6a2 2 0 01-2-2v-5m16 0h-2.586a1 1 0 00-.707.293l-2.414 2.414a1 1 0 01-.707.293h-3.172a1 1 0 01-.707-.293l-2.414-2.414A1 1 0 006.586 13H4"/>
                </svg>
                <p>No products found</p>
            </div>
        @endforelse
    </div>
</div>
