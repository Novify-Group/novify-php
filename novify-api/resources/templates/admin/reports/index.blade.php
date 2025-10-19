@extends('templates.base')

@section('title', 'System Reports - Admin')

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">System Reports</h1>
    <div>
        <a href="{{ route('ui.admin.dashboard') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left fa-sm"></i> Back to Dashboard
        </a>
    </div>
</div>

<!-- Report Filters -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Report Filters</h6>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('ui.admin.reports') }}" class="row">
            <div class="col-md-3 mb-3">
                <label for="period">Period</label>
                <select class="form-control" id="period" name="period">
                    <option value="week" {{ $period == 'week' ? 'selected' : '' }}>This Week</option>
                    <option value="month" {{ $period == 'month' ? 'selected' : '' }}>This Month</option>
                    <option value="quarter" {{ $period == 'month' ? 'selected' : '' }}>This Quarter</option>
                    <option value="year" {{ $period == 'year' ? 'selected' : '' }}>This Year</option>
                    <option value="custom" {{ $period == 'custom' ? 'selected' : '' }}>Custom Range</option>
                </select>
            </div>
            <div class="col-md-3 mb-3">
                <label for="start_date">Start Date</label>
                <input type="date" class="form-control" id="start_date" name="start_date" 
                       value="{{ $startDate ? $startDate->format('Y-m-d') : '' }}">
            </div>
            <div class="col-md-3 mb-3">
                <label for="end_date">End Date</label>
                <input type="date" class="form-control" id="end_date" name="end_date" 
                       value="{{ $endDate ? $endDate->format('Y-m-d') : '' }}">
            </div>
            <div class="col-md-3 mb-3">
                <label>&nbsp;</label>
                <div>
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-search"></i> Generate Report
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Revenue Report -->
<div class="row">
    <div class="col-xl-8 col-lg-7">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Revenue Report</h6>
            </div>
            <div class="card-body">
                <div class="chart-area">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>
        </div>
    </div>

    <!-- Summary Stats -->
    <div class="col-xl-4 col-lg-5">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Summary</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                        Total Revenue</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                        ${{ number_format($revenueReport->sum('revenue'), 2) }}
                    </div>
                </div>
                <div class="mb-3">
                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                        Total Orders</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                        {{ $revenueReport->sum('orders') }}
                    </div>
                </div>
                <div class="mb-3">
                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                        Average Order Value</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                        ${{ $revenueReport->sum('orders') > 0 ? number_format($revenueReport->sum('revenue') / $revenueReport->sum('orders'), 2) : '0.00' }}
                    </div>
                </div>
                <div class="mb-3">
                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                        Date Range</div>
                    <div class="h6 mb-0 font-weight-bold text-gray-800">
                        {{ $startDate->format('M d, Y') }} - {{ $endDate->format('M d, Y') }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Performance Reports -->
<div class="row">
    <!-- Top Merchants -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Top Performing Merchants</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Merchant</th>
                                <th>Orders</th>
                                <th>Revenue</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($merchantPerformance as $merchant)
                            <tr>
                                <td>
                                    <div>
                                        <strong>{{ $merchant->store_name }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $merchant->first_name }} {{ $merchant->last_name }}</small>
                                    </div>
                                </td>
                                <td class="text-center">{{ $merchant->orders_count ?? 0 }}</td>
                                <td class="text-right">${{ number_format($merchant->orders_sum_total_amount ?? 0, 2) }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center">No data available</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <!-- Top Products -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Top Performing Products</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-sm">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Orders</th>
                                <th>Revenue</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($productPerformance as $product)
                            <tr>
                                <td>
                                    <div>
                                        <strong>{{ $product->name }}</strong>
                                        <br>
                                        <small class="text-muted">{{ $product->sku }}</small>
                                    </div>
                                </td>
                                <td class="text-center">{{ $product->orders_count ?? 0 }}</td>
                                <td class="text-right">${{ number_format($product->orders_sum_total_amount ?? 0, 2) }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="3" class="text-center">No data available</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Geographic Report -->
<div class="row">
    <div class="col-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Geographic Performance</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Country</th>
                                <th>Total Orders</th>
                                <th>Total Revenue</th>
                                <th>Average Order Value</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($geographicReport as $geo)
                            <tr>
                                <td>
                                    <strong>{{ $geo->country }}</strong>
                                </td>
                                <td class="text-center">{{ $geo->orders }}</td>
                                <td class="text-right">${{ number_format($geo->revenue, 2) }}</td>
                                <td class="text-right">${{ $geo->orders > 0 ? number_format($geo->revenue / $geo->orders, 2) : '0.00' }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="4" class="text-center">No data available</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Export Options -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Export Reports</h6>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-3 mb-3">
                <form method="POST" action="{{ route('ui.admin.reports.export') }}">
                    @csrf
                    <input type="hidden" name="type" value="revenue">
                    <input type="hidden" name="start_date" value="{{ $startDate->format('Y-m-d') }}">
                    <input type="hidden" name="end_date" value="{{ $endDate->format('Y-m-d') }}">
                    <button type="submit" class="btn btn-success btn-block">
                        <i class="fas fa-download"></i> Export Revenue Report
                    </button>
                </form>
            </div>
            <div class="col-md-3 mb-3">
                <form method="POST" action="{{ route('ui.admin.reports.export') }}">
                    @csrf
                    <input type="hidden" name="type" value="merchants">
                    <input type="hidden" name="start_date" value="{{ $startDate->format('Y-m-d') }}">
                    <input type="hidden" name="end_date" value="{{ $endDate->format('Y-m-d') }}">
                    <button type="submit" class="btn btn-info btn-block">
                        <i class="fas fa-download"></i> Export Merchant Report
                    </button>
                </form>
            </div>
            <div class="col-md-3 mb-3">
                <form method="POST" action="{{ route('ui.admin.reports.export') }}">
                    @csrf
                    <input type="hidden" name="type" value="products">
                    <input type="hidden" name="start_date" value="{{ $startDate->format('Y-m-d') }}">
                    <input type="hidden" name="end_date" value="{{ $endDate->format('Y-m-d') }}">
                    <button type="submit" class="btn btn-warning btn-block">
                        <i class="fas fa-download"></i> Export Product Report
                    </button>
                </form>
            </div>
            <div class="col-md-3 mb-3">
                <form method="POST" action="{{ route('ui.admin.reports.export') }}">
                    @csrf
                    <input type="hidden" name="type" value="geographic">
                    <input type="hidden" name="start_date" value="{{ $startDate->format('Y-m-d') }}">
                    <input type="hidden" name="end_date" value="{{ $endDate->format('Y-m-d') }}">
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-download"></i> Export Geographic Report
                    </button>
                </form>
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
        labels: {!! json_encode($revenueReport->pluck('date')->map(function($date) {
            return \Carbon\Carbon::parse($date)->format('M d');
        })) !!},
        datasets: [{
            label: 'Revenue ($)',
            data: {!! json_encode($revenueReport->pluck('revenue')) !!},
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

// Handle period change
$('#period').change(function() {
    if ($(this).val() === 'custom') {
        $('#start_date, #end_date').prop('disabled', false);
    } else {
        $('#start_date, #end_date').prop('disabled', true);
    }
});

// Initialize
$(document).ready(function() {
    if ($('#period').val() !== 'custom') {
        $('#start_date, #end_date').prop('disabled', true);
    }
});
</script>
@endsection
