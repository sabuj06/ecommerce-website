@extends('layouts.app')
@section('title', $product->name)
@section('content')

<style>
    .related-product-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
        margin: 5px;
        height: 100%;
        display: flex;
        flex-direction: column;
    }
    
    .related-product-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.15);
    }
    
    .related-product-card .card-body {
        flex: 1;
        display: flex;
        flex-direction: column;
    }
    
    .related-product-card .card-body > .d-grid {
        margin-top: auto;
    }
    
    .related-product-image {
        width: 100%;
        height: 200px;
        object-fit: cover;
        object-position: center;
    }

    /* Owl Carousel Custom Styling */
    .owl-carousel .owl-nav button.owl-prev,
    .owl-carousel .owl-nav button.owl-next {
        position: absolute;
        top: 50%;
        transform: translateY(-50%);
        background: rgba(0, 0, 0, 0.5) !important;
        color: white !important;
        font-size: 30px !important;
        width: 50px;
        height: 50px;
        border-radius: 50% !important;
        line-height: 50px !important;
        padding: 0 !important;
    }

    .owl-carousel .owl-nav button.owl-prev {
        left: -70px;
    }

    .owl-carousel .owl-nav button.owl-next {
        right: -70px;
    }

    .owl-carousel .owl-nav button.owl-prev:hover,
    .owl-carousel .owl-nav button.owl-next:hover {
        background: rgba(0, 0, 0, 0.8) !important;
    }

    .owl-carousel .owl-dots {
        text-align: center;
        padding-top: 15px;
    }

    .owl-carousel .owl-dots button.owl-dot {
        width: 12px;
        height: 12px;
        border-radius: 50%;
        display: inline-block;
        background: #d6d6d6;
        margin: 0 5px;
    }

    .owl-carousel .owl-dots button.owl-dot.active {
        background: #0d6efd;
    }

    .related-products-section {
        position: relative;
        padding: 0 90px;
    }

    @media (max-width: 768px) {
        .related-products-section {
            padding: 0 20px;
        }
        
        .owl-carousel .owl-nav button.owl-prev {
            left: -15px;
        }

        .owl-carousel .owl-nav button.owl-next {
            right: -15px;
        }
    }
</style>

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
        <div class="col-md-6 mb-4">
            @if($product->image)
                <img src="{{ asset('storage/' . $product->image) }}" class="img-fluid rounded shadow" alt="{{ $product->name }}">
            @else
                <img src="https://via.placeholder.com/600x400?text={{ urlencode($product->name) }}" class="img-fluid rounded shadow" alt="{{ $product->name }}">
            @endif
        </div>
        
        <!-- Product Information -->
        <div class="col-md-6">
            <h1 class="mb-3">{{ $product->name }}</h1>
            <p class="text-muted mb-3">
                <i class="fas fa-tag"></i> Category: <a href="{{ route('shop.category', $product->category->slug) }}" class="text-decoration-none">{{ $product->category->name }}</a>
                @if($product->brand)
                    | <i class="fas fa-certificate"></i> Brand: <a href="{{ route('shop.brand', $product->brand->slug) }}" class="text-decoration-none">{{ $product->brand->name }}</a>
                @endif
            </p>
            
            <h3 class="text-primary mb-3">
                <i class="fas fa-rupee-sign"></i>{{ number_format($product->price, 2) }}
            </h3>
            
            <!-- Stock Status -->
            <div class="mb-3">
                <span class="badge bg-{{ $product->stock > 0 ? 'success' : 'danger' }} fs-6">
                    <i class="fas fa-{{ $product->stock > 0 ? 'check-circle' : 'times-circle' }}"></i>
                    {{ $product->stock > 0 ? 'In Stock (' . $product->stock . ' available)' : 'Out of Stock' }}
                </span>
            </div>
            
            <!-- Description -->
            <div class="mb-4">
                <h5><i class="fas fa-info-circle"></i> Description:</h5>
                <p class="text-muted">{{ $product->description }}</p>
            </div>
            
            <!-- Add to Cart -->
            @if($product->stock > 0)
                <div class="mb-3">
                    <label class="form-label fw-bold"><i class="fas fa-sort-numeric-up"></i> Quantity:</label>
                    <input type="number" class="form-control" id="quantity" value="1" min="1" max="{{ $product->stock }}" style="max-width: 120px;">
                </div>
                <button class="btn btn-primary btn-lg" id="add-to-cart-btn" data-product-id="{{ $product->id }}">
                    <i class="fas fa-cart-plus"></i> Add to Cart
                </button>
            @else
                <button class="btn btn-secondary btn-lg" disabled>
                    <i class="fas fa-ban"></i> Out of Stock
                </button>
            @endif
        </div>
    </div>

    <!-- Related Products Section -->
    @php
        $relatedProducts = \App\Models\Product::where('id', '!=', $product->id)
            ->where('is_active', true)
            ->where('category_id', $product->category_id)
            ->where('brand_id', $product->brand_id)
            ->where('stock', '>', 0)
            ->take(10)
            ->get();
        
        // If no products from same brand and category, show products from same category
        if($relatedProducts->count() < 10) {
            $additionalProducts = \App\Models\Product::where('id', '!=', $product->id)
                ->where('is_active', true)
                ->where('category_id', $product->category_id)
                ->where('stock', '>', 0)
                ->whereNotIn('id', $relatedProducts->pluck('id'))
                ->take(10 - $relatedProducts->count())
                ->get();
            
            $relatedProducts = $relatedProducts->merge($additionalProducts);
        }
    @endphp

    @if($relatedProducts->count() > 0)
        <div class="row mt-5">
            <div class="col-12">
                <h3 class="mb-4">
                    <i class="fas fa-box-open"></i> Related Products
                    @if($product->brand)
                        <small class="text-muted fs-6">from {{ $product->brand->name }}</small>
                    @endif
                </h3>
            </div>
            
            <div class="col-12 related-products-section">
                <div class="owl-carousel owl-theme related-products-carousel">
                    @foreach($relatedProducts as $relatedProduct)
                        <div class="item">
                            <div class="card related-product-card">
                                <a href="{{ route('shop.product', $relatedProduct->slug) }}">
                                    @if($relatedProduct->image)
                                        <img src="{{ asset('storage/' . $relatedProduct->image) }}" 
                                             class="card-img-top related-product-image" 
                                             alt="{{ $relatedProduct->name }}">
                                    @else
                                        <img src="https://via.placeholder.com/400x300/007bff/ffffff?text={{ urlencode($relatedProduct->name) }}" 
                                             class="card-img-top related-product-image" 
                                             alt="{{ $relatedProduct->name }}">
                                    @endif
                                </a>
                                
                                <div class="card-body">
                                    <h6 class="card-title">
                                        <a href="{{ route('shop.product', $relatedProduct->slug) }}" class="text-decoration-none text-dark">
                                            {{ Str::limit($relatedProduct->name, 40) }}
                                        </a>
                                    </h6>
                                    <p class="card-text text-muted small">
                                        {{ Str::limit($relatedProduct->description, 50) }}
                                    </p>
                                    
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <h5 class="text-primary mb-0">₹{{ number_format($relatedProduct->price, 2) }}</h5>
                                        <span class="badge bg-secondary">
                                            <i class="fas fa-box"></i> {{ $relatedProduct->stock }}
                                        </span>
                                    </div>
                                    
                                    <div class="d-grid gap-2">
                                        <a href="{{ route('shop.product', $relatedProduct->slug) }}" 
                                           class="btn btn-outline-primary btn-sm">
                                            <i class="fas fa-eye"></i> View Details
                                        </a>
                                        <button class="btn btn-primary btn-sm add-to-cart-related" 
                                                data-product-id="{{ $relatedProduct->id }}">
                                            <i class="fas fa-cart-plus"></i> Add to Cart
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    @endforeach
                </div>
            </div>
        </div>
    @endif
</div>

@push('scripts')
<script>
$(document).ready(function(){
    // Initialize Owl Carousel
    $('.related-products-carousel').owlCarousel({
        loop: true,
        margin: 15,
        nav: true,
        dots: true,
        autoplay: true,
        autoplayTimeout: 3000,
        autoplayHoverPause: true,
        navText: ["<i class='fas fa-chevron-left'></i>", "<i class='fas fa-chevron-right'></i>"],
        responsive: {
            0: {
                items: 1
            },
            576: {
                items: 2
            },
            768: {
                items: 3
            },
            992: {
                items: 4
            }
        }
    });

    // Main product add to cart
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

    // Related products add to cart
    $(document).on('click', '.add-to-cart-related', function() {
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
});
</script>
@endpush
@endsection