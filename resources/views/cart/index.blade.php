@extends('layouts.app')

@section('title', 'Shopping Cart')

@section('content')
<div class="container">
    <h2 class="mb-4">Shopping Cart</h2>

    @if($cartItems->isEmpty())
        <div class="alert alert-info">
            Your cart is empty. <a href="{{ route('shop.index') }}">Continue shopping</a>
        </div>
    @else
        <div class="row">
            <!-- Cart Items -->
            <div class="col-md-8">
                <div class="card">
                    <div class="card-body">
                        @foreach($cartItems as $item)
                            <div class="row mb-3 pb-3 border-bottom cart-item" data-cart-id="{{ $item->id }}">
                                <!-- Product Image -->
                                <div class="col-md-2">
                                    @if($item->product->image)
                                        <img src="{{ asset('storage/' . $item->product->image) }}" class="img-fluid rounded" alt="{{ $item->product->name }}">
                                    @else
                                        <img src="https://via.placeholder.com/100?text=Product" class="img-fluid rounded" alt="{{ $item->product->name }}">
                                    @endif
                                </div>
                                
                                <!-- Product Info -->
                                <div class="col-md-4">
                                    <h5>{{ $item->product->name }}</h5>
                                    <p class="text-muted">৳{{ number_format($item->product->price, 2) }}</p>
                                </div>
                                
                                <!-- Quantity -->
                                <div class="col-md-3">
                                    <label class="form-label">Quantity</label>
                                    <input type="number" class="form-control quantity-input" 
                                           value="{{ $item->quantity }}" 
                                           min="1" 
                                           max="{{ $item->product->stock }}" 
                                           data-cart-id="{{ $item->id }}">
                                </div>
                                
                                <!-- Subtotal -->
                                <div class="col-md-2">
                                    <label class="form-label">Subtotal</label>
                                    <h5 class="item-subtotal">৳{{ number_format($item->product->price * $item->quantity, 2) }}</h5>
                                </div>
                                
                                <!-- Remove Button -->
                                <div class="col-md-1">
                                    <button class="btn btn-danger btn-sm remove-item" data-cart-id="{{ $item->id }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </div>
                            </div>
                        @endforeach
                    </div>
                </div>
            </div>

            <!-- Order Summary -->
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0">Order Summary</h5>
                    </div>
                    <div class="card-body">
                        <div class="d-flex justify-content-between mb-2">
                            <span>Subtotal:</span>
                            <strong id="cart-total">৳{{ number_format($total, 2) }}</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-3 pb-3 border-bottom">
                            <span>Shipping:</span>
                            <strong>Free</strong>
                        </div>
                        <div class="d-flex justify-content-between mb-3">
                            <h5>Total:</h5>
                            <h5 class="text-primary" id="final-total">৳{{ number_format($total, 2) }}</h5>
                        </div>
                        <a href="{{ route('order.checkout') }}" class="btn btn-primary btn-lg w-100">
                            Proceed to Checkout
                        </a>
                        <a href="{{ route('shop.index') }}" class="btn btn-outline-secondary w-100 mt-2">
                            Continue Shopping
                        </a>
                    </div>
                </div>
            </div>
        </div>
    @endif
</div>

@push('scripts')
<script>
    // Update quantity
    $('.quantity-input').change(function() {
        const input = $(this);
        const cartId = input.data('cart-id');
        const quantity = parseInt(input.val());

        $.post('{{ route("cart.update") }}', {
            cart_id: cartId,
            quantity: quantity
        }, function(response) {
            if(response.success) {
                const item = input.closest('.cart-item');
                const price = parseFloat(item.find('.text-muted').text().replace('৳', '').replace(',', ''));
                const subtotal = price * quantity;
                item.find('.item-subtotal').text('৳' + subtotal.toFixed(2));
                
                $('#cart-total').text('৳' + response.total);
                $('#final-total').text('৳' + response.total);
            }
        });
    });

    // Remove item
    $('.remove-item').click(function() {
        if(!confirm('Remove this item from cart?')) return;

        const btn = $(this);
        const cartId = btn.data('cart-id');

        $.post('{{ route("cart.remove") }}', {
            cart_id: cartId
        }, function(response) {
            if(response.success) {
                btn.closest('.cart-item').fadeOut(function() {
                    $(this).remove();
                    
                    $('#cart-count').text(response.cartCount);
                    $('#cart-total').text('৳' + response.total);
                    $('#final-total').text('৳' + response.total);

                    if(response.cartCount === 0) {
                        location.reload();
                    }
                });
            }
        });
    });
</script>
@endpush
@endsection