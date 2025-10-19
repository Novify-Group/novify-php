@extends('templates.base')

@section('title', 'Wallet - Novify')

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">Wallet</h1>
    <div>
        <button type="button" class="btn btn-success" data-toggle="modal" data-target="#topupModal">
            <i class="fas fa-plus fa-sm"></i> Top Up
        </button>
        <button type="button" class="btn btn-info" data-toggle="modal" data-target="#transferModal">
            <i class="fas fa-exchange-alt fa-sm"></i> Transfer
        </button>
    </div>
</div>

<!-- Wallet Overview -->
<div class="row">
    <!-- Balance Card -->
    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Current Balance</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">${{ number_format($wallet->balance, 2) }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-wallet fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Currency Card -->
    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Currency</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $wallet->currency }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-dollar-sign fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Status Card -->
    <div class="col-xl-4 col-md-6 mb-4">
        <div class="card border-left-{{ $wallet->is_active ? 'success' : 'danger' }} shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-{{ $wallet->is_active ? 'success' : 'danger' }} text-uppercase mb-1">
                            Status</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ $wallet->is_active ? 'Active' : 'Inactive' }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-{{ $wallet->is_active ? 'check-circle' : 'times-circle' }} fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent Transactions -->
<div class="row">
    <div class="col-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Recent Transactions</h6>
                <div class="float-right">
                    <a href="{{ route('ui.wallet.transactions') }}" class="btn btn-sm btn-outline-primary">
                        View All
                    </a>
                </div>
            </div>
            <div class="card-body">
                @if($recentTransactions->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Type</th>
                                    <th>Amount</th>
                                    <th>Description</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentTransactions as $transaction)
                                <tr>
                                    <td>
                                        <span class="badge badge-{{ $transaction->type === 'topup' ? 'success' : ($transaction->type === 'transfer' ? 'info' : 'warning') }}">
                                            {{ ucfirst($transaction->type) }}
                                        </span>
                                    </td>
                                    <td class="text-{{ $transaction->amount >= 0 ? 'success' : 'danger' }}">
                                        {{ $transaction->amount >= 0 ? '+' : '' }}${{ number_format(abs($transaction->amount), 2) }}
                                    </td>
                                    <td>{{ $transaction->description }}</td>
                                    <td>
                                        <span class="badge badge-{{ $transaction->status === 'completed' ? 'success' : ($transaction->status === 'pending' ? 'warning' : 'danger') }}">
                                            {{ ucfirst($transaction->status) }}
                                        </span>
                                    </td>
                                    <td>{{ $transaction->created_at->format('M d, Y H:i') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-receipt fa-3x text-gray-300 mb-3"></i>
                        <p class="text-gray-500">No transactions yet</p>
                        <small class="text-muted">Your wallet transactions will appear here</small>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- Top Up Modal -->
<div class="modal fade" id="topupModal" tabindex="-1" role="dialog" aria-labelledby="topupModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="topupModalLabel">Top Up Wallet</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST" action="{{ route('ui.wallet.topup') }}">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="amount">Amount</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">$</span>
                            </div>
                            <input type="number" class="form-control" id="amount" name="amount" 
                                   step="0.01" min="1" required placeholder="Enter amount">
                        </div>
                    </div>
                    <div class="form-group">
                        <label for="payment_method">Payment Method</label>
                        <select class="form-control" id="payment_method" name="payment_method" required>
                            <option value="">Select payment method</option>
                            <option value="credit_card">Credit Card</option>
                            <option value="bank_transfer">Bank Transfer</option>
                            <option value="mobile_money">Mobile Money</option>
                        </select>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-success">Top Up</button>
                </div>
            </form>
        </div>
    </div>
</div>

<!-- Transfer Modal -->
<div class="modal fade" id="transferModal" tabindex="-1" role="dialog" aria-labelledby="transferModalLabel" aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="transferModalLabel">Transfer Funds</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <form method="POST" action="{{ route('ui.wallet.transfer') }}">
                @csrf
                <div class="modal-body">
                    <div class="form-group">
                        <label for="recipient_id">Recipient Merchant</label>
                        <select class="form-control" id="recipient_id" name="recipient_id" required>
                            <option value="">Select recipient</option>
                            <!-- This would be populated with available merchants -->
                        </select>
                    </div>
                    <div class="form-group">
                        <label for="transfer_amount">Amount</label>
                        <div class="input-group">
                            <div class="input-group-prepend">
                                <span class="input-group-text">$</span>
                            </div>
                            <input type="number" class="form-control" id="transfer_amount" name="amount" 
                                   step="0.01" min="1" max="{{ $wallet->balance }}" required placeholder="Enter amount">
                        </div>
                        <small class="form-text text-muted">Available balance: ${{ number_format($wallet->balance, 2) }}</small>
                    </div>
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea class="form-control" id="description" name="description" rows="3" 
                                  placeholder="Optional transfer description"></textarea>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancel</button>
                    <button type="submit" class="btn btn-info">Transfer</button>
                </div>
            </form>
        </div>
    </div>
</div>
@endsection
