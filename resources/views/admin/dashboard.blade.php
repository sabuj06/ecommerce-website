@extends('admin.layouts.app')

@section('title', 'Admin Dashboard')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-dashboard"></i> Dashboard</h2>
    <div>
        <span class="text-muted">{{ now()->format('l, F d, Y') }}</span>
    </div>
</div>

<!-- Statistics Cards -->
<div class="row mb-4">
    <div class="col-md-3 mb-3">
        <div class="card stat-card border-primary">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="text-muted">Total Products</h6>
                        <h2 class="text-primary">{{ $stats['total_products'] }}</h2>
                    </div>
                    <div>
                        <i class="fas fa-box fa-3x text-primary opacity-25"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="card stat-card border-success">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="text-muted">Categories</h6>
                        <h2 class="text-success">{{ $stats['total_categories'] }}</h2>
                    </div>
                    <div>
                        <i class="fas fa-folder fa-3x text-success opacity-25"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    

    <div class="col-md-3 mb-3">
        <div class="card stat-card border-warning">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="text-muted">Total Orders</h6>
                        <h2 class="text-warning">{{ $stats['total_orders'] }}</h2>
                    </div>
                    <div>
                        <i class="fas fa-shopping-cart fa-3x text-warning opacity-25"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="col-md-3 mb-3">
        <div class="card stat-card border-info">
            <div class="card-body">
                <div class="d-flex justify-content-between">
                    <div>
                        <h6 class="text-muted">Pending Orders</h6>
                        <h2 class="text-info">{{ $stats['pending_orders'] }}</h2>
                    </div>
                    <div>
                        <i class="fas fa-clock fa-3x text-info opacity-25"></i>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- Revenue Card -->
<div class="row mb-4">
    <div class="col-md-12">
        <div class="card">
            <div class="card-body text-center py-4">
                <h6 class="text-muted">Total Revenue</h6>
                <h1 class="text-success">₹{{ number_format($stats['total_revenue'], 2) }}</h1>
            </div>
        </div>
    </div>
</div>

<!-- Recent Orders -->
<div class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-white">
                <h5 class="mb-0"><i class="fas fa-list"></i> Recent Orders</h5>
            </div>
            <div class="card-body">
                @if($recent_orders->count() > 0)
                    <div class="table-responsive">
                        <table class="table table-hover">
                            <thead>
                                <tr>
                                    <th>Order ID</th>
                                    <th>Customer</th>
                                    <th>Amount</th>
                                    <th>Status</th>
                                    <th>Date</th>
                                    <th>Action</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($recent_orders as $order)
                                    <tr>
                                        <td>#{{ $order->id }}</td>
                                        <td>{{ $order->customer_name }}</td>
                                        <td>₹{{ number_format($order->total_amount, 2) }}</td>
                                        <td>
                                            <span class="badge bg-{{ $order->status == 'pending' ? 'warning' : ($order->status == 'completed' ? 'success' : 'info') }}">
                                                {{ ucfirst($order->status) }}
                                            </span>
                                        </td>
                                        <td>{{ $order->created_at->format('d M, Y') }}</td>
                                        <td>
                                            <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-sm btn-primary">
                                                <i class="fas fa-eye"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                @else
                    <div class="alert alert-info">No orders yet.</div>
                @endif
            </div>
        </div>
    </div>
</div>
@endsection