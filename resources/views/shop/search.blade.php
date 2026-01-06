
@extends('layouts.app')

@section('title', 'Search Results')

@section('content')
<div class="container">
    <h2 class="mb-4">Search Results for "{{ $query }}"</h2>
    <p class="text-muted">Found {{ $products->total() }} products</p>

    <div class="row">
        @forelse($products as $product)
            <div class="col-md-3 mb-4">
                <div class="card product-card h-100">
                    <!-- Product Image -->
                    @if($product->image)
                        <img src="{{ asset('storage/' . $product->image) }}" class="card-img-top" alt="{{ $product->name }}" style="height: 200px; object-fit: cover;">
                    @else
                        <img src="https://via.placeholder.com/300x200?text={{ urlencode($product->name) }}" class="card-img-top" alt="{{ $product->name }}">
                    @endif
                    
                    <!-- Product Details -->
                    <div class="card-body">
                        <h5 class="card-title">{{ $product->name }}</h5>
                        <p class="card-text text-muted">{{ Str::limit($product->description, 60) }}</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <h4 class="text-primary mb-0">৳{{ number_format($product->price, 2) }}</h4>
                            <span class="badge bg-secondary">Stock: {{ $product->stock }}</span>
                        </div>
                    </div>
                    
                    <!-- Product Actions -->
                    <div class="card-footer bg-white">
                        <div class="d-grid gap-2">
                            <a href="{{ route('shop.product', $product->slug) }}" class="btn btn-outline-primary btn-sm">
                                View Details
                            </a>
                            <button class="btn btn-primary btn-sm add-to-cart" data-product-id="{{ $product->id }}">
                                <i class="fas fa-cart-plus"></i> Add to Cart
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        @empty
            <div class="col-12">
                <div class="alert alert-warning">
                    No products found matching your search.
                    <a href="{{ route('shop.index') }}" class="alert-link">Browse all products</a>
                </div>
            </div>
        @endforelse
    </div>

    <!-- Pagination with Search Query -->
    <div class="d-flex justify-content-center mt-4">
        {{ $products->appends(['q' => $query])->links() }}
    </div>
</div>

@push('scripts')
<script>
    $(document).on('click', '.add-to-cart', function() {
        const btn = $(this);
        const productId = btn.data('product-id');
        
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Adding...');

        $.post('{{ route("cart.add") }}', {
            product_id: productId,
            quantity: 1
        }, function(response) {
            if(response.success) {
                $('#cart-count').text(response.cartCount);
                btn.removeClass('btn-primary').addClass('btn-success').html('<i class="fas fa-check"></i> Added');
                
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