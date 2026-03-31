@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gradient-to-br from-blue-50 to-indigo-100">
    <div class="max-w-4xl mx-auto py-12 px-4 sm:px-6 lg:px-8">
        <div class="text-center mb-12">
            <h1 class="text-4xl font-bold text-gray-900 mb-4">Choose Your Plan</h1>
            <p class="text-lg text-gray-600">Unlock powerful features for your business</p>
        </div>

        @if(session('success'))
            <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded mb-6">
                {{ session('success') }}
            </div>
        @endif

        <div class="grid md:grid-cols-3 gap-8">
            @foreach($plans as $plan)
                <div class="bg-white rounded-2xl shadow-lg p-8 {{ $loop->first ? 'border-2 border-blue-500' : '' }}">
                    <h3 class="text-2xl font-bold text-gray-900 mb-2">{{ $plan->name }}</h3>
                    <p class="text-gray-600 mb-6">{{ $plan->description ?? 'Best for growing businesses' }}</p>
                    
                    <div class="mb-6">
                        <span class="text-5xl font-bold text-gray-900">${{ number_format($plan->price, 2) }}</span>
                        <span class="text-gray-600">/{{ $plan->interval }}</span>
                    </div>

                    <ul class="space-y-3 mb-8">
                        @if($plan->product_limit === -1)
                            <li class="flex items-center text-gray-700">
                                <svg class="w-5 h-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                Unlimited Products
                            </li>
                        @else
                            <li class="flex items-center text-gray-700">
                                <svg class="w-5 h-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                {{ $plan->product_limit }} Products
                            </li>
                        @endif
                        <li class="flex items-center text-gray-700">
                            <svg class="w-5 h-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            {{ $plan->staff_limit }} Staff Members
                        </li>
                        <li class="flex items-center text-gray-700">
                            <svg class="w-5 h-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                            {{ $plan->store_limit }} Store(s)
                        </li>
                        @if($plan->has_analytics)
                            <li class="flex items-center text-gray-700">
                                <svg class="w-5 h-5 text-green-500 mr-2" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"/></svg>
                                Analytics Dashboard
                            </li>
                        @endif
                    </ul>

                    <form action="{{ route('subscription.subscribe') }}" method="POST">
                        @csrf
                        <input type="hidden" name="plan_id" value="{{ $plan->id }}">
                        <button type="submit" class="w-full py-3 px-4 rounded-lg font-semibold {{ $loop->first ? 'bg-blue-600 text-white hover:bg-blue-700' : 'bg-gray-100 text-gray-900 hover:bg-gray-200' }}">
                            Subscribe Now
                        </button>
                    </form>
                </div>
            @endforeach
        </div>

        <div class="mt-12 text-center">
            <a href="{{ route('pos') }}" class="text-blue-600 hover:underline">Continue with trial</a>
        </div>
    </div>
</div>
@endsection
