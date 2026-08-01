<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use App\Models\Order;
use App\Models\Product;

class DashboardController extends Controller
{
    public function index()
    {
        $stats = [
            'total_products'  => Product::count(),
            'active_products' => Product::where('is_active', true)->count(),
            'total_orders'    => Order::count(),
            'paid_orders'     => Order::where('payment_status', 'paid')->count(),
            'total_revenue'   => Order::where('payment_status', 'paid')->sum('total_amount'),
            'categories'      => Category::count(),
        ];

        $recentOrders = Order::with('product')
            ->latest()
            ->take(10)
            ->get();

        $topProducts = Product::withCount(['orders' => fn ($q) => $q->where('payment_status', 'paid')])
            ->orderByDesc('orders_count')
            ->take(5)
            ->get();

        return view('admin.dashboard', compact('stats', 'recentOrders', 'topProducts'));
    }
}
