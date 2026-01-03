@extends('admin.layouts.app')

@section('title', 'Manage Colors')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-palette"></i> Manage Colors</h2>
    <a href="{{ route('admin.colors.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Add New Color
    </a>
</div>

<div class="card">
    <div class="card-body">
        @if($colors->count() > 0)
            <div class="table-responsive">
                <table class="table table-hover">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Preview</th>
                            <th>Name</th>
                            <th>Hex Code</th>
                            <th>Products</th>
                            <th>Created</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($colors as $color)
                            <tr>
                                <td>{{ $color->id }}</td>
                                <td>
                                    <div style="width: 50px; height: 50px; background-color: {{ $color->code }}; border: 2px solid #ddd; border-radius: 5px;"></div>
                                </td>
                                <td><strong>{{ $color->name }}</strong></td>
                                <td><code>{{ $color->code }}</code></td>
                                <td><span class="badge bg-primary">{{ $color->products_count }} Products</span></td>
                                <td>{{ $color->created_at->format('d M, Y') }}</td>
                                <td>
                                    <a href="{{ route('admin.colors.edit', $color->id) }}" class="btn btn-sm btn-warning">
                                        <i class="fas fa-edit"></i>
                                    </a>
                                    <button class="btn btn-sm btn-danger delete-color" data-id="{{ $color->id }}">
                                        <i class="fas fa-trash"></i>
                                    </button>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>
            {{ $colors->links() }}
        @else
            <div class="alert alert-info">
                No colors found. <a href="{{ route('admin.colors.create') }}">Create your first color</a>
            </div>
        @endif
    </div>
</div>

@push('scripts')
<script>
     $('.delete-color').click(function() {
        const btn = $(this);
        const colorId = btn.data('id');
        
        Swal.fire({
            title: 'Are you sure?',
            text: "Do you want to delete this color?",
            icon: 'warning',
            showCancelButton: true,
            confirmButtonColor: '#d33',
            cancelButtonColor: '#3085d6',
            confirmButtonText: 'Yes, delete it!',
            cancelButtonText: 'Cancel'
        }).then((result) => {
            if (result.isConfirmed) {
                btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');
                
                $.ajax({
                    url: '/admin/colors/' + colorId,
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
                                timer: 2000
                            }).then(() => {
                                btn.closest('tr').fadeOut();
                            });
                        }
                    },
                    error: function(xhr) {
                        btn.prop('disabled', false).html('<i class="fas fa-trash"></i>');
                        
                        if(xhr.status === 400) {
                            // Color has products - user-friendly error
                            const response = xhr.responseJSON;
                            Swal.fire({
                                icon: 'error',
                                title: 'Cannot Delete!',
                                html: `<p>${response.message}</p>
                                       <p class="mt-3"><strong>To delete this color:</strong></p>
                                       <ol class="text-start">
                                           <li>Go to Products section</li>
                                           <li>Change the color of products using this color</li>
                                           <li>Then try deleting again</li>
                                       </ol>`,
                                confirmButtonText: 'Got it'
                            });
                        } else {
                            Swal.fire({
                                icon: 'error',
                                title: 'Error!',
                                text: 'Failed to delete color. Please try again.',
                            });
                        }
                    }
                });
            }
        });
    });
</script>
@endpush
@endsection