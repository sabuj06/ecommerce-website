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
                        <!-- Status Toggle -->
                        <div class="form-check form-switch d-flex justify-content-center">
                            <input class="form-check-input status-toggle" 
                                   type="checkbox" 
                                   data-id="{{ $product->id }}"
                                   {{ $product->is_active ? 'checked' : '' }}>
                        </div>
                        <small class="status-text-{{ $product->id }} badge bg-{{ $product->is_active ? 'success' : 'secondary' }}">
                            {{ $product->is_active ? 'Active' : 'Inactive' }}
                        </small>
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

@push('scripts')
<script>
$(document).ready(function() {
    
    // Toggle Status
    $('.status-toggle').on('change', function() {
        const checkbox = $(this);
        const productId = checkbox.data('id');
        const isChecked = checkbox.is(':checked');
        
        checkbox.prop('disabled', true);
        
        $.ajax({
            url: '/admin/products/' + productId + '/toggle-status',
            type: 'POST',
            data: { _token: '{{ csrf_token() }}' },
            success: function(response) {
                if(response.success) {
                    const badge = $('.status-text-' + productId);
                    if(response.is_active) {
                        badge.removeClass('bg-secondary').addClass('bg-success').text('Active');
                    } else {
                        badge.removeClass('bg-success').addClass('bg-secondary').text('Inactive');
                    }
                    
                    Swal.fire({
                        icon: 'success',
                        title: response.message,
                        timer: 1500,
                        showConfirmButton: false
                    });
                }
                checkbox.prop('disabled', false);
            },
            error: function() {
                checkbox.prop('checked', !isChecked);
                checkbox.prop('disabled', false);
                Swal.fire('Error!', 'Failed to update status', 'error');
            }
        });
    });

    // Delete Product
    $('.delete-product').on('click', function() {
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
                            $('#product-row-' + productId).fadeOut();
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