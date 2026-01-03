@extends('admin.layouts.app')

@section('title', 'Manage Orders')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-shopping-cart"></i> Manage Orders</h2>
</div>

<div class="card">
    <div class="card-body">
        @if($orders->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>Order ID</th>
                            <th>Customer</th>
                            <th>Email</th>
                            <th>Phone</th>
                            <th>Items</th>
                            <th>Total Amount</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($orders as $order)
                            <tr>
                                <td><strong>#{{ $order->id }}</strong></td>
                                <td>{{ $order->customer_name }}</td>
                                <td>{{ $order->customer_email }}</td>
                                <td>{{ $order->customer_phone }}</td>
                                <td>
                                    <span class="badge bg-info">{{ $order->orderItems->count() }} items</span>
                                </td>
                                <td><strong>₹{{ number_format($order->total_amount, 2) }}</strong></td>
                                <td>
                                    <select class="form-select form-select-sm status-select" data-order-id="{{ $order->id }}">
                                        <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Pending</option>
                                        <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>Processing</option>
                                        <option value="completed" {{ $order->status == 'completed' ? 'selected' : '' }}>Completed</option>
                                        <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                                    </select>
                                </td>
                                <td>{{ $order->created_at->format('d M, Y h:i A') }}</td>
                                <td>
                                    <a href="{{ route('admin.orders.show', $order->id) }}" class="btn btn-sm btn-primary">
                                        <i class="fas fa-eye"></i> View
                                    </a>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $orders->links() }}
            </div>
        @else
            <div class="alert alert-info">
                No orders found yet.
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    $('.status-select').change(function() {
        const select = $(this);
        const orderId = select.data('order-id');
        const newStatus = select.val();
        
        if(!confirm('Change order status to ' + newStatus + '?')) {
            select.val(select.data('original-status'));
            return;
        }

        $.post('/admin/orders/' + orderId + '/status', {
            status: newStatus
        }, function(response) {
            if(response.success) {
                alert(response.message);
                select.data('original-status', newStatus);
                
                // Update badge color
                const row = select.closest('tr');
                let badgeClass = 'bg-warning';
                if(newStatus === 'completed') badgeClass = 'bg-success';
                else if(newStatus === 'processing') badgeClass = 'bg-info';
                else if(newStatus === 'cancelled') badgeClass = 'bg-danger';
            }
        }).fail(function() {
            alert('Failed to update order status');
            select.val(select.data('original-status'));
        });
    });

    // Store original status
    $('.status-select').each(function() {
        $(this).data('original-status', $(this).val());
    });
</script>
@endpush
@endsection