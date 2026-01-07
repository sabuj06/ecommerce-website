<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'E-commerce Shop')</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <link href="https://cdn.jsdelivr.net/npm/sweetalert2@11/dist/sweetalert2.min.css" rel="stylesheet">
    

    <!-- Owl Carousel CSS -->
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.carousel.min.css">
<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/assets/owl.theme.default.min.css">
    <style>
        body { 
            padding-top: 70px; 
        }
        .product-card { 
            transition: transform 0.2s; 
        }
        .product-card:hover { 
            transform: translateY(-5px); 
            box-shadow: 0 4px 15px rgba(0,0,0,0.1); 
        }
        .cart-badge { 
            position: absolute; 
            top: -8px; 
            right: -8px; 
        }
        
        /* Live Search Dropdown Styles */
        .search-wrapper {
            position: relative;
            width: 350px;
        }
        
        .search-dropdown {
            position: absolute;
            top: 100%;
            left: 0;
            right: 0;
            background: white;
            border: 1px solid #ddd;
            border-radius: 0 0 8px 8px;
            box-shadow: 0 4px 12px rgba(0,0,0,0.15);
            max-height: 400px;
            overflow-y: auto;
            z-index: 1050;
            display: none;
            margin-top: 2px;
        }
        
        .search-dropdown.show {
            display: block;
        }
        
        .search-item {
            display: flex;
            align-items: center;
            padding: 10px 15px;
            cursor: pointer;
            transition: background 0.2s;
            border-bottom: 1px solid #f0f0f0;
        }
        
        .search-item:hover {
            background: #f8f9fa;
        }
        
        .search-item:last-child {
            border-bottom: none;
        }
        
        .search-item-image {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 6px;
            margin-right: 12px;
        }
        
        .search-item-details {
            flex: 1;
        }
        
        .search-item-name {
            font-size: 0.9rem;
            font-weight: 500;
            color: #333;
            margin-bottom: 4px;
        }
        
        .search-item-price {
            font-size: 0.85rem;
            color: #0d6efd;
            font-weight: 600;
        }
        
        .search-loading {
            padding: 15px;
            text-align: center;
            color: #666;
        }
        
        .search-no-results {
            padding: 15px;
            text-align: center;
            color: #999;
        }
        
        @media (max-width: 768px) {
            .search-wrapper {
                width: 100%;
            }
        }
    </style>
</head>
<body>
    <!-- Navigation Bar -->
    <nav class="navbar navbar-expand-lg navbar-dark bg-primary fixed-top">
        <div class="container">
            <a class="navbar-brand" href="{{ route('shop.index') }}">
                <i class="fas fa-shopping-bag"></i> E-Shop
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarNav">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarNav">
                <ul class="navbar-nav me-auto">
                    <li class="nav-item">
                        <a class="nav-link" href="{{ route('shop.index') }}">Home</a>
                    </li>
                </ul>
                
                <!-- Live Search Form -->
                <div class="search-wrapper me-3">
                    <form action="{{ route('shop.search') }}" method="GET" id="searchForm">
                        <div class="input-group">
                            <input 
                                class="form-control" 
                                type="search" 
                                name="q" 
                                id="searchInput"
                                placeholder="Search products..." 
                                value="{{ request('q') }}"
                                autocomplete="off">
                            <button class="btn btn-outline-light" type="submit">
                                <i class="fas fa-search"></i>
                            </button>
                        </div>
                    </form>
                    
                    <!-- Live Search Dropdown -->
                    <div class="search-dropdown" id="searchDropdown">
                        <div class="search-loading" id="searchLoading">
                            <i class="fas fa-spinner fa-spin"></i> Searching...
                        </div>
                        <div id="searchResults"></div>
                    </div>
                </div>
                
                <a href="{{ route('cart.index') }}" class="btn btn-outline-light position-relative">
                    <i class="fas fa-shopping-cart"></i> Cart
                    <span class="badge bg-danger cart-badge" id="cart-count">0</span>
                </a>
            </div>
        </div>
    </nav>

    <!-- Main Content -->
    <main class="py-4">
        @yield('content')
    </main>

    <!-- Footer -->
    <footer class="bg-dark text-white mt-5 py-4">
        <div class="container text-center">
            <p>&copy; 2024 E-commerce Shop. All rights reserved.</p>
        </div>
    </footer>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        // Setup CSRF Token for Ajax
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });

        // Update Cart Count
        function updateCartCount() {
            $.get('{{ route("cart.count") }}', function(data) {
                $('#cart-count').text(data.count);
            });
        }

        // Live Search Functionality
        let searchTimeout;
        const searchInput = $('#searchInput');
        const searchDropdown = $('#searchDropdown');
        const searchResults = $('#searchResults');
        const searchLoading = $('#searchLoading');

        searchInput.on('input', function() {
            const query = $(this).val().trim();

            clearTimeout(searchTimeout);

            if (query.length < 2) {
                searchDropdown.removeClass('show');
                return;
            }

            searchLoading.show();
            searchResults.empty();
            searchDropdown.addClass('show');

            searchTimeout = setTimeout(function() {
                $.ajax({
                    url: '{{ route("shop.liveSearch") }}',
                    method: 'GET',
                    data: { q: query },
                    success: function(products) {
                        searchLoading.hide();
                        
                        if (products.length === 0) {
                            searchResults.html('<div class="search-no-results">No products found</div>');
                            return;
                        }

                        let html = '';
                        products.forEach(function(product) {
                            const imageUrl = product.image 
                                ? '{{ asset("storage") }}/' + product.image 
                                : 'https://via.placeholder.com/50?text=' + encodeURIComponent(product.name);
                            
                            html += `
                                <a href="/product/${product.slug}" class="search-item text-decoration-none">
                                    <img src="${imageUrl}" alt="${product.name}" class="search-item-image">
                                    <div class="search-item-details">
                                        <div class="search-item-name">${product.name}</div>
                                        <div class="search-item-price">₹${parseFloat(product.price).toLocaleString()}</div>
                                    </div>
                                </a>
                            `;
                        });

                        searchResults.html(html);
                    },
                    error: function() {
                        searchLoading.hide();
                        searchResults.html('<div class="search-no-results">Error loading results</div>');
                    }
                });
            }, 300);
        });

        // Close dropdown when clicking outside
        $(document).on('click', function(e) {
            if (!$(e.target).closest('.search-wrapper').length) {
                searchDropdown.removeClass('show');
            }
        });

        // Show dropdown when input is focused and has value
        searchInput.on('focus', function() {
            if ($(this).val().trim().length >= 2) {
                searchDropdown.addClass('show');
            }
        });

        $(document).ready(function() {
            updateCartCount();
        });
    </script>
    <!-- Owl Carousel JS -->
<script src="https://cdnjs.cloudflare.com/ajax/libs/OwlCarousel2/2.3.4/owl.carousel.min.js"></script>
    @stack('scripts')
</body>
</html>