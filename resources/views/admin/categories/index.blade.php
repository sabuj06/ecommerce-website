@extends('admin.layouts.app')

@section('title', 'Manage Categories')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-folder"></i> Manage Categories</h2>
    <a href="{{ route('admin.categories.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Add New Category
    </a>
</div>

<div class="card">
    <div class="card-body">
        @if($categories->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Name</th>
                            <th>Slug</th>
                            <th>Products Count</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($categories as $category)
                            <tr>
                                <td>{{ $category->id }}</td>
                                <td>{{ $category->name }}</td>
                                <td><code>{{ $category->slug }}</code></td>
                                <td>
                                    <span class="badge bg-primary">{{ $category->products_count }} Products</span>
                                </td>
                                <td>{{ $category->created_at->format('d M, Y') }}</td>
                                <td>
                                    <a href="{{ route('admin.categories.edit', $category->id) }}" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button class="btn btn-sm btn-danger delete-category" data-id="{{ $category->id }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="mt-3">
                {{ $categories->links() }}
            </div>
        @else
            <div class="alert alert-info">
                No categories found. <a href="{{ route('admin.categories.create') }}">Create your first category</a>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
    $('.delete-category').click(function() {
        if(!confirm('Are you sure you want to delete this category?')) return;

        const btn = $(this);
        const categoryId = btn.data('id');

        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');

        $.ajax({
            url: '/admin/categories/' + categoryId,
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
                alert('Failed to delete category');
                btn.prop('disabled', false).html('<i class="fas fa-trash"></i>');
            }
        });
    });
</script>
@endpush
@endsection