<?php

namespace App\Http\Controllers\UI;

use App\Http\Controllers\Controller;
use App\Models\Merchant;
use App\Models\Product;
use App\Models\Order;
use App\Models\Customer;
use App\Models\Branch;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class DashboardController extends Controller
{
    public function index()
    {
        // Get authenticated merchant
        $merchant = Auth::user();
        
        // Get dashboard statistics
        $stats = [
            'total_products' => Product::where('merchant_id', $merchant->id)->count(),
            'total_orders' => Order::where('merchant_id', $merchant->id)->count(),
            'total_customers' => Customer::where('merchant_id', $merchant->id)->count(),
            'total_branches' => Branch::where('merchant_id', $merchant->id)->count(),
            'monthly_revenue' => Order::where('merchant_id', $merchant->id)
                ->whereMonth('created_at', now()->month)
                ->sum('total_amount'),
            'annual_revenue' => Order::where('merchant_id', $merchant->id)
                ->whereYear('created_at', now()->year)
                ->sum('total_amount'),
        ];

        // Get recent orders
        $recent_orders = Order::where('merchant_id', $merchant->id)
            ->with('customer')
            ->latest()
            ->take(5)
            ->get();

        // Get low stock products
        $low_stock_products = Product::where('merchant_id', $merchant->id)
            ->where('is_inventory_tracked', true)
            ->where('stock_quantity', '<=', 'min_stock_level')
            ->take(5)
            ->get();

        return view('templates.dashboard', compact('stats', 'recent_orders', 'low_stock_products'));
    }
}
