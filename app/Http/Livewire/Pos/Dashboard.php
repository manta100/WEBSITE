<?php

namespace App\Http\Livewire\Pos;

use Livewire\Component;
use App\Models\Order;
use App\Services\AnalyticsService;

class Dashboard extends Component
{
    public array $todayStats = [];
    public array $topProducts = [];
    public array $recentOrders = [];
    public array $inventoryStats = [];

    public function mount(): void
    {
        $this->loadDashboardData();
    }

    public function loadDashboardData(): void
    {
        $analyticsService = app(AnalyticsService::class);

        $this->todayStats = $analyticsService->getTodayStats();
        $this->topProducts = $analyticsService->getTopProducts(5);
        $this->inventoryStats = $analyticsService->getInventoryStats();
        $this->recentOrders = Order::with('user')
            ->completed()
            ->latest()
            ->limit(10)
            ->get()
            ->toArray();
    }

    public function render()
    {
        return view('livewire.pos.dashboard');
    }
}
