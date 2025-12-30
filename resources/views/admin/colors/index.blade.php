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
        if(!confirm('Are you sure?')) return;
        const btn = $(this);
        const colorId = btn.data('id');
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');

        $.ajax({
            url: '/admin/colors/' + colorId,
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