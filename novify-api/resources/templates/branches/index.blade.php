@extends('templates.base')

@section('title', 'Branches - Novify')

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Branches</h1>
    <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addBranchModal">
        <i class="fas fa-plus fa-sm"></i> Add Branch
    </button>
</div>

<!-- Content Row -->
<div class="row">
    <div class="col-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">All Branches</h6>
            </div>
            <div class="card-body">
                <div class="table-responsive">
                    <table class="table table-bordered" id="branchesTable" width="100%" cellspacing="0">
                        <thead>
                            <tr>
                                <th>Name</th>
                                <th>Phone</th>
                                <th>Email</th>
                                <th>City</th>
                                <th>Type</th>
                                <th>Status</th>
                                <th>Actions</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse($branches ?? [] as $branch)
                            <tr>
                                <td>{{ $branch->name }}</td>
                                <td>{{ $branch->phone_number }}</td>
                                <td>{{ $branch->email ?? 'N/A' }}</td>
                                <td>{{ $branch->city }}</td>
                                <td>
                                    @if($branch->is_main_branch)
                                        <span class="badge badge-primary">Main Branch</span>
                                    @else
                                        <span class="badge badge-secondary">Branch</span>
                                    @endif
                                </td>
                                <td>
                                    @if($branch->is_active)
                                        <span class="badge badge-success">Active</span>
                                    @else
                                        <span class="badge badge-danger">Inactive</span>
                                    @endif
                                </td>
                                <td>
                                    <button class="btn btn-sm btn-info" onclick="viewBranch({{ $branch->id }})">
                                        <i class="fas fa-eye"></i>
                                    </button>
                                    <button class="btn btn-sm btn-warning" onclick="editBranch({{ $branch->id }})">
                                        <i class="fas fa-edit"></i>
                                    </button>
                                    @if(!$branch->is_main_branch)
                                    <button class="btn btn-sm btn-danger" onclick="deleteBranch({{ $branch->id }})">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                    @endif
                                </td>
                            </tr>
                            @empty
                            <tr>
                                <td colspan="7" class="text-center">No branches found</td>
                            </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Add Branch Modal -->
<div class="modal fade" id="addBranchModal" tabindex="-1" role="dialog" aria-labelledby="addBranchModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="addBranchModalLabel">Add New Branch</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="addBranchForm" action="{{ route('ui.branches.store') }}" method="POST">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="name">Branch Name *</label>
                        <input type="text" class="form-control" id="name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="phone_number">Phone Number *</label>
                        <input type="text" class="form-control" id="phone_number" name="phone_number" required>
                    </div>
                    <div class="form-group">
                        <label for="email">Email</label>
                        <input type="email" class="form-control" id="email" name="email">
                    </div>
                    <div class="form-group">
                        <label for="address">Address *</label>
                        <textarea class="form-control" id="address" name="address" rows="3" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="city">City *</label>
                        <input type="text" class="form-control" id="city" name="city" required>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="is_main_branch" name="is_main_branch">
                        <label class="form-check-label" for="is_main_branch">
                            Set as Main Branch
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Add Branch</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Edit Branch Modal -->
<div class="modal fade" id="editBranchModal" tabindex="-1" role="dialog" aria-labelledby="editBranchModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="editBranchModalLabel">Edit Branch</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form id="editBranchForm" method="POST">
                @csrf
                @method('PUT')
                <div class="modal-body">
                    <div class="form-group">
                        <label for="edit_name">Branch Name *</label>
                        <input type="text" class="form-control" id="edit_name" name="name" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_phone_number">Phone Number *</label>
                        <input type="text" class="form-control" id="edit_phone_number" name="phone_number" required>
                    </div>
                    <div class="form-group">
                        <label for="edit_email">Email</label>
                        <input type="email" class="form-control" id="edit_email" name="email">
                    </div>
                    <div class="form-group">
                        <label for="edit_address">Address *</label>
                        <textarea class="form-control" id="edit_address" name="address" rows="3" required></textarea>
                    </div>
                    <div class="form-group">
                        <label for="edit_city">City *</label>
                        <input type="text" class="form-control" id="edit_city" name="city" required>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="edit_is_main_branch" name="is_main_branch">
                        <label class="form-check-label" for="edit_is_main_branch">
                            Set as Main Branch
                        </label>
                    </div>
                    <div class="form-check">
                        <input type="checkbox" class="form-check-input" id="edit_is_active" name="is_active">
                        <label class="form-check-label" for="edit_is_active">
                            Active
                        </label>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-primary">Update Branch</button>
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
    $('#branchesTable').DataTable();
});

function editBranch(branchId) {
    // Fetch branch data and populate modal
    fetch(`/ui/branches/${branchId}/edit`)
        .then(response => response.json())
        .then(data => {
            $('#edit_name').val(data.name);
            $('#edit_phone_number').val(data.phone_number);
            $('#edit_email').val(data.email);
            $('#edit_address').val(data.address);
            $('#edit_city').val(data.city);
            $('#edit_is_main_branch').prop('checked', data.is_main_branch);
            $('#edit_is_active').prop('checked', data.is_active);
            
            $('#editBranchForm').attr('action', `/ui/branches/${branchId}`);
            $('#editBranchModal').modal('show');
        });
}

function deleteBranch(branchId) {
    if (confirm('Are you sure you want to delete this branch?')) {
        fetch(`/ui/branches/${branchId}`, {
            method: 'DELETE',
            headers: {
                'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').getAttribute('content')
            }
        }).then(() => {
            location.reload();
        });
    }
}

function viewBranch(branchId) {
    window.location.href = `/ui/branches/${branchId}`;
}
</script>
@endsection
