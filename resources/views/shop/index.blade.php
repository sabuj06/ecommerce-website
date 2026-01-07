@extends('layouts.app')

@section('title', 'Shop - All Products')

@section('content')

<style>
    /* Sidebar Sticky CSS */
    .sidebar-fixed {
        position: sticky;
        top: 80px;
        max-height: calc(100vh - 100px);
        overflow-y: auto;
    }

    .sidebar-fixed::-webkit-scrollbar {
        width: 6px;
    }
    
    .sidebar-fixed::-webkit-scrollbar-track {
        background: #f1f1f1;
    }
    
    .sidebar-fixed::-webkit-scrollbar-thumb {
        background: #888;
        border-radius: 10px;
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

    /* Brand count badge */
    .brand-count {
        font-size: 0.75rem;
    }

    /* Chevron animation */
    .card-header[data-bs-toggle="collapse"] i.fa-chevron-down {
        transition: transform 0.3s ease;
    }
    
    .card-header[data-bs-toggle="collapse"]:not(.collapsed) i.fa-chevron-down {
        transform: rotate(180deg);
    }

    /* Price Range Styling */
    .price-filter-item {
        cursor: pointer;
        transition: all 0.3s ease;
    }

    .price-filter-item:hover {
        background-color: #f8f9fa;
    }

    .price-filter-item.active {
        background-color: #0d6efd;
        color: white;
    }
</style>

<div class="container">
    <div class="row">
        
        <!-- Sidebar - Categories, Brands & Price Filter -->
        <div class="col-md-3">
            <div class="sidebar-fixed">
                <!-- Categories Card -->
                <div class="card mb-3">
                    <div class="card-header bg-primary text-white">
                        <h5 class="mb-0"><i class="fas fa-list"></i> Categories</h5>
                    </div>
                    <div class="list-group list-group-flush">
                        <a href="{{ route('shop.index') }}" 
                           class="list-group-item list-group-item-action {{ !isset($currentCategory) ? 'active' : '' }}">
                            <i class="fas fa-home"></i> All Products
                        </a>
                        @foreach($categories as $category)
                            <a href="{{ route('shop.category', $category->slug) }}" 
                               class="list-group-item list-group-item-action {{ isset($currentCategory) && $currentCategory->id == $category->id ? 'active' : '' }}">
                                {{ $category->name }}
                            </a>
                        @endforeach
                    </div>
                </div>

                <!-- Brands Card with Collapse -->
                <div class="card mb-3">
                    <div class="card-header bg-info text-white" 
                         style="cursor: pointer;" 
                         data-bs-toggle="collapse" 
                         data-bs-target="#brandsCollapse">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0"><i class="fas fa-tags"></i> Brands</h5>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                    </div>
                    <div class="collapse show" id="brandsCollapse">
                        <div class="list-group list-group-flush">
                            @php
                                // If viewing a specific category, show only brands for that category
                                if(isset($currentCategory)) {
                                    $brands = \App\Models\Brand::where('is_active', true)
                                                ->whereHas('products', function($query) use ($currentCategory) {
                                                    $query->where('category_id', $currentCategory->id)
                                                          ->where('is_active', true);
                                                })
                                                ->withCount(['products' => function($query) use ($currentCategory) {
                                                    $query->where('category_id', $currentCategory->id)
                                                          ->where('is_active', true);
                                                }])
                                                ->get();
                                } else {
                                    $brands = \App\Models\Brand::where('is_active', true)
                                                ->withCount(['products' => function($query) {
                                                    $query->where('is_active', true);
                                                }])
                                                ->get();
                                }
                            @endphp
                            
                            @forelse($brands as $brand)
                                @php
                                    // Preserve price filter in brand links
                                    $brandParams = request()->only(['min_price', 'max_price']);
                                    if(isset($currentCategory)) {
                                        $brandUrl = route('shop.category.brand', [$currentCategory->slug, $brand->slug]) . (count($brandParams) ? '?' . http_build_query($brandParams) : '');
                                    } else {
                                        $brandUrl = route('shop.brand', $brand->slug) . (count($brandParams) ? '?' . http_build_query($brandParams) : '');
                                    }
                                @endphp
                                
                                <a href="{{ $brandUrl }}" 
                                   class="list-group-item list-group-item-action d-flex justify-content-between align-items-center {{ isset($currentBrand) && $currentBrand->id == $brand->id ? 'active' : '' }}">
                                    <span>
                                        <i class="fas fa-tag"></i> {{ $brand->name }}
                                    </span>
                                    <span class="badge bg-secondary brand-count">
                                        {{ $brand->products_count }}
                                    </span>
                                </a>
                            @empty
                                <div class="list-group-item text-muted">
                                    <i class="fas fa-info-circle"></i> No brands available
                                    @if(isset($currentCategory))
                                        for {{ $currentCategory->name }}
                                    @endif
                                </div>
                            @endforelse
                        </div>
                    </div>
                </div>

                <!-- Price Range Filter -->
                <div class="card">
                    <div class="card-header bg-success text-white" 
                         style="cursor: pointer;" 
                         data-bs-toggle="collapse" 
                         data-bs-target="#priceCollapse">
                        <div class="d-flex justify-content-between align-items-center">
                            <h5 class="mb-0"><i class="fas fa-rupee-sign"></i> Price Range</h5>
                            <i class="fas fa-chevron-down"></i>
                        </div>
                    </div>
                    <div class="collapse show" id="priceCollapse">
                        <div class="list-group list-group-flush">
                            @php
                                $priceRanges = [
                                    ['min' => null, 'max' => null, 'label' => 'All Prices'],
                                    ['min' => 0, 'max' => 10000, 'label' => 'Under ₹10,000'],
                                    ['min' => 10000, 'max' => 15000, 'label' => '₹10,000 - ₹15,000'],
                                    ['min' => 15000, 'max' => 20000, 'label' => '₹15,000 - ₹20,000'],
                                    ['min' => 20000, 'max' => 25000, 'label' => '₹20,000 - ₹25,000'],
                                    ['min' => 25000, 'max' => 30000, 'label' => '₹25,000 - ₹30,000'],
                                    ['min' => 30000, 'max' => null, 'label' => 'Above ₹30,000'],
                                ];
                                
                                $currentMinPrice = request('min_price');
                                $currentMaxPrice = request('max_price');
                            @endphp
                            
                            @foreach($priceRanges as $range)
                                @php
                                    $isActive = ($currentMinPrice == $range['min'] && $currentMaxPrice == $range['max']);
                                    
                                    // Build URL with current filters
                                    $params = request()->except(['min_price', 'max_price', 'page']);
                                    if($range['min'] !== null) $params['min_price'] = $range['min'];
                                    if($range['max'] !== null) $params['max_price'] = $range['max'];
                                    
                                    // Determine route based on current page
                                    if(isset($currentCategory) && isset($currentBrand)) {
                                        $url = route('shop.category.brand', [$currentCategory->slug, $currentBrand->slug]) . '?' . http_build_query($params);
                                    } elseif(isset($currentCategory)) {
                                        $url = route('shop.category', $currentCategory->slug) . '?' . http_build_query($params);
                                    } elseif(isset($currentBrand)) {
                                        $url = route('shop.brand', $currentBrand->slug) . '?' . http_build_query($params);
                                    } else {
                                        $url = route('shop.index') . '?' . http_build_query($params);
                                    }
                                @endphp
                                
                                <a href="{{ $url }}" 
                                   class="list-group-item list-group-item-action price-filter-item {{ $isActive ? 'active' : '' }}">
                                    <i class="fas fa-filter"></i> {{ $range['label'] }}
                                </a>
                            @endforeach
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Main Content - Products -->
        <div class="col-md-9">
            <div class="d-flex justify-content-between align-items-center mb-4">
                <h2>
                    <i class="fas fa-shopping-bag"></i> 
                    @if(isset($currentCategory) && isset($currentBrand))
                        {{ $currentCategory->name }} - {{ $currentBrand->name }}
                    @elseif(isset($currentCategory))
                        {{ $currentCategory->name }}
                    @elseif(isset($currentBrand))
                        {{ $currentBrand->name }}
                    @else
                        All Products
                    @endif
                </h2>
                <div>
                    <span class="badge bg-primary">{{ $products->total() }} Products</span>
                    @if(request('min_price') || request('max_price'))
                        <span class="badge bg-success">
                            @if(request('min_price') && request('max_price'))
                                ₹{{ number_format(request('min_price')) }} - ₹{{ number_format(request('max_price')) }}
                            @elseif(request('min_price'))
                                Above ₹{{ number_format(request('min_price')) }}
                            @else
                                Under ₹{{ number_format(request('max_price')) }}
                            @endif
                        </span>
                    @endif
                </div>
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
                            <i class="fas fa-info-circle"></i> No products found
                            @if(isset($currentCategory) && isset($currentBrand))
                                for {{ $currentCategory->name }} - {{ $currentBrand->name }}
                            @elseif(isset($currentCategory))
                                in {{ $currentCategory->name }}
                            @elseif(isset($currentBrand))
                                for {{ $currentBrand->name }}
                            @endif
                            @if(request('min_price') || request('max_price'))
                                in this price range
                            @endif
                        </div>
                    </div>
                @endforelse
            </div>

            <!-- Pagination -->
            <div class="d-flex justify-content-center mt-4">
                {{ $products->appends(request()->query())->links() }}
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