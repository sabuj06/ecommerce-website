@extends('admin.layouts.app')

@section('title', 'Edit Category')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-edit"></i> Edit Category</h2>
    <a href="{{ route('admin.categories.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back to Categories
    </a>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <form id="category-form">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label class="form-label">Category Name *</label>
                        <input type="text" class="form-control" name="name" value="{{ $category->name }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" rows="4">{{ $category->description }}</textarea>
                    </div>

                    <button type="submit" class="btn btn-primary" id="submit-btn">
                        <i class="fas fa-save"></i> Update Category
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $('#category-form').submit(function(e) {
        e.preventDefault();
        
        const btn = $('#submit-btn');
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Updating...');

        $.ajax({
            url: '{{ route("admin.categories.update", $category->id) }}',
            type: 'PUT',
            data: $(this).serialize(),
            success: function(response) {
                if(response.success) {
                    alert(response.message);
                    window.location.href = '{{ route("admin.categories.index") }}';
                }
            },
            error: function() {
                alert('Failed to update category');
                btn.prop('disabled', false).html('<i class="fas fa-save"></i> Update Category');
            }
        });
    });
</script>
@endpush
@endsection