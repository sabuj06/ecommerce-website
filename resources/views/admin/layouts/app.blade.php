<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <meta name="csrf-token" content="{{ csrf_token() }}">
    <title>@yield('title', 'Admin Panel')</title>
    
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">

    <script src="https://cdn.jsdelivr.net/npm/sweetalert2@11"></script>
    
    <style>
        body {
            font-family: Arial, sans-serif;
            margin: 0;
            padding: 0;
        }

        /* Fixed Sidebar */
        .sidebar {
            position: fixed;
            top: 0;
            left: 0;
            width: 220px; /* fixed width */
            height: 100vh;
            background: #343a40;
            overflow-y: auto; /* scroll if content overflows */
        }

        .sidebar .nav-link {
            color: #fff;
            padding: 15px 20px;
            border-bottom: 1px solid #495057;
        }
        .sidebar .nav-link:hover,
        .sidebar .nav-link.active {
            background: #495057;
            color: #fff;
        }
        .sidebar .nav-link.ps-4 {
            padding-left: 40px;
        }

        /* Main content */
        .main-content {
            margin-left: 220px; /* sidebar width */
            padding: 30px;
            background: #f8f9fa;
            min-height: 100vh;
        }

        /* Optional: stat cards hover */
        .stat-card {
            border-left: 4px solid;
            transition: transform 0.2s;
        }
        .stat-card:hover {
            transform: translateY(-5px);
        }

        /* Scrollbar styling for sidebar */
        .sidebar::-webkit-scrollbar {
            width: 6px;
        }
        .sidebar::-webkit-scrollbar-thumb {
            background-color: rgba(255,255,255,0.2);
            border-radius: 3px;
        }

        /* Responsive for mobile */
        @media (max-width: 768px) {
            .sidebar {
                position: relative;
                width: 100%;
                height: auto;
            }
            .main-content {
                margin-left: 0;
            }
        }
    </style>
</head>
<body>
    <!-- Sidebar -->
    <div class="sidebar">
        <div class="p-3 text-white">
            <h4><i class="fas fa-store"></i> Admin Panel</h4>
            <small>Welcome, {{ Auth::guard('admin')->user()->name }}</small>
        </div>
        
        <nav class="nav flex-column">
            <a class="nav-link {{ request()->routeIs('admin.dashboard') ? 'active' : '' }}" href="{{ route('admin.dashboard') }}">
                <i class="fas fa-dashboard"></i> Dashboard
            </a>
            <a class="nav-link {{ request()->routeIs('admin.categories.*') ? 'active' : '' }}" href="{{ route('admin.categories.index') }}">
                <i class="fas fa-folder"></i> Categories
            </a>
            <a class="nav-link {{ request()->routeIs('admin.brands.*') ? 'active' : '' }}" href="{{ route('admin.brands.index') }}">
                <i class="fas fa-tag"></i> Brands
            </a>
            <a class="nav-link {{ request()->routeIs('admin.colors.*') ? 'active' : '' }}" href="{{ route('admin.colors.index') }}">
                <i class="fas fa-palette"></i> Colors
            </a>
            <a class="nav-link {{ request()->routeIs('admin.products.*') ? 'active' : '' }}" href="{{ route('admin.products.index') }}">
                <i class="fas fa-box"></i> Products
            </a>
            <a class="nav-link {{ request()->routeIs('admin.orders.*') ? 'active' : '' }}" href="{{ route('admin.orders.index') }}">
                <i class="fas fa-shopping-cart"></i> Orders
            </a>

            <!-- DataTables Section -->
            <div class="nav-link text-white" style="background: #495057; font-weight: bold; margin-top: 10px;">
                <i class="fas fa-table"></i> DataTables View
            </div>
            <a class="nav-link ps-4 {{ request()->routeIs('admin.products.datatable') ? 'active' : '' }}" href="{{ route('admin.products.datatable') }}">
                <i class="fas fa-box"></i> Products DT
            </a>
            <a class="nav-link ps-4 {{ request()->routeIs('admin.brands.datatable') ? 'active' : '' }}" href="{{ route('admin.brands.datatable') }}">
                <i class="fas fa-tag"></i> Brands DT
            </a>
            <a class="nav-link ps-4 {{ request()->routeIs('admin.orders.datatable') ? 'active' : '' }}" href="{{ route('admin.orders.datatable') }}">
                <i class="fas fa-shopping-cart"></i> Orders DT
            </a>

            <a class="nav-link" href="{{ route('shop.index') }}" target="_blank">
                <i class="fas fa-external-link-alt"></i> View Site
            </a>
            <form action="{{ route('admin.logout') }}" method="POST" style="margin: 0;">
                @csrf
                <button type="submit" class="nav-link btn text-start w-100" style="border: none; color: #fff;">
                    <i class="fas fa-sign-out-alt"></i> Logout
                </button>
            </form>
        </nav>
    </div>

    <!-- Main Content -->
    <div class="main-content">
        @yield('content')
    </div>

    <!-- jQuery -->
    <script src="https://code.jquery.com/jquery-3.7.0.min.js"></script>
    
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    
    <script>
        $.ajaxSetup({
            headers: {
                'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
            }
        });
    </script>
    
    @stack('scripts')
</body>
</html>
