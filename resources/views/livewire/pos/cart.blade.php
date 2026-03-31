<div class="flex flex-col h-full">
    <div class="p-4 border-b border-gray-200">
        <h2 class="text-lg font-semibold text-gray-900">Shopping Cart</h2>
        <p class="text-sm text-gray-500">{{ $this->itemCount }} items</p>
    </div>

    <div class="flex-1 overflow-auto p-4">
        @if(count($items) > 0)
            <div class="space-y-3">
                @foreach($items as $id => $item)
                    <div class="bg-gray-50 rounded-lg p-3">
                        <div class="flex justify-between items-start">
                            <div class="flex-1">
                                <h4 class="font-medium text-gray-900">{{ $item['name'] }}</h4>
                                <p class="text-sm text-gray-500">${{ number_format($item['price'], 2) }} each</p>
                            </div>
                            <button wire:click="removeFromCart('{{ $id }}')" class="text-gray-400 hover:text-red-500">
                                <svg class="w-5 h-5" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M6 18L18 6M6 6l12 12"/>
                                </svg>
                            </button>
                        </div>
                        <div class="flex items-center justify-between mt-2">
                            <div class="flex items-center space-x-2">
                                <button wire:click="decrementQuantity('{{ $id }}')" class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center hover:bg-gray-300">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M20 12H4"/>
                                    </svg>
                                </button>
                                <span class="font-medium w-8 text-center">{{ $item['quantity'] }}</span>
                                <button wire:click="incrementQuantity('{{ $id }}')" class="w-8 h-8 rounded-full bg-gray-200 flex items-center justify-center hover:bg-gray-300">
                                    <svg class="w-4 h-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4v16m8-8H4"/>
                                    </svg>
                                </button>
                            </div>
                            <span class="font-semibold text-gray-900">${{ number_format($item['price'] * $item['quantity'], 2) }}</span>
                        </div>
                    </div>
                @endforeach
            </div>
        @else
            <div class="text-center py-8 text-gray-500">
                <svg class="w-16 h-16 mx-auto text-gray-300 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 3h2l.4 2M7 13h10l4-8H5.4M7 13L5.4 5M7 13l-2.293 2.293c-.63.63-.184 1.707.707 1.707H17m0 0a2 2 0 100 4 2 2 0 000-4zm-8 2a2 2 0 11-4 0 2 2 0 014 0z"/>
                </svg>
                <p>Your cart is empty</p>
                <p class="text-sm">Add products to get started</p>
            </div>
        @endif
    </div>

    <div class="p-4 border-t border-gray-200 bg-gray-50">
        <div class="space-y-2 mb-4">
            <div class="flex justify-between text-sm">
                <span class="text-gray-600">Subtotal</span>
                <span class="font-medium">${{ $formattedSubtotal }}</span>
            </div>
            <div class="flex justify-between text-sm">
                <span class="text-gray-600">Tax ({{ $taxRate }}%)</span>
                <span class="font-medium">${{ $formattedTax }}</span>
            </div>
            @if($discountAmount > 0)
                <div class="flex justify-between text-sm text-green-600">
                    <span>Discount</span>
                    <span>-${{ number_format($discountAmount, 2) }}</span>
                </div>
            @endif
            <div class="flex justify-between text-lg font-bold pt-2 border-t">
                <span>Total</span>
                <span>${{ $formattedTotal }}</span>
            </div>
        </div>

        <div class="mb-4">
            <div class="flex gap-2">
                <input type="text" wire:model.live.debounce.300ms="discountCode" placeholder="Discount code"
                    class="flex-1 px-3 py-2 border border-gray-300 rounded-lg text-sm">
                <button wire:click="applyDiscount" class="px-3 py-2 bg-gray-100 text-gray-700 rounded-lg text-sm hover:bg-gray-200">
                    Apply
                </button>
            </div>
        </div>

        @if(count($items) > 0)
            <button wire:click="clearCart" class="w-full mb-3 py-2 text-sm text-gray-600 hover:text-gray-900">
                Clear Cart
            </button>
            <button wire:click="openPaymentModal" class="w-full py-3 bg-blue-600 text-white rounded-lg font-semibold hover:bg-blue-700">
                Pay ${{ $formattedTotal }}
            </button>
        @endif
    </div>

    @if($showPaymentModal)
        <div class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
            <div class="bg-white rounded-xl p-6 w-full max-w-md mx-4">
                <h3 class="text-xl font-bold mb-4">Payment</h3>
                
                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Payment Method</label>
                    <div class="grid grid-cols-2 gap-3">
                        <button wire:click="$set('paymentMethod', 'cash')" 
                            class="p-3 border rounded-lg text-center {{ $paymentMethod === 'cash' ? 'border-blue-500 bg-blue-50' : 'border-gray-300' }}">
                            <svg class="w-8 h-8 mx-auto mb-1 text-green-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 9V7a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2m2 4h10a2 2 0 002-2v-6a2 2 0 00-2-2H9a2 2 0 00-2 2v6a2 2 0 002 2zm7-5a2 2 0 11-4 0 2 2 0 014 0z"/>
                            </svg>
                            <span class="text-sm font-medium">Cash</span>
                        </button>
                        <button wire:click="$set('paymentMethod', 'card')"
                            class="p-3 border rounded-lg text-center {{ $paymentMethod === 'card' ? 'border-blue-500 bg-blue-50' : 'border-gray-300' }}">
                            <svg class="w-8 h-8 mx-auto mb-1 text-blue-600" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 10h18M7 15h1m4 0h1m-7 4h12a3 3 0 003-3V8a3 3 0 00-3-3H6a3 3 0 00-3 3v8a3 3 0 003 3z"/>
                            </svg>
                            <span class="text-sm font-medium">Card</span>
                        </button>
                    </div>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700 mb-2">Amount Paid</label>
                    <input type="number" step="0.01" wire:model.live.debounce.300ms="amountPaid" wire:change="calculateChange"
                        class="w-full px-4 py-3 text-2xl font-bold border border-gray-300 rounded-lg focus:ring-2 focus:ring-blue-500">
                </div>

                <div class="flex justify-between items-center p-4 bg-green-50 rounded-lg mb-6">
                    <span class="text-lg font-medium">Change:</span>
                    <span class="text-2xl font-bold text-green-600">${{ $formattedChange }}</span>
                </div>

                <div class="flex gap-3">
                    <button wire:click="closePaymentModal" class="flex-1 py-3 border border-gray-300 rounded-lg font-medium hover:bg-gray-50">
                        Cancel
                    </button>
                    <button wire:click="processPayment" class="flex-1 py-3 bg-green-600 text-white rounded-lg font-semibold hover:bg-green-700">
                        Complete Payment
                    </button>
                </div>
            </div>
        </div>
    @endif
</div>
