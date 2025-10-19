@extends('templates.base')

@section('title', 'Merchant Details - Admin')

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Merchant Details</h1>
    <div>
        <a href="{{ route('ui.admin.merchants.index') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left fa-sm"></i> Back to Merchants
        </a>
        <button type="button" class="btn btn-warning" onclick="editMerchantStatus()">
            <i class="fas fa-edit fa-sm"></i> Edit Status
        </button>
    </div>
</div>

<!-- Merchant Information -->
<div class="row">
    <!-- Basic Info -->
    <div class="col-lg-8">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Merchant Information</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-6">
                        <h6 class="font-weight-bold">Store Details</h6>
                        <p><strong>Store Name:</strong> {{ $merchant->store_name }}</p>
                        <p><strong>Store Type:</strong> {{ $merchant->store_type ?? 'N/A' }}</p>
                        <p><strong>Business Type:</strong> {{ $merchant->business_type ?? 'N/A' }}</p>
                        <p><strong>Tax ID:</strong> {{ $merchant->tax_id ?? 'N/A' }}</p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="font-weight-bold">Owner Details</h6>
                        <p><strong>Name:</strong> {{ $merchant->first_name }} {{ $merchant->last_name }}</p>
                        <p><strong>Email:</strong> {{ $merchant->email }}</p>
                        <p><strong>Phone:</strong> {{ $merchant->phone_number }}</p>
                        <p><strong>Date of Birth:</strong> {{ $merchant->date_of_birth ? $merchant->date_of_birth->format('M d, Y') : 'N/A' }}</p>
                    </div>
                </div>
                
                <div class="row mt-3">
                    <div class="col-md-6">
                        <h6 class="font-weight-bold">Location</h6>
                        <p><strong>Country:</strong> {{ $merchant->country->name ?? 'N/A' }}</p>
                        <p><strong>City:</strong> {{ $merchant->city ?? 'N/A' }}</p>
                        <p><strong>Address:</strong> {{ $merchant->address ?? 'N/A' }}</p>
                        <p><strong>Postal Code:</strong> {{ $merchant->postal_code ?? 'N/A' }}</p>
                    </div>
                    <div class="col-md-6">
                        <h6 class="font-weight-bold">Status</h6>
                        <p>
                            <strong>Account Status:</strong> 
                            @if($merchant->is_active)
                                <span class="badge badge-success">Active</span>
                            @else
                                <span class="badge badge-danger">Inactive</span>
                            @endif
                        </p>
                        <p>
                            <strong>Verification:</strong> 
                            @if($merchant->is_verified)
                                <span class="badge badge-success">Verified</span>
                            @else
                                <span class="badge badge-warning">Pending</span>
                            @endif
                        </p>
                        <p><strong>Joined:</strong> {{ $merchant->created_at->format('M d, Y') }}</p>
                        <p><strong>Last Updated:</strong> {{ $merchant->updated_at->format('M d, Y') }}</p>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Statistics -->
    <div class="col-lg-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Statistics</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                        Total Revenue</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                        ${{ number_format($merchantStats['total_revenue'], 2) }}
                    </div>
                </div>
                <div class="mb-3">
                    <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                        Total Orders</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                        {{ $merchantStats['total_orders'] }}
                    </div>
                </div>
                <div class="mb-3">
                    <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                        Total Customers</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                        {{ $merchantStats['total_customers'] }}
                    </div>
                </div>
                <div class="mb-3">
                    <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                        Average Order Value</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                        ${{ number_format($merchantStats['average_order_value'], 2) }}
                    </div>
                </div>
                <div class="mb-3">
                    <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">
                        Products</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                        {{ $merchantStats['products_count'] }}
                    </div>
                </div>
                <div class="mb-3">
                    <div class="text-xs font-weight-bold text-dark text-uppercase mb-1">
                        Branches</div>
                    <div class="h5 mb-0 font-weight-bold text-gray-800">
                        {{ $merchantStats['branches_count'] }}
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Revenue Chart -->
<div class="row">
    <div class="col-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Revenue Overview (Last 12 Months)</h6>
            </div>
            <div class="card-body">
                <div class="chart-area">
                    <canvas id="revenueChart"></canvas>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Orders -->
<div class="row">
    <div class="col-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Recent Orders</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered">
                        <thead>
                            <tr>
                                <th>Order ID</th>
                                <th>Customer</th>
                                <th>Amount</th>
                                <th>Status</th>
                                <th>Date</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($recentOrders as $order)
                            <tr>
                                <td>{{ $order->id }}</td>
                                <td>
                                    @if($order->customer)
                                        {{ $order->customer->first_name }} {{ $order->customer->last_name }}
                                    @else
                                        N/A
                                    @endif
                                </td>
                                <td>${{ number_format($order->total_amount, 2) }}</td>
                                <td>
                                    <span class="badge badge-{{ $order->status === 'completed' ? 'success' : ($order->status === 'pending' ? 'warning' : 'info') }}">
                                        {{ ucfirst($order->status) }}
                                    </span>
                                </td>
                                <td>{{ $order->created_at->format('M d, Y H:i') }}</td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="5" class="text-center">No orders found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Edit Status Modal -->
<div class="modal fade" id="editMerchantStatusModal" tabindex="-1" role="dialog" aria-labelledby="editMerchantStatusModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editMerchantStatusModalLabel">Edit Merchant Status</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST" action="{{ route('ui.admin.merchants.update-status', $merchant) }}">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="form-group">
                        <label for="is_active">Account Status</label>
                        <select class="form-control" id="is_active" name="is_active" required>
                            <option value="1" {{ $merchant->is_active ? 'selected' : '' }}>Active</option>
                            <option value="0" {{ !$merchant->is_active ? 'selected' : '' }}>Inactive</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="is_verified">Verification Status</label>
                        <select class="form-control" id="is_verified" name="is_verified" required>
                            <option value="1" {{ $merchant->is_verified ? 'selected' : '' }}>Verified</option>
                            <option value="0" {{ !$merchant->is_verified ? 'selected' : '' }}>Pending</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Status</button>
                </div>
            </form>
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
        labels: {!! json_encode($revenueData->pluck('month')->map(function($month) {
            return date('M', mktime(0, 0, 0, $month, 1));
        })) !!},
        datasets: [{
            label: 'Revenue ($)',
            data: {!! json_encode($revenueData->pluck('revenue')) !!},
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

function editMerchantStatus() {
    $('#editMerchantStatusModal').modal('show');
}
</script>
@endsection
