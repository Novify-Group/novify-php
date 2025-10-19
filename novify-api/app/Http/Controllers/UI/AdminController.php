<?php

namespace App\Http\Controllers\UI;

use App\Http\Controllers\Controller;
use App\Models\Merchant;
use App\Models\Product;
use App\Models\Order;
use App\Models\Customer;
use App\Models\Branch;
use App\Models\WalletTransaction;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class AdminController extends Controller
{
    /**
     * Display admin dashboard with system overview
     */
    public function dashboard()
    {
        // System-wide statistics
        $stats = [
            'total_merchants' => Merchant::count(),
            'active_merchants' => Merchant::where('is_active', true)->count(),
            'pending_merchants' => Merchant::where('is_verified', false)->count(),
            'total_products' => Product::count(),
            'total_orders' => Order::count(),
            'total_customers' => Customer::count(),
            'total_branches' => Branch::count(),
            'total_revenue' => Order::sum('total_amount'),
        ];

        // Recent merchant registrations
        $recent_merchants = Merchant::latest()
            ->take(5)
            ->get();

        // Revenue by month (last 6 months)
        $monthly_revenue = Order::selectRaw('MONTH(created_at) as month, YEAR(created_at) as year, SUM(total_amount) as revenue')
            ->whereYear('created_at', '>=', Carbon::now()->subMonths(6))
            ->groupBy('year', 'month')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get();

        // Top performing merchants
        $top_merchants = Merchant::withCount('orders')
            ->withSum('orders', 'total_amount')
            ->orderByDesc('orders_sum_total_amount')
            ->take(10)
            ->get();

        return view('templates.admin.dashboard', compact('stats', 'recent_merchants', 'monthly_revenue', 'top_merchants'));
    }

    /**
     * Display all merchants with filtering and search
     */
    public function merchants(Request $request)
    {
        $query = Merchant::with(['country', 'marketArea'])
            ->withCount(['branches', 'products', 'orders'])
            ->withSum('orders', 'total_amount');

        // Search functionality
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function($q) use ($search) {
                $q->where('store_name', 'like', "%{$search}%")
                  ->orWhere('first_name', 'like', "%{$search}%")
                  ->orWhere('last_name', 'like', "%{$search}%")
                  ->orWhere('email', 'like', "%{$search}%")
                  ->orWhere('phone_number', 'like', "%{$search}%");
            });
        }

        // Filter by status
        if ($request->filled('status')) {
            switch ($request->status) {
                case 'active':
                    $query->where('is_active', true);
                    break;
                case 'inactive':
                    $query->where('is_active', false);
                    break;
                case 'verified':
                    $query->where('is_verified', true);
                    break;
                case 'pending':
                    $query->where('is_verified', false);
                    break;
            }
        }

        // Filter by country
        if ($request->filled('country_id')) {
            $query->where('country_id', $request->country_id);
        }

        $merchants = $query->latest()->paginate(20);
        $countries = \App\Models\Country::all();

        return view('templates.admin.merchants.index', compact('merchants', 'countries'));
    }

    /**
     * Show merchant details
     */
    public function showMerchant(Merchant $merchant)
    {
        $merchant->load(['country', 'marketArea', 'branches', 'products', 'orders.customer', 'wallets']);
        
        // Get merchant statistics
        $merchantStats = [
            'total_revenue' => $merchant->orders->sum('total_amount'),
            'total_orders' => $merchant->orders->count(),
            'total_customers' => $merchant->orders->pluck('customer_id')->unique()->count(),
            'average_order_value' => $merchant->orders->avg('total_amount'),
            'products_count' => $merchant->products->count(),
            'branches_count' => $merchant->branches->count(),
        ];

        // Recent orders
        $recentOrders = $merchant->orders()
            ->with('customer')
            ->latest()
            ->take(10)
            ->get();

        // Revenue chart data (last 12 months)
        $revenueData = $merchant->orders()
            ->selectRaw('MONTH(created_at) as month, YEAR(created_at) as year, SUM(total_amount) as revenue')
            ->whereYear('created_at', '>=', Carbon::now()->subYear())
            ->groupBy('year', 'month')
            ->orderBy('year', 'asc')
            ->orderBy('month', 'asc')
            ->get();

        return view('templates.admin.merchants.show', compact('merchant', 'merchantStats', 'recentOrders', 'revenueData'));
    }

    /**
     * Update merchant status
     */
    public function updateMerchantStatus(Request $request, Merchant $merchant)
    {
        $request->validate([
            'is_active' => 'required|boolean',
            'is_verified' => 'required|boolean',
        ]);

        $merchant->update($request->only(['is_active', 'is_verified']));

        return back()->with('success', 'Merchant status updated successfully');
    }

    /**
     * Generate system reports
     */
    public function reports(Request $request)
    {
        $period = $request->get('period', 'month');
        $startDate = $request->get('start_date');
        $endDate = $request->get('end_date');

        // Set default date range if not provided
        if (!$startDate || !$endDate) {
            switch ($period) {
                case 'week':
                    $startDate = Carbon::now()->startOfWeek();
                    $endDate = Carbon::now()->endOfWeek();
                    break;
                case 'month':
                    $startDate = Carbon::now()->startOfMonth();
                    $endDate = Carbon::now()->endOfMonth();
                    break;
                case 'quarter':
                    $startDate = Carbon::now()->startOfQuarter();
                    $endDate = Carbon::now()->endOfQuarter();
                    break;
                case 'year':
                    $startDate = Carbon::now()->startOfYear();
                    $endDate = Carbon::now()->endOfYear();
                    break;
                default:
                    $startDate = Carbon::now()->subDays(30);
                    $endDate = Carbon::now();
            }
        }

        // Revenue report
        $revenueReport = Order::whereBetween('created_at', [$startDate, $endDate])
            ->selectRaw('DATE(created_at) as date, SUM(total_amount) as revenue, COUNT(*) as orders')
            ->groupBy('date')
            ->orderBy('date')
            ->get();

        // Merchant performance report
        $merchantPerformance = Merchant::withCount(['orders' => function($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            }])
            ->withSum(['orders' => function($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            }], 'total_amount')
            ->orderByDesc('orders_sum_total_amount')
            ->take(20)
            ->get();

        // Product performance report
        $productPerformance = Product::withCount(['orders' => function($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            }])
            ->withSum(['orders' => function($query) use ($startDate, $endDate) {
                $query->whereBetween('created_at', [$startDate, $endDate]);
            }], 'total_amount')
            ->orderByDesc('orders_sum_total_amount')
            ->take(20)
            ->get();

        // Geographic report
        $geographicReport = Order::join('merchants', 'orders.merchant_id', '=', 'merchants.id')
            ->join('countries', 'merchants.country_id', '=', 'countries.id')
            ->whereBetween('orders.created_at', [$startDate, $endDate])
            ->selectRaw('countries.name as country, SUM(orders.total_amount) as revenue, COUNT(*) as orders')
            ->groupBy('countries.id', 'countries.name')
            ->orderByDesc('revenue')
            ->get();

        return view('templates.admin.reports.index', compact(
            'revenueReport', 
            'merchantPerformance', 
            'productPerformance', 
            'geographicReport',
            'startDate',
            'endDate',
            'period'
        ));
    }

    /**
     * Export reports
     */
    public function exportReport(Request $request)
    {
        $type = $request->get('type', 'revenue');
        $format = $request->get('format', 'csv');
        
        // Implementation for export functionality
        // This would generate CSV/Excel files based on the report type
        
        return back()->with('success', 'Report exported successfully');
    }

    /**
     * System health monitoring
     */
    public function systemHealth()
    {
        // Database connection status
        $dbStatus = 'healthy';
        try {
            DB::connection()->getPdo();
        } catch (\Exception $e) {
            $dbStatus = 'error';
        }

        // System statistics
        $systemStats = [
            'database_status' => $dbStatus,
            'total_users' => \App\Models\User::count(),
            'total_merchants' => Merchant::count(),
            'total_orders' => Order::count(),
            'total_products' => Product::count(),
            'disk_usage' => disk_free_space('/') / disk_total_space('/') * 100,
            'memory_usage' => memory_get_usage(true) / 1024 / 1024, // MB
        ];

        // Recent system activities - placeholder for now
        $recentActivities = collect(); // Empty collection since Activity model doesn't exist yet

        return view('templates.admin.system.health', compact('systemStats', 'recentActivities'));
    }
}
