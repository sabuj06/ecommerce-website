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
$(document).ready(function() {
    // Delete Category
    $('.delete-category').on('click', function(e) {
        e.preventDefault();
        
        const btn = $(this);
        const categoryId = btn.data('id');
        const deleteUrl = '{{ url("admin/categories") }}/' + categoryId;
        
        Swal.fire({
            title: 'Are you sure?',
            text: "Do you want to delete this category?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                // Show loading
                btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');
                
                $.ajax({
                    url: deleteUrl,
                    type: 'DELETE',
                    data: {
                        _token: '{{ csrf_token() }}'
                    },
                    success: function(response) {
                        if(response.success) {
                            Swal.fire({
                                icon: 'success',
                                title: 'Deleted!',
                                text: response.message,
                                timer: 2000,
                                showConfirmButton: false
                            });
                            
                            // Remove row with animation
                            btn.closest('tr').fadeOut(500, function() {
                                $(this).remove();
                            });
                        }
                    },
                    error: function(xhr) {
                        console.log('Error:', xhr);
                        
                        // Reset button
                        btn.prop('disabled', false).html('<i class="fas fa-trash"></i>');
                        
                        if(xhr.status === 400 && xhr.responseJSON) {
                            // Category has products or subcategories
                            const response = xhr.responseJSON;
                            Swal.fire({
                                icon: 'error',
                                title: 'Cannot Delete Category!',
                                html: `
                                    <p class="mb-3">${response.message}</p>
                                    <div class="alert alert-warning text-start">
                                        <strong>Steps to delete this category:</strong>
                                        <ol class="mb-0 mt-2">
                                            <li>Go to <strong>Products</strong> section</li>
                                            <li>Find products in this category</li>
                                            <li>Change their category or delete those products</li>
                                            <li>If there are subcategories, delete or move them first</li>
                                            <li>Then try deleting this category again</li>
                                        </ol>
                                    </div>
                                `,
                                confirmButtonText: 'Got it',
                                confirmButtonColor: '#3085d6'
                            });
                        } else if(xhr.status === 500 && xhr.responseJSON) {
                            Swal.fire({
                                icon: 'error',
                                title: 'Server Error!',
                                text: xhr.responseJSON.message || 'Something went wrong',
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: 'Failed to delete category. Please try again.',
                            });
                        }
                    }
                });
            }
        });
    });
});
</script>
@endpush
@endsection