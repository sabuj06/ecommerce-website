@extends('admin.layouts.app')

@section('title', 'Create Product')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-plus"></i> Create New Product</h2>
    <a href="{{ route('admin.products.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back to Products
    </a>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <form id="product-form" enctype="multipart/form-data">
                    @csrf
                    
                    <div class="mb-3">
                        <label class="form-label">Product Name *</label>
                        <input type="text" class="form-control" name="name" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Category *</label>
                        <select class="form-control" name="category_id" required>
                            <option value="">Select Category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}">{{ $category->name }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Price (৳) *</label>
                            <input type="number" class="form-control" name="price" step="0.01" min="0" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Stock Quantity *</label>
                            <input type="number" class="form-control" name="stock" min="0" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description *</label>
                        <textarea class="form-control" name="description" rows="5" required></textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Product Image</label>
                        <input type="file" class="form-control" name="image" accept="image/*" id="image-input">
                        <small class="text-muted">Supported: JPG, PNG, GIF (Max: 2MB)</small>
                    </div>

                    <div class="mb-3" id="image-preview-container" style="display: none;">
                        <img id="image-preview" src="" alt="Preview" class="img-thumbnail" style="max-width: 200px;">
                    </div>

                    <button type="submit" class="btn btn-primary" id="submit-btn">
                        <i class="fas fa-save"></i> Create Product
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-info text-white">
                <h6 class="mb-0"><i class="fas fa-info-circle"></i> Tips</h6>
            </div>
            <div class="card-body">
                <ul class="mb-0">
                    <li>Use clear product names</li>
                    <li>Select appropriate category</li>
                    <li>Set competitive prices</li>
                    <li>Maintain accurate stock</li>
                    <li>Upload high-quality images</li>
                </ul>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    // Image Preview
    $('#image-input').change(function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#image-preview').attr('src', e.target.result);
                $('#image-preview-container').show();
            }
            reader.readAsDataURL(file);
        }
    });

    // Form Submit
    $('#product-form').submit(function(e) {
        e.preventDefault();
        
        const btn = $('#submit-btn');
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Creating...');

        const formData = new FormData(this);

        $.ajax({
            url: '{{ route("admin.products.store") }}',
            type: 'POST',
            data: formData,
            processData: false,
            contentType: false,
            success: function(response) {
                if(response.success) {
                    alert(response.message);
                    window.location.href = '{{ route("admin.products.index") }}';
                }
            },
            error: function(xhr) {
                alert('Failed to create product. Please check all fields.');
                btn.prop('disabled', false).html('<i class="fas fa-save"></i> Create Product');
            }
        });
    });
</script>
@endpush
@endsection