<?php

namespace App\Services;

use App\Models\Order;
use App\Models\Product;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AnalyticsService
{
    public function getDailyRevenue(int $days = 30): array
    {
        return Order::completed()
            ->selectRaw('DATE(created_at) as date, SUM(total) as revenue, COUNT(*) as orders')
            ->where('created_at', '>=', now()->subDays($days))
            ->groupBy('date')
            ->orderBy('date')
            ->get()
            ->mapWithKeys(fn ($item) => [$item->date => [
                'revenue' => (float) $item->revenue,
                'orders' => $item->orders,
            ]])
            ->toArray();
    }

    public function getMonthlyRevenue(int $months = 12): array
    {
        return Order::completed()
            ->selectRaw('YEAR(created_at) as year, MONTH(created_at) as month, SUM(total) as revenue, COUNT(*) as orders')
            ->where('created_at', '>=', now()->subMonths($months))
            ->groupBy('year', 'month')
            ->orderBy('year')
            ->orderBy('month')
            ->get()
            ->mapWithKeys(fn ($item) => [$item->year . '-' . str_pad($item->month, 2, '0', STR_PAD_LEFT) => [
                'revenue' => (float) $item->revenue,
                'orders' => $item->orders,
            ]])
            ->toArray();
    }

    public function getTodayStats(): array
    {
        $today = today();
        
        $orders = Order::completed()->today()->get();
        
        return [
            'revenue' => (float) $orders->sum('total'),
            'orders' => $orders->count(),
            'average_order_value' => $orders->count() > 0 
                ? (float) $orders->avg('total') 
                : 0,
            'items_sold' => (int) DB::table('order_items')
                ->whereIn('order_id', $orders->pluck('id'))
                ->sum('quantity'),
        ];
    }

    public function getTopProducts(int $limit = 10, string $period = 'month'): array
    {
        $query = DB::table('order_items')
            ->join('orders', 'order_items.order_id', '=', 'orders.id')
            ->join('products', 'order_items.product_id', '=', 'products.id')
            ->where('orders.status', 'completed');

        if ($period === 'today') {
            $query->whereDate('orders.created_at', today());
        } elseif ($period === 'week') {
            $query->where('orders.created_at', '>=', now()->startOfWeek());
        } else {
            $query->where('orders.created_at', '>=', now()->startOfMonth());
        }

        return $query
            ->selectRaw('products.id, products.name, products.sku, SUM(order_items.quantity) as total_sold, SUM(order_items.total) as revenue')
            ->groupBy('products.id', 'products.name', 'products.sku')
            ->orderByDesc('total_sold')
            ->limit($limit)
            ->get()
            ->toArray();
    }

    public function getInventoryStats(): array
    {
        $products = Product::all();
        
        $lowStock = $products->filter(fn ($p) => $p->isLowStock())->count();
        $outOfStock = $products->filter(fn ($p) => $p->stock_quantity <= 0)->count();
        $totalValue = $products->sum(fn ($p) => $p->stock_quantity * $p->cost_price);
        $retailValue = $products->sum(fn ($p) => $p->stock_quantity * $p->price);

        return [
            'total_products' => $products->count(),
            'total_items_in_stock' => $products->sum('stock_quantity'),
            'low_stock_count' => $lowStock,
            'out_of_stock_count' => $outOfStock,
            'total_inventory_value' => $totalValue,
            'total_retail_value' => $retailValue,
            'potential_profit' => $retailValue - $totalValue,
        ];
    }

    public function getSalesByPaymentMethod(): array
    {
        return Order::completed()
            ->selectRaw('payment_method, COUNT(*) as count, SUM(total) as revenue')
            ->groupBy('payment_method')
            ->get()
            ->mapWithKeys(fn ($item) => [$item->payment_method => [
                'count' => $item->count,
                'revenue' => (float) $item->revenue,
            ]])
            ->toArray();
    }

    public function getHourlySales(int $hours = 24): array
    {
        return Order::completed()
            ->selectRaw('HOUR(created_at) as hour, COUNT(*) as orders, SUM(total) as revenue')
            ->where('created_at', '>=', now()->subHours($hours))
            ->groupBy('hour')
            ->orderBy('hour')
            ->get()
            ->mapWithKeys(fn ($item) => [$item->hour => [
                'orders' => $item->orders,
                'revenue' => (float) $item->revenue,
            ]])
            ->toArray();
    }

    public function getStaffPerformance(int $days = 30): array
    {
        return DB::table('orders')
            ->join('users', 'orders.user_id', '=', 'users.id')
            ->where('orders.status', 'completed')
            ->where('orders.created_at', '>=', now()->subDays($days))
            ->selectRaw('users.id, users.name, COUNT(orders.id) as orders, SUM(orders.total) as revenue')
            ->groupBy('users.id', 'users.name')
            ->orderByDesc('revenue')
            ->get()
            ->toArray();
    }

    public function getGrowthMetrics(): array
    {
        $thisMonth = Order::completed()
            ->whereMonth('created_at', now()->month)
            ->whereYear('created_at', now()->year);
        
        $lastMonth = Order::completed()
            ->whereMonth('created_at', now()->subMonth()->month)
            ->whereYear('created_at', now()->subMonth()->year);

        $currentRevenue = (float) $thisMonth->sum('total');
        $previousRevenue = (float) $lastMonth->sum('total');
        $currentOrders = $thisMonth->count();
        $previousOrders = $lastMonth->count();

        return [
            'revenue_growth' => $previousRevenue > 0 
                ? (($currentRevenue - $previousRevenue) / $previousRevenue) * 100 
                : 0,
            'order_growth' => $previousOrders > 0 
                ? (($currentOrders - $previousOrders) / $previousOrders) * 100 
                : 0,
            'current_month_revenue' => $currentRevenue,
            'previous_month_revenue' => $previousRevenue,
        ];
    }
}
