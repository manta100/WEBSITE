@extends('layouts.app')

@section('content')
<div class="min-h-screen bg-gray-100">
    <nav class="bg-white shadow-sm">
        <div class="max-w-7xl mx-auto px-4 sm:px-6 lg:px-8">
            <div class="flex justify-between h-16">
                <div class="flex items-center">
                    <h1 class="text-xl font-bold">{{ tenant()->name }} Dashboard</h1>
                </div>
                <div class="flex items-center space-x-4">
                    <a href="{{ route('pos') }}" class="px-4 py-2 bg-blue-600 text-white rounded-lg hover:bg-blue-700">Open POS</a>
                    <form action="{{ route('logout') }}" method="POST" class="inline">
                        @csrf
                        <button type="submit" class="px-4 py-2 text-gray-700 hover:text-gray-900">Logout</button>
                    </form>
                </div>
            </div>
        </div>
    </nav>

    <main class="max-w-7xl mx-auto py-6 px-4 sm:px-6 lg:px-8">
        @livewire('pos.dashboard')
    </main>
</div>
@endsection
