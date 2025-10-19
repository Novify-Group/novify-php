@extends('templates.base')

@section('title', 'Manage Merchants - Admin')

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Manage Merchants</h1>
    <div>
        <a href="{{ route('ui.admin.dashboard') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left fa-sm"></i> Back to Dashboard
        </a>
    </div>
</div>

<!-- Filters and Search -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">Filters & Search</h6>
    </div>
    <div class="card-body">
        <form method="GET" action="{{ route('ui.admin.merchants.index') }}" class="row">
            <div class="col-md-4 mb-3">
                <label for="search">Search</label>
                <input type="text" class="form-control" id="search" name="search" 
                       value="{{ request('search') }}" placeholder="Store name, email, phone...">
            </div>
            <div class="col-md-3 mb-3">
                <label for="status">Status</label>
                <select class="form-control" id="status" name="status">
                    <option value="">All Statuses</option>
                    <option value="active" {{ request('status') == 'active' ? 'selected' : '' }}>Active</option>
                    <option value="inactive" {{ request('status') == 'inactive' ? 'selected' : '' }}>Inactive</option>
                    <option value="verified" {{ request('status') == 'verified' ? 'selected' : '' }}>Verified</option>
                    <option value="pending" {{ request('status') == 'pending' ? 'selected' : '' }}>Pending</option>
                </select>
            </div>
            <div class="col-md-3 mb-3">
                <label for="country_id">Country</label>
                <select class="form-control" id="country_id" name="country_id">
                    <option value="">All Countries</option>
                    @foreach($countries as $country)
                        <option value="{{ $country->id }}" {{ request('country_id') == $country->id ? 'selected' : '' }}>
                            {{ $country->name }}
                        </option>
                    @endforeach
                </select>
            </div>
            <div class="col-md-2 mb-3">
                <label>&nbsp;</label>
                <div>
                    <button type="submit" class="btn btn-primary btn-block">
                        <i class="fas fa-search"></i> Filter
                    </button>
                </div>
            </div>
        </form>
    </div>
</div>

<!-- Merchants Table -->
<div class="card shadow mb-4">
    <div class="card-header py-3">
        <h6 class="m-0 font-weight-bold text-primary">All Merchants</h6>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered" id="merchantsTable" width="100%" cellspacing="0">
                <thead>
                    <tr>
                        <th>Store</th>
                        <th>Owner</th>
                        <th>Contact</th>
                        <th>Location</th>
                        <th>Stats</th>
                        <th>Status</th>
                        <th>Actions</th>
                    </tr>
                </thead>
                <tbody>
                    @forelse($merchants as $merchant)
                    <tr>
                        <td>
                            <div>
                                <strong>{{ $merchant->store_name }}</strong>
                                <br>
                                <small class="text-muted">ID: {{ $merchant->id }}</small>
                            </div>
                        </td>
                        <td>
                            <div>
                                {{ $merchant->first_name }} {{ $merchant->last_name }}
                                <br>
                                <small class="text-muted">{{ $merchant->email }}</small>
                            </div>
                        </td>
                        <td>
                            <div>
                                <i class="fas fa-phone"></i> {{ $merchant->phone_number }}
                                @if($merchant->email)
                                    <br>
                                    <i class="fas fa-envelope"></i> {{ $merchant->email }}
                                @endif
                            </div>
                        </td>
                        <td>
                            <div>
                                <i class="fas fa-map-marker-alt"></i> {{ $merchant->country->name ?? 'N/A' }}
                                @if($merchant->city)
                                    <br>
                                    <small class="text-muted">{{ $merchant->city }}</small>
                                @endif
                            </div>
                        </td>
                        <td>
                            <div class="text-center">
                                <div class="row">
                                    <div class="col-4">
                                        <div class="text-primary font-weight-bold">{{ $merchant->branches_count ?? 0 }}</div>
                                        <small class="text-muted">Branches</small>
                                    </div>
                                    <div class="col-4">
                                        <div class="text-success font-weight-bold">{{ $merchant->products_count ?? 0 }}</div>
                                        <small class="text-muted">Products</small>
                                    </div>
                                    <div class="col-4">
                                        <div class="text-info font-weight-bold">{{ $merchant->orders_count ?? 0 }}</div>
                                        <small class="text-muted">Orders</small>
                                    </div>
                                </div>
                                @if($merchant->orders_sum_total_amount)
                                    <div class="text-success mt-1">
                                        <strong>${{ number_format($merchant->orders_sum_total_amount, 2) }}</strong>
                                    </div>
                                @endif
                            </div>
                        </td>
                        <td>
                            <div class="text-center">
                                @if($merchant->is_verified)
                                    <span class="badge badge-success">Verified</span>
                                @else
                                    <span class="badge badge-warning">Pending</span>
                                @endif
                                <br>
                                @if($merchant->is_active)
                                    <span class="badge badge-success">Active</span>
                                @else
                                    <span class="badge badge-danger">Inactive</span>
                                @endif
                                <br>
                                <small class="text-muted">{{ $merchant->created_at->diffForHumans() }}</small>
                            </div>
                        </td>
                        <td>
                            <div class="btn-group-vertical">
                                <a href="{{ route('ui.admin.merchants.show', $merchant) }}" 
                                   class="btn btn-sm btn-info mb-1">
                                    <i class="fas fa-eye"></i> View
                                </a>
                                <button type="button" class="btn btn-sm btn-warning mb-1" 
                                        onclick="editMerchantStatus({{ $merchant->id }}, '{{ $merchant->is_active }}', '{{ $merchant->is_verified }}')">
                                    <i class="fas fa-edit"></i> Edit Status
                                </button>
                            </div>
                        </td>
                    </tr>
                    @empty
                    <tr>
                        <td colspan="7" class="text-center">No merchants found</td>
                    </tr>
                    @endforelse
                </tbody>
            </table>
        </div>
        
        @if($merchants->hasPages())
            <div class="d-flex justify-content-center">
                {{ $merchants->links() }}
            </div>
        @endif
    </div>
</div>

<!-- Edit Merchant Status Modal -->
<div class="modal fade" id="editMerchantStatusModal" tabindex="-1" role="dialog" aria-labelledby="editMerchantStatusModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editMerchantStatusModalLabel">Edit Merchant Status</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="editMerchantStatusForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="form-group">
                        <label for="is_active">Account Status</label>
                        <select class="form-control" id="is_active" name="is_active" required>
                            <option value="1">Active</option>
                            <option value="0">Inactive</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="is_verified">Verification Status</label>
                        <select class="form-control" id="is_verified" name="is_verified" required>
                            <option value="1">Verified</option>
                            <option value="0">Pending</option>
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
<script src="{{ asset('vendor/datatables/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('vendor/datatables/dataTables.bootstrap4.min.js') }}"></script>

<script>
$(document).ready(function() {
    $('#merchantsTable').DataTable({
        "pageLength": 25,
        "order": [[0, "asc"]]
    });
});

function editMerchantStatus(merchantId, isActive, isVerified) {
    // Set form action
    $('#editMerchantStatusForm').attr('action', `/admin/merchants/${merchantId}/status`);
    
    // Set current values
    $('#is_active').val(isActive);
    $('#is_verified').val(isVerified);
    
    // Show modal
    $('#editMerchantStatusModal').modal('show');
}
</script>
@endsection
