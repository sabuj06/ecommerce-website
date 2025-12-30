@extends('admin.layouts.app')

@section('title', 'Manage Brands')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-tag"></i> Manage Brands</h2>
    <a href="{{ route('admin.brands.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Add New Brand
    </a>
</div>

<div class="card">
    <div class="card-body">
        @if($brands->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Logo</th>
                            <th>Name</th>
                            <th>Products</th>
                            <th>Status</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($brands as $brand)
                            <tr>
                                <td>{{ $brand->id }}</td>
                                <td>
                                    @if($brand->logo)
                                        <img src="{{ asset('storage/' . $brand->logo) }}" alt="{{ $brand->name }}" style="width: 50px; height: 50px; object-fit: contain;">
                                    @else
                                        <i class="fas fa-tag fa-2x text-muted"></i>
                                    @endif
                                </td>
                                <td><strong>{{ $brand->name }}</strong></td>
                                <td><span class="badge bg-primary">{{ $brand->products_count }} Products</span></td>
                                <td>
                                    <span class="badge bg-{{ $brand->is_active ? 'success' : 'secondary' }}">
                                        {{ $brand->is_active ? 'Active' : 'Inactive' }}
                                    </span>
                                </td>
                                <td>
                                    <a href="{{ route('admin.brands.edit', $brand->id) }}" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button class="btn btn-sm btn-danger delete-brand" data-id="{{ $brand->id }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $brands->links() }}
        @else
            <div class="alert alert-info">
                No brands found. <a href="{{ route('admin.brands.create') }}">Create your first brand</a>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    $('.delete-brand').click(function() {
        if(!confirm('Are you sure?')) return;
        const btn = $(this);
        const brandId = btn.data('id');
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');

        $.ajax({
            url: '/admin/brands/' + brandId,
            type: 'DELETE',
            success: function(response) {
                if(response.success) {
                    alert(response.message);
                    btn.closest('tr').fadeOut();
                } else {
                    alert(response.message);
                    btn.prop('disabled', false).html('<i class="fas fa-trash"></i>');
                }
            }
        });
    });
</script>
@endpush
@endsection