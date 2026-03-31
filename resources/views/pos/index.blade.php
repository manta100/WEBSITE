@extends('layouts.pos')

@section('content')
<div class="flex h-screen bg-gray-100">
    <div class="flex-1 p-6 overflow-auto">
        @livewire('pos.product-search')
    </div>
    <div class="w-96 bg-white shadow-xl border-l border-gray-200">
        @livewire('pos.cart')
    </div>
</div>
@endsection
