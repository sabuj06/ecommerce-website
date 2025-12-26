@extends('admin.layouts.app')

@section('title', 'Manage Products')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-box"></i> Manage Products</h2>
    <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Add New Product
    </a>
</div>

<div class="card">
    <div class="card-body">
        @if($products->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Image</th>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($products as $product)
                            <tr>
                                <td>{{ $product->id }}</td>
                                <td>
                                    @if($product->image)
                                        <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px;">
                                    @else
                                        <div style="width: 50px; height: 50px; background: #ddd; border-radius: 5px;"></div>
                                    @endif
                                </td>
                                <td>{{ $product->name }}</td>
                                <td>
                                    <span class="badge bg-secondary">{{ $product->category->name }}</span>
                                </td>
                                <td><strong>৳{{ number_format($product->price, 2) }}</strong></td>
                                <td>
                                    <span class="badge bg-{{ $product->stock > 10 ? 'success' : ($product->stock > 0 ? 'warning' : 'danger') }}">
                                        {{ $product->stock }}
                                    </span>
                                </td>
                                <td>{{ $product->created_at->format('d M, Y') }}</td>
                                <td>
                                    <a href="{{ route('admin.products.edit', $product->id) }}" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button class="btn btn-sm btn-danger delete-product" data-id="{{ $product->id }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $products->links() }}
            </div>
        @else
            <div class="alert alert-info">
                No products found. <a href="{{ route('admin.products.create') }}">Create your first product</a>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    $('.delete-product').click(function() {
        if(!confirm('Are you sure you want to delete this product?')) return;

        const btn = $(this);
        const productId = btn.data('id');

        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');

        $.ajax({
            url: '/admin/products/' + productId,
            type: 'DELETE',
            success: function(response) {
                if(response.success) {
                    btn.closest('tr').fadeOut(function() {
                        $(this).remove();
                    });
                    alert(response.message);
                }
            },
            error: function() {
                alert('Failed to delete product');
                btn.prop('disabled', false).html('<i class="fas fa-trash"></i>');
            }
        });
    });
</script>
@endpush
@endsection