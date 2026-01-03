@extends('admin.layouts.app')

@section('title', 'Order Details')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-file-invoice"></i> Order #{{ $order->id }}</h2>
    <a href="{{ route('admin.orders.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back to Orders
    </a>
</div>

<div class="row">
    <!-- Customer Information -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header bg-primary text-white">
                <h5 class="mb-0"><i class="fas fa-user"></i> Customer Information</h5>
            </div>
            <div class="card-body">
                <p><strong>Name:</strong> {{ $order->customer_name }}</p>
                <p><strong>Email:</strong> {{ $order->customer_email }}</p>
                <p><strong>Phone:</strong> {{ $order->customer_phone }}</p>
                <p><strong>Address:</strong><br>{{ $order->customer_address }}</p>
            </div>
        </div>
    </div>

    <!-- Order Information -->
    <div class="col-md-6 mb-4">
        <div class="card">
            <div class="card-header bg-success text-white">
                <h5 class="mb-0"><i class="fas fa-info-circle"></i> Order Information</h5>
            </div>
            <div class="card-body">
                <p><strong>Order ID:</strong> #{{ $order->id }}</p>
                <p><strong>Order Date:</strong> {{ $order->created_at->format('d M, Y h:i A') }}</p>
                <p><strong>Total Amount:</strong> <span class="fs-4 text-success">₹{{ number_format($order->total_amount, 2) }}</span></p>
                <p>
                    <strong>Status:</strong>
                    <select class="form-select form-select-sm d-inline-block w-auto" id="status-select">
                        <option value="pending" {{ $order->status == 'pending' ? 'selected' : '' }}>Pending</option>
                        <option value="processing" {{ $order->status == 'processing' ? 'selected' : '' }}>Processing</option>
                        <option value="completed" {{ $order->status == 'completed' ? 'selected' : '' }}>Completed</option>
                        <option value="cancelled" {{ $order->status == 'cancelled' ? 'selected' : '' }}>Cancelled</option>
                    </select>
                </p>
            </div>
        </div>
    </div>
</div>

<!-- Order Items -->
<div class="card">
    <div class="card-header bg-info text-white">
        <h5 class="mb-0"><i class="fas fa-shopping-bag"></i> Order Items</h5>
    </div>
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered">
                <thead class="table-light">
                    <tr>
                        <th>Product</th>
                        <th>Image</th>
                        <th>Price</th>
                        <th>Quantity</th>
                        <th>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    @foreach($order->orderItems as $item)
                        <tr>
                            <td>
                                <strong>{{ $item->product->name }}</strong><br>
                                <small class="text-muted">Category: {{ $item->product->category->name }}</small>
                            </td>
                            <td>
                                @if($item->product->image)
                                    <img src="{{ asset('storage/'. $item->product->image) }}" alt="{{ $item->product->name }}" style="width: 60px; height: 60px; object-fit: cover; border-radius: 5px;">
                                @else
                                    <div style="width: 60px; height: 60px; background: #ddd; border-radius: 5px;"></div>
                                @endif
                            </td>
                            <td>₹{{ number_format($item->price, 2) }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td><strong>₹{{ number_format($item->price * $item->quantity, 2) }}</strong></td>
                        </tr>
                    @endforeach
                </tbody>
                <tfoot class="table-light">
                    <tr>
                        <th colspan="4" class="text-end">Total:</th>
                        <th class="fs-5 text-success">₹{{ number_format($order->total_amount, 2) }}</th>
                    </tr>
                </tfoot>
            </table>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $('#status-select').change(function() {
        const newStatus = $(this).val();
        
        if(!confirm('Change order status to ' + newStatus + '?')) {
            $(this).val('{{ $order->status }}');
            return;
        }

        $.post('{{ route("admin.orders.updateStatus", $order->id) }}', {
            status: newStatus
        }, function(response) {
            if(response.success) {
                alert(response.message);
                location.reload();
            }
        }).fail(function() {
            alert('Failed to update order status');
            $('#status-select').val('{{ $order->status }}');
        });
    });
</script>
@endpush
@endsection