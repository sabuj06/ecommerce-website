@extends('layouts.app')

@section('title', $product->name)

@section('content')
<div class="container">
    <!-- Breadcrumb -->
    <nav aria-label="breadcrumb">
        <ol class="breadcrumb">
            <li class="breadcrumb-item"><a href="{{ route('shop.index') }}">Home</a></li>
            <li class="breadcrumb-item"><a href="{{ route('shop.category', $product->category->slug) }}">{{ $product->category->name }}</a></li>
            <li class="breadcrumb-item active">{{ $product->name }}</li>
        </ol>
    </nav>

    <div class="row">
        <!-- Product Image -->
        <div class="col-md-6">
            @if($product->image)
                <img src="{{ asset('storage/' . $product->image) }}" class="img-fluid rounded" alt="{{ $product->name }}">
            @else
                <img src="https://via.placeholder.com/600x400?text={{ urlencode($product->name) }}" class="img-fluid rounded" alt="{{ $product->name }}">
            @endif
        </div>

        <!-- Product Information -->
        <div class="col-md-6">
            <h1>{{ $product->name }}</h1>
            <p class="text-muted">
                Category: <a href="{{ route('shop.category', $product->category->slug) }}">{{ $product->category->name }}</a>
            </p>
            
            <h3 class="text-primary mb-3">₹{{ number_format($product->price, 2) }}</h3>
            
            <!-- Stock Status -->
            <div class="mb-3">
                <span class="badge bg-{{ $product->stock > 0 ? 'success' : 'danger' }} fs-6">
                    {{ $product->stock > 0 ? 'In Stock (' . $product->stock . ' available)' : 'Out of Stock' }}
                </span>
            </div>

            <!-- Description -->
            <div class="mb-4">
                <h5>Description:</h5>
                <p>{{ $product->description }}</p>
            </div>

            <!-- Add to Cart -->
            @if($product->stock > 0)
                <div class="mb-3">
                    <label class="form-label">Quantity:</label>
                    <input type="number" class="form-control" id="quantity" value="1" min="1" max="{{ $product->stock }}" style="max-width: 100px;">
                </div>

                <button class="btn btn-primary btn-lg" id="add-to-cart-btn" data-product-id="{{ $product->id }}">
                    <i class="fas fa-cart-plus"></i> Add to Cart
                </button>
            @else
                <button class="btn btn-secondary btn-lg" disabled>Out of Stock</button>
            @endif
        </div>
    </div>
</div>

@push('scripts')
<script>
    $('#add-to-cart-btn').click(function() {
        const btn = $(this);
        const productId = btn.data('product-id');
        const quantity = parseInt($('#quantity').val());
        
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Adding...');

        $.post('{{ route("cart.add") }}', {
            product_id: productId,
            quantity: quantity
        }, function(response) {
            if(response.success) {
                $('#cart-count').text(response.cartCount);
                btn.removeClass('btn-primary').addClass('btn-success').html('<i class="fas fa-check"></i> Added to Cart');
                
                setTimeout(function() {
                    btn.prop('disabled', false).removeClass('btn-success').addClass('btn-primary').html('<i class="fas fa-cart-plus"></i> Add to Cart');
                }, 2000);
            }
        }).fail(function() {
            alert('Failed to add product to cart');
            btn.prop('disabled', false).html('<i class="fas fa-cart-plus"></i> Add to Cart');
        });
    });
</script>
@endpush
@endsection