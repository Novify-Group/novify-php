@extends('templates.base')

@section('title', 'Bill Payments - Novify')

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Bill Payments</h1>
    <div>
        <a href="{{ route('ui.bills.create') }}" class="btn btn-primary">
            <i class="fas fa-plus fa-sm"></i> Pay Bill
        </a>
        <a href="{{ route('ui.bills.categories') }}" class="btn btn-info">
            <i class="fas fa-list fa-sm"></i> View Categories
        </a>
    </div>
</div>

<!-- Bill Categories Overview -->
<div class="row">
    @foreach($categories as $category)
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            {{ $category->name }}</div>
                        <div class="h6 mb-0 font-weight-bold text-gray-800">
                            {{ $category->billers_count ?? 0 }} Billers
                        </div>
                        <small class="text-muted">{{ $category->description ?? 'No description' }}</small>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-{{ $category->icon ?? 'file-invoice' }} fa-2x text-gray-300"></i>
                    </div>
                </div>
                <div class="mt-2">
                    <a href="{{ route('ui.bills.show-billers', $category) }}" class="btn btn-sm btn-outline-primary">
                        View Billers
                    </a>
                </div>
            </div>
        </div>
    </div>
    @endforeach
</div>

<!-- Recent Bills -->
<div class="row">
    <div class="col-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Recent Bill Payments</h6>
            </div>
            <div class="card-body">
                @if($recentBills->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Bill ID</th>
                                    <th>Category</th>
                                    <th>Biller</th>
                                    <th>Account</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentBills as $bill)
                                <tr>
                                    <td>{{ $bill->id }}</td>
                                    <td>{{ $bill->category->name ?? 'N/A' }}</td>
                                    <td>{{ $bill->biller->name ?? 'N/A' }}</td>
                                    <td>{{ $bill->account_number }}</td>
                                    <td>${{ number_format($bill->amount, 2) }}</td>
                                    <td>
                                        <span class="badge badge-{{ $bill->status === 'completed' ? 'success' : ($bill->status === 'pending' ? 'warning' : 'danger') }}">
                                            {{ ucfirst($bill->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $bill->created_at->format('M d, Y H:i') }}</td>
                                    <td>
                                        <a href="{{ route('ui.bills.show', $bill) }}" class="btn btn-sm btn-info">
                                            <i class="fas fa-eye"></i>
                                        </a>
                                        @if($bill->status === 'pending')
                                        <a href="{{ route('ui.bills.edit', $bill) }}" class="btn btn-sm btn-warning">
                                            <i class="fas fa-edit"></i>
                                        </a>
                                        @endif
                                    </td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-file-invoice fa-3x text-gray-300 mb-3"></i>
                        <p class="text-gray-500">No bill payments yet</p>
                        <small class="text-muted">Your bill payment history will appear here</small>
                        <br>
                        <a href="{{ route('ui.bills.create') }}" class="btn btn-primary mt-2">
                            Make Your First Payment
                        </a>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection
