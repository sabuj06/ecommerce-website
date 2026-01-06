@extends('layouts.app')

@section('title', 'Shop - All Products')

@section('content')

<style>
    /* Sidebar Sticky CSS */
    .sidebar-fixed {
        position: sticky;
        top: 80px;
    }

    @media (max-width: 767px) {
        .sidebar-fixed {
            position: static;
        }
    }

    /* Product Card Image Styling */
    .product-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }

    .product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1);
    }

    .product-image-container {
        position: relative;
        width: 100%;
        height: 250px;
        overflow: hidden;
        background: #f8f9fa;
    }

    .product-image {
        width: 100%;
        height: 100%;
        object-fit: cover;
        object-position: center;
        transition: transform 0.3s ease;
    }

    .product-card:hover .product-image {
        transform: scale(1.05);
    }
</style>

<div class="container">
    <div class="row">
        
        <!-- Sidebar - Categories & Brands -->
        <div class="col-md-3">
            <!-- Categories Card -->
            <div class="card sidebar-fixed mb-3">
                <div class="card-header bg-primary text-white">
                    <h5 class="mb-0"><i class="fas fa-list"></i> Categories</h5>
                </div>
                <div class="list-group list-group-flush">
                    <a href="{{ route('shop.index') }}" class="list-group-item list-group-item-action active">
                        <i class="fas fa-home"></i> All Products
                    </a>
                    @foreach($categories as $category)
                        <a href="{{ route('shop.category', $category->slug) }}" 
                           class="list-group-item list-group-item-action">
                            {{ $category->name }}
                        </a>
                    @endforeach
                </div>
            </div>

            <!-- Brands Card with Collapse -->
            <div class="card">
                <div class="card-header bg-info text-white" 
                     style="cursor: pointer;" 
                     data-bs-toggle="collapse" 
                     data-bs-target="#brandsCollapse">
                    <div class="d-flex justify-content-between align-items-center">
                        <h5 class="mb-0"><i class="fas fa-tags"></i> Brands</h5>
                        <i class="fas fa-chevron-down"></i>
                    </div>
                </div>
                <div class="collapse" id="brandsCollapse">
                    <div class="list-group list-group-flush">
                        @php
                            $brands = \App\Models\Brand::where('is_active', true)->get();
                        @endphp
                        
                        @forelse($brands as $brand)
                            <a href="{{ route('shop.brand', $brand->slug) }}" 
                               class="list-group-item list-group-item-action">
                                <i class="fas fa-tag"></i> {{ $brand->name }}
                            </a>
                        @empty
                            <div class="list-group-item text-muted">
                                <i class="fas fa-info-circle"></i> No brands available
                            </div>
                        @endforelse
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content - Products -->
        <div class="col-md-9">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-shopping-bag"></i> All Products</h2>
                <span class="badge bg-primary">{{ $products->total() }} Products</span>
            </div>

            <div class="row">
                @forelse($products as $product)
                    <div class="col-md-4 mb-4">
                        <div class="card product-card h-100">

                            <!-- Product Image with Container -->
                            <div class="product-image-container">
                                @if($product->image)
                                    <img src="{{ asset('storage/' . $product->image) }}"
                                         class="product-image"
                                         alt="{{ $product->name }}">
                                @else
                                    <img src="https://via.placeholder.com/400x400/007bff/ffffff?text={{ urlencode($product->name) }}"
                                         class="product-image"
                                         alt="{{ $product->name }}">
                                @endif
                            </div>

                            <!-- Product Details -->
                            <div class="card-body">
                                <h5 class="card-title">{{ $product->name }}</h5>
                                <p class="card-text text-muted">
                                    {{ Str::limit($product->description, 60) }}
                                </p>

                                <div class="d-flex justify-content-between align-items-center">
                                    <h4 class="text-primary mb-0">
                                        ₹{{ number_format($product->price, 2) }}
                                    </h4>
                                    <span class="badge bg-secondary">
                                        Stock: {{ $product->stock }}
                                    </span>
                                </div>
                            </div>

                            <!-- Product Actions -->
                            <div class="card-footer bg-white">
                                <div class="d-grid gap-2">
                                    <a href="{{ route('shop.product', $product->slug) }}"
                                       class="btn btn-outline-primary btn-sm">
                                        View Details
                                    </a>

                                    <button class="btn btn-primary btn-sm add-to-cart"
                                            data-product-id="{{ $product->id }}">
                                        <i class="fas fa-cart-plus"></i> Add to Cart
                                    </button>
                                </div>
                            </div>

                        </div>
                    </div>
                @empty
                    <div class="col-12">
                        <div class="alert alert-info">
                            <i class="fas fa-info-circle"></i> No products found.
                        </div>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                {{ $products->links() }}
            </div>
        </div>

    </div>
</div>

@push('scripts')
<script>
    $(document).on('click', '.add-to-cart', function () {
        const btn = $(this);
        const productId = btn.data('product-id');

        btn.prop('disabled', true)
           .html('<i class="fas fa-spinner fa-spin"></i> Adding...');

        $.post('{{ route("cart.add") }}', {
            product_id: productId,
            quantity: 1
        }, function (response) {
            if (response.success) {
                $('#cart-count').text(response.cartCount);
                btn.removeClass('btn-primary')
                   .addClass('btn-success')
                   .html('<i class="fas fa-check"></i> Added');

                setTimeout(function () {
                    btn.prop('disabled', false)
                       .removeClass('btn-success')
                       .addClass('btn-primary')
                       .html('<i class="fas fa-cart-plus"></i> Add to Cart');
                }, 2000);
            }
        }).fail(function () {
            alert('Failed to add product to cart');
            btn.prop('disabled', false)
               .html('<i class="fas fa-cart-plus"></i> Add to Cart');
        });
    });
</script>
@endpush

@endsection