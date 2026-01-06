@extends('layouts.app')

@section('title', 'Shop - All Products')

@section('content')

<style>
    /* Sidebar Fixed and Scrollable */
    .sidebar-wrapper {
        position: fixed;
        top: 80px; /* navbar height */
        left: 0;
        width: 23%; /* col-md-3 approximate width */
        height: calc(100vh - 80px);
        overflow-y: auto;
        overflow-x: hidden;
        padding-right: 15px;
        padding-left: 15px;
    }
    
    /* Custom Scrollbar */
    .sidebar-wrapper::-webkit-scrollbar {
        width: 6px;
    }
    
    .sidebar-wrapper::-webkit-scrollbar-track {
        background: #f1f1f1;
        border-radius: 10px;
    }
    
    .sidebar-wrapper::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 10px;
    }
    
    .sidebar-wrapper::-webkit-scrollbar-thumb:hover {
        background: #555;
    }
    
    /* Main content offset */
    .main-content-offset {
        margin-left: 25%; /* col-md-3 + gap */
    }

    @media (max-width: 767px) {
        .sidebar-wrapper {
            position: static;
            width: 100%;
            height: auto;
            margin-bottom: 20px;
        }
        
        .main-content-offset {
            margin-left: 0;
        }
    }
    
    /* Brands Header Animation */
    .card-header[data-bs-toggle="collapse"] {
        transition: background-color 0.3s ease;
    }
    
    .card-header[data-bs-toggle="collapse"]:hover {
        background-color: #0d6efd !important;
    }
    
    .card-header[data-bs-toggle="collapse"] i.fa-chevron-down {
        transition: transform 0.3s ease;
    }
    
    .card-header[data-bs-toggle="collapse"]:not(.collapsed) i.fa-chevron-down {
        transform: rotate(180deg);
    }
</style>

<div class="container-fluid">
    <div class="row">
        
        <!-- Fixed Sidebar - Categories & Brands -->
        <div class="col-md-3">
            <div class="sidebar-wrapper">
                <!-- Categories Card -->
                <div class="card mb-3">
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
        </div>

        <!-- Main Content - Products -->
        <div class="col-md-9 main-content-offset">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2><i class="fas fa-shopping-bag"></i> All Products</h2>
                <span class="badge bg-primary">{{ $products->total() }} Products</span>
            </div>

            <div class="row">
                @forelse($products as $product)
                    <div class="col-md-4 mb-4">
                        <div class="card product-card h-100">

                            <!-- Product Image -->
                            @if($product->image)
                                <img src="{{ asset('storage/' . $product->image) }}"
                                     class="card-img-top"
                                     alt="{{ $product->name }}"
                                     style="height:200px;object-fit:cover;">
                            @else
                                <img src="https://via.placeholder.com/300x200?text={{ urlencode($product->name) }}"
                                     class="card-img-top"
                                     alt="{{ $product->name }}">
                            @endif

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