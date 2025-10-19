@extends('templates.base')

@section('title', 'System Health - Admin')

@section('content')
<!-- Page Heading -->
<div class="d-sm-flex align-items-center justify-content-between mb-4">
    <h1 class="h3 mb-0 text-gray-800">System Health</h1>
    <div>
        <a href="{{ route('ui.admin.dashboard') }}" class="btn btn-secondary">
            <i class="fas fa-arrow-left fa-sm"></i> Back to Dashboard
        </a>
    </div>
</div>

<!-- System Status Cards -->
<div class="row">
    <!-- Database Status -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-{{ $systemStats['database_status'] === 'healthy' ? 'success' : 'danger' }} shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-{{ $systemStats['database_status'] === 'healthy' ? 'success' : 'danger' }} text-uppercase mb-1">
                            Database Status</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">
                            {{ ucfirst($systemStats['database_status']) }}
                        </div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-database fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Users -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-primary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-primary text-uppercase mb-1">
                            Total Users</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $systemStats['total_users'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-users fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Merchants -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-success shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-success text-uppercase mb-1">
                            Total Merchants</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $systemStats['total_merchants'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-store fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Total Orders -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            Total Orders</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $systemStats['total_orders'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-shopping-cart fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Additional System Stats -->
<div class="row">
    <!-- Total Products -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-warning shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-warning text-uppercase mb-1">
                            Total Products</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ $systemStats['total_products'] }}</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-box fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Disk Usage -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-secondary shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-secondary text-uppercase mb-1">
                            Disk Usage</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($systemStats['disk_usage'], 1) }}%</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-hdd fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Memory Usage -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-dark shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-dark text-uppercase mb-1">
                            Memory Usage</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">{{ number_format($systemStats['memory_usage'], 1) }} MB</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-memory fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- System Uptime -->
    <div class="col-xl-3 col-md-6 mb-4">
        <div class="card border-left-info shadow h-100 py-2">
            <div class="card-body">
                <div class="row no-gutters align-items-center">
                    <div class="col mr-2">
                        <div class="text-xs font-weight-bold text-info text-uppercase mb-1">
                            System Uptime</div>
                        <div class="h5 mb-0 font-weight-bold text-gray-800">Active</div>
                    </div>
                    <div class="col-auto">
                        <i class="fas fa-clock fa-2x text-gray-300"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- System Health Details -->
<div class="row">
    <!-- Performance Metrics -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Performance Metrics</h6>
            </div>
            <div class="card-body">
                <div class="mb-3">
                    <div class="d-flex justify-content-between">
                        <span>Database Response Time</span>
                        <span class="text-success">Fast</span>
                    </div>
                    <div class="progress mt-1">
                        <div class="progress-bar bg-success" style="width: 85%"></div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <div class="d-flex justify-content-between">
                        <span>API Response Time</span>
                        <span class="text-success">Optimal</span>
                    </div>
                    <div class="progress mt-1">
                        <div class="progress-bar bg-success" style="width: 90%"></div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <div class="d-flex justify-content-between">
                        <span>Cache Hit Rate</span>
                        <span class="text-warning">Good</span>
                    </div>
                    <div class="progress mt-1">
                        <div class="progress-bar bg-warning" style="width: 75%"></div>
                    </div>
                </div>
                
                <div class="mb-3">
                    <div class="d-flex justify-content-between">
                        <span>Queue Processing</span>
                        <span class="text-success">Normal</span>
                    </div>
                    <div class="progress mt-1">
                        <div class="progress-bar bg-success" style="width: 95%"></div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- System Alerts -->
    <div class="col-lg-6 mb-4">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">System Alerts</h6>
            </div>
            <div class="card-body">
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <strong>Database:</strong> Connection healthy
                </div>
                
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <strong>Redis:</strong> Cache service running
                </div>
                
                <div class="alert alert-success">
                    <i class="fas fa-check-circle"></i>
                    <strong>Storage:</strong> File system accessible
                </div>
                
                <div class="alert alert-info">
                    <i class="fas fa-info-circle"></i>
                    <strong>Updates:</strong> System up to date
                </div>
                
                <div class="alert alert-warning">
                    <i class="fas fa-exclamation-triangle"></i>
                    <strong>Backup:</strong> Last backup 2 days ago
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Recent System Activities -->
<div class="row">
    <div class="col-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">Recent System Activities</h6>
            </div>
            <div class="card-body">
                @if(isset($recentActivities) && $recentActivities->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-bordered">
                            <thead>
                                <tr>
                                    <th>Activity</th>
                                    <th>User</th>
                                    <th>IP Address</th>
                                    <th>Timestamp</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recentActivities as $activity)
                                <tr>
                                    <td>{{ $activity->description ?? 'System activity' }}</td>
                                    <td>{{ $activity->causer->name ?? 'System' }}</td>
                                    <td>{{ $activity->properties['ip_address'] ?? 'N/A' }}</td>
                                    <td>{{ $activity->created_at->format('M d, Y H:i:s') }}</td>
                                </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="text-center py-4">
                        <i class="fas fa-info-circle fa-3x text-gray-300 mb-3"></i>
                        <p class="text-gray-500">No recent activities found</p>
                        <small class="text-muted">System activities will appear here</small>
                    </div>
                @endif
            </div>
        </div>
    </div>
</div>

<!-- System Maintenance -->
<div class="row">
    <div class="col-12">
        <div class="card shadow mb-4">
            <div class="card-header py-3">
                <h6 class="m-0 font-weight-bold text-primary">System Maintenance</h6>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-md-4 mb-3">
                        <button class="btn btn-outline-primary btn-block">
                            <i class="fas fa-sync-alt"></i> Clear Cache
                        </button>
                    </div>
                    <div class="col-md-4 mb-3">
                        <button class="btn btn-outline-warning btn-block">
                            <i class="fas fa-database"></i> Optimize Database
                        </button>
                    </div>
                    <div class="col-md-4 mb-3">
                        <button class="btn btn-outline-info btn-block">
                            <i class="fas fa-download"></i> Generate Backup
                        </button>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection
