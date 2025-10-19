@extends('templates.base')

@section('title', 'Admin Dashboard - Novify')

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Admin Dashboard</h1>
    <div>
        <a href="{{ route('ui.admin.reports') }}" class="btn btn-info">
            <i class="fas fa-chart-bar fa-sm"></i> View Reports
        </a>
        <a href="{{ route('ui.admin.merchants') }}" class="btn btn-primary">
            <i class="fas fa-users fa-sm"></i> Manage Merchants
        </a>
    </div>
</div>

<!-- System Overview Cards -->
<div class="row">
    <!-- Total Merchants -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Total Merchants</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_merchants'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Active Merchants -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Active Merchants</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['active_merchants'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-check-circle fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Pending Verifications -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Pending Verifications</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['pending_merchants'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-clock fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Revenue -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            Total Revenue</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">${{ number_format($stats['total_revenue'], 2) }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Additional Stats Row -->
<div class="row">
    <!-- Total Products -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-secondary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">
                            Total Products</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_products'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-box fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Orders -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-dark shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-dark text-uppercase mb-1">
                            Total Orders</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_orders'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-shopping-cart fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Customers -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-danger shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-danger text-uppercase mb-1">
                            Total Customers</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_customers'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-user-friends fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Branches -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Total Branches</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $stats['total_branches'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-building fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Charts Row -->
<div class="row">
    <!-- Revenue Chart -->
    <div class="col-xl-8 col-lg-7">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">System Revenue Overview (Last 6 Months)</h6>
            </div>
            <div class="card-body">
                <div class="chart-area">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Merchants -->
    <div class="col-xl-4 col-lg-5">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Top Performing Merchants</h6>
            </div>
            <div class="card-body">
                @forelse($top_merchants as $merchant)
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h6 class="mb-0">{{ $merchant->store_name }}</h6>
                        <small class="text-muted">{{ $merchant->first_name }} {{ $merchant->last_name }}</small>
                    </div>
                    <div class="text-right">
                        <div class="font-weight-bold">${{ number_format($merchant->orders_sum_total_amount ?? 0, 2) }}</div>
                        <small class="text-muted">{{ $merchant->orders_count ?? 0 }} orders</small>
                    </div>
                </div>
                @empty
                <p class="text-muted">No data available</p>
                @endforelse
            </div>
        </div>
    </div>
</div>

<!-- Recent Activity Row -->
<div class="row">
    <!-- Recent Merchant Registrations -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Recent Merchant Registrations</h6>
            </div>
            <div class="card-body">
                @forelse($recent_merchants as $merchant)
                <div class="d-flex justify-content-between align-items-center mb-3">
                    <div>
                        <h6 class="mb-0">{{ $merchant->store_name }}</h6>
                        <small class="text-muted">{{ $merchant->email }}</small>
                    </div>
                    <div class="text-right">
                        <span class="badge badge-{{ $merchant->is_verified ? 'success' : 'warning' }}">
                            {{ $merchant->is_verified ? 'Verified' : 'Pending' }}
                        </span>
                        <br>
                        <small class="text-muted">{{ $merchant->created_at->diffForHumans() }}</small>
                    </div>
                </div>
                @empty
                <p class="text-muted">No recent registrations</p>
                @endforelse
            </div>
        </div>
    </div>

    <!-- Quick Actions -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Quick Actions</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-6 mb-3">
                        <a href="{{ route('ui.admin.merchants') }}" class="btn btn-outline-primary btn-block">
                            <i class="fas fa-users fa-2x mb-2"></i><br>
                            Manage Merchants
                        </a>
                    </div>
                    <div class="col-6 mb-3">
                        <a href="{{ route('ui.admin.reports') }}" class="btn btn-outline-info btn-block">
                            <i class="fas fa-chart-bar fa-2x mb-2"></i><br>
                            View Reports
                        </a>
                    </div>
                    <div class="col-6 mb-3">
                        <a href="{{ route('ui.admin.system.health') }}" class="btn btn-outline-success btn-block">
                            <i class="fas fa-heartbeat fa-2x mb-2"></i><br>
                            System Health
                        </a>
                    </div>
                    <div class="col-6 mb-3">
                        <a href="#" class="btn btn-outline-warning btn-block">
                            <i class="fas fa-cog fa-2x mb-2"></i><br>
                            Settings
                        </a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection

@section('js')
<!-- Page level plugins -->
<script src="{{ asset('vendor/chart.js/Chart.min.js') }}"></script>

<script>
// Revenue Chart
var ctx = document.getElementById('revenueChart');
var revenueChart = new Chart(ctx, {
    type: 'line',
    data: {
        labels: {!! json_encode($monthly_revenue->pluck('month')->map(function($month) {
            return date('M', mktime(0, 0, 0, $month, 1));
        })) !!},
        datasets: [{
            label: 'Revenue ($)',
            data: {!! json_encode($monthly_revenue->pluck('revenue')) !!},
            borderColor: '#4e73df',
            backgroundColor: 'rgba(78, 115, 223, 0.05)',
            borderWidth: 2,
            fill: true,
            tension: 0.4
        }]
    },
    options: {
        responsive: true,
        maintainAspectRatio: false,
        scales: {
            y: {
                beginAtZero: true,
                ticks: {
                    callback: function(value) {
                        return '$' + value.toLocaleString();
                    }
                }
            }
        },
        plugins: {
            legend: {
                display: false
            }
        }
    }
});
</script>
@endsection
