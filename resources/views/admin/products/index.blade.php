@extends('admin.layouts.app')

@section('title', 'Manage Products')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-box"></i> Manage Products</h2>
    <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Add New Products
    </a>
</div>

<div class="table-responsive">
    <table class="table table-hover align-middle text-center">
        <thead class="table-light">
            <tr>
                <th>ID</th>
                <th>Image</th>
                <th>Name</th>
                <th>Category</th>
                <th>Brand</th>
                <th>Color</th>
                <th>Price</th>
                <th>Stock</th>
                <th>Status</th>
                <th>Created</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $product)
                <tr id="product-row-{{ $product->id }}">
                    <td>{{ $product->id }}</td>
                    <td>
                        @if($product->image)
                            <img src="{{ asset('storage/' . $product->image) }}"
                                 alt="{{ $product->name }}"
                                 class="rounded"
                                 style="width:50px;height:50px;object-fit:cover;">
                        @else
                            <div class="bg-secondary rounded"
                                 style="width:50px;height:50px;margin:auto;"></div>
                        @endif
                    </td>
                    <td class="fw-semibold">{{ $product->name }}</td>
                    <td>
                        <span class="badge bg-secondary px-3">
                            {{ $product->category->name }}
                        </span>
                    </td>
                    <td>
                        <span class="badge bg-info text-dark px-3">
                            {{ $product->brand->name ?? 'No Brand' }}
                        </span>
                    </td>
                    <td>
                        <span class="badge bg-dark px-3">
                            {{ $product->color->name ?? 'No Color' }}
                        </span>
                    </td>
                    <td class="fw-bold text-success">
                        ₹{{ number_format($product->price, 2) }}
                    </td>
                    <td>
                        <span class="badge bg-{{ $product->stock > 10 ? 'success' : ($product->stock > 0 ? 'warning' : 'danger') }} px-3">
                            {{ $product->stock }}
                        </span>
                    </td>
                    <td>
                        <!-- Status Dropdown -->
                        @php
                            $statuses = [
                                0 => ['label' => 'Inactive', 'color' => 'secondary'],
                                1 => ['label' => 'Active', 'color' => 'success'],
                                8 => ['label' => 'Unavailable', 'color' => 'danger']
                            ];
                            $currentStatus = $statuses[$product->is_active] ?? $statuses[1];
                        @endphp
                        
                        <div class="dropdown">
                            <button class="btn btn-sm btn-{{ $currentStatus['color'] }} dropdown-toggle status-dropdown-btn" 
                                    type="button" 
                                    data-bs-toggle="dropdown"
                                    id="statusDropdown{{ $product->id }}">
                                {{ $currentStatus['label'] }}
                            </button>
                            <ul class="dropdown-menu" aria-labelledby="statusDropdown{{ $product->id }}">
                                @foreach($statuses as $value => $status)
                                    @if($value != $product->is_active)
                                        <li>
                                            <a class="dropdown-item change-status" 
                                               href="javascript:void(0)" 
                                               data-id="{{ $product->id }}" 
                                               data-status="{{ $value }}"
                                               data-label="{{ $status['label'] }}"
                                               data-color="{{ $status['color'] }}">
                                                <span class="badge bg-{{ $status['color'] }}">{{ $status['label'] }}</span>
                                            </a>
                                        </li>
                                    @endif
                                @endforeach
                            </ul>
                        </div>
                    </td>
                    <td>{{ $product->created_at->format('d M, Y') }}</td>
                    <td>
                        <div class="d-flex justify-content-center gap-1">
                            <a href="{{ route('admin.products.edit', $product->id) }}"
                               class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button class="btn btn-sm btn-danger delete-product"
                                    data-id="{{ $product->id }}">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>

<!-- Pagination -->
<div class="mt-3">
    {{ $products->links() }}
</div>
@endsection

@push('styles')
<style>
    .dropdown-menu {
        background-color: #fff !important;
        border: 1px solid #ddd !important;
        box-shadow: 0 4px 6px rgba(0,0,0,0.1) !important;
        z-index: 1050 !important;
        min-width: 120px !important;
    }
    
    .dropdown-item {
        padding: 8px 16px !important;
        cursor: pointer !important;
    }
    
    .dropdown-item:hover {
        background-color: #f8f9fa !important;
    }
    
    .table-responsive {
        overflow-x: auto;
        overflow-y: visible !important;
    }
    
    .dropdown {
        position: relative;
        z-index: 1;
    }
</style>
@endpush

@push('scripts')
<script>
$(document).ready(function() {
    
    // Change Status from Dropdown
    $(document).on('click', '.change-status', function(e) {
        e.preventDefault();
        
        const link = $(this);
        const productId = link.data('id');
        const newStatus = link.data('status');
        const newLabel = link.data('label');
        const newColor = link.data('color');
        const button = $('#statusDropdown' + productId);
        
        // Disable button during request
        button.prop('disabled', true);
        
        $.ajax({
            url: '/admin/products/' + productId + '/update-status',
            type: 'POST',
            data: { 
                _token: '{{ csrf_token() }}',
                status: newStatus
            },
            success: function(response) {
                if(response.success) {
                    // Update button appearance
                    button.removeClass('btn-secondary btn-success btn-danger')
                          .addClass('btn-' + newColor)
                          .text(newLabel);
                    
                    Swal.fire({
                        icon: 'success',
                        title: response.message,
                        timer: 1500,
                        showConfirmButton: false
                    });
                    
                    // Reload page after 1.5 seconds to update dropdown options
                    setTimeout(() => {
                        location.reload();
                    }, 1500);
                }
                button.prop('disabled', false);
            },
            error: function(xhr) {
                button.prop('disabled', false);
                let errorMsg = 'Failed to update status';
                if(xhr.responseJSON && xhr.responseJSON.message) {
                    errorMsg = xhr.responseJSON.message;
                }
                Swal.fire('Error!', errorMsg, 'error');
            }
        });
    });

    // Delete Product
    $(document).on('click', '.delete-product', function() {
        const btn = $(this);
        const productId = btn.data('id');
        
        Swal.fire({
            title: 'Are you sure?',
            text: "Delete this product?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            confirmButtonText: 'Yes, delete it!'
        }).then((result) => {
            if (result.isConfirmed) {
                btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');
                
                $.ajax({
                    url: '/admin/products/' + productId,
                    type: 'DELETE',
                    data: { _token: '{{ csrf_token() }}' },
                    success: function(response) {
                        if(response.success) {
                            Swal.fire('Deleted!', response.message, 'success');
                            $('#product-row-' + productId).fadeOut(500, function() {
                                $(this).remove();
                            });
                        }
                    },
                    error: function() {
                        btn.prop('disabled', false).html('<i class="fas fa-trash"></i>');
                        Swal.fire('Error!', 'Failed to delete', 'error');
                    }
                });
            }
        });
    });
});
</script>
@endpush