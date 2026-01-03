@extends('layouts.app')

@section('title', 'Checkout')

@section('content')
<div class="container">
    <h2 class="mb-4">Checkout</h2>

    <div class="row">
        <!-- Billing Information Form -->
        <div class="col-md-7">
            <div class="card">
                <div class="card-header">
                    <h5>Billing Information</h5>
                </div>
                <div class="card-body">
                    <form id="checkout-form">
                        <div class="mb-3">
                            <label class="form-label">Full Name *</label>
                            <input type="text" class="form-control" name="customer_name" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Email *</label>
                            <input type="email" class="form-control" name="customer_email" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Phone Number *</label>
                            <input type="tel" class="form-control" name="customer_phone" required>
                        </div>

                        <div class="mb-3">
                            <label class="form-label">Complete Address *</label>
                            <textarea class="form-control" name="customer_address" rows="3" required></textarea>
                        </div>

                        <button type="submit" class="btn btn-primary btn-lg w-100" id="place-order-btn">
                            <i class="fas fa-check"></i> Place Order
                        </button>
                    </form>
                </div>
            </div>
        </div>

        <!-- Order Summary -->
        <div class="col-md-5">
            <div class="card">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0">Order Summary</h5>
                </div>
                <div class="card-body">
                    @foreach($cartItems as $item)
                        <div class="d-flex justify-content-between mb-2">
                            <span>{{ $item->product->name }} × {{ $item->quantity }}</span>
                            <span>₹{{ number_format($item->product->price * $item->quantity, 2) }}</span>
                        </div>
                    @endforeach
                    <hr>
                    <div class="d-flex justify-content-between mb-2">
                        <strong>Subtotal:</strong>
                        <strong>₹{{ number_format($total, 2) }}</strong>
                    </div>
                    <div class="d-flex justify-content-between mb-2">
                        <span>Shipping:</span>
                        <span>Free</span>
                    </div>
                    <hr>
                    <div class="d-flex justify-content-between">
                        <h5>Total:</h5>
                        <h5 class="text-primary">₹{{ number_format($total, 2) }}</h5>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $('#checkout-form').submit(function(e) {
        e.preventDefault();
        
        const btn = $('#place-order-btn');
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Processing...');

        $.post('{{ route("order.store") }}', $(this).serialize(), function(response) {
            if(response.success) {
                window.location.href = '{{ url("/order/success") }}/' + response.order_id;
            } else {
                alert(response.message);
                btn.prop('disabled', false).html('<i class="fas fa-check"></i> Place Order');
            }
        }).fail(function() {
            alert('Order failed. Please try again.');
            btn.prop('disabled', false).html('<i class="fas fa-check"></i> Place Order');
        });
    });
</script>
@endpush
@endsection