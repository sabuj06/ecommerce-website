@extends('admin.layouts.app')

@section('title', 'Edit Product')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-edit"></i> Edit Product</h2>
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
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label class="form-label">Product Name *</label>
                        <input type="text" class="form-control" name="name" value="{{ $product->name }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Category *</label>
                        <select class="form-control" name="category_id" required>
                            <option value="">Select Category</option>
                            @foreach($categories as $category)
                                <option value="{{ $category->id }}" {{ $product->category_id == $category->id ? 'selected' : '' }}>
                                    {{ $category->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>
                    <div class="mb-2">
                        <label class="form-label">Brands *</label>
                        <select class="form-control" name="brand_id" required>
                            <option value="">Select Brand</option>
                            @foreach($brands as $brand)
                            <option value="{{ $brand->id }}"
                                 {{ isset($product) && $product->brand_id == $brand->id ? 'selected' : '' }}>
                                {{ $brand->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="mb-2">
                        <label class="form-label">Colors *</label>
                        <select class="form-control" name="colors_id" required>
                            <option value="">Select Brand</option>
                            @foreach($colors as $color)
                            <option value="{{ $color->id }}"
                                 {{ isset($product) && $product->color_id == $color->id ? 'selected' : '' }}>
                                {{ $color->name }}
                                </option>
                            @endforeach
                        </select>
                    </div>



                    <div class="row">
                        <div class="col-md-6 mb-3">
                            <label class="form-label">Price  ₹*</label>
                            <input type="number" class="form-control" name="price" value="{{ $product->price }}" step="0.01" min="0" required>
                        </div>

                        <div class="col-md-6 mb-3">
                            <label class="form-label">Stock Quantity *</label>
                            <input type="number" class="form-control" name="stock" value="{{ $product->stock }}" min="0" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description *</label>
                        <textarea class="form-control" name="description" rows="5" required>{{ $product->description }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Product Image</label>
                        @if($product->image)
                            <div class="mb-2">
                                <img src="{{ asset('storage/' . $product->image) }}" alt="{{ $product->name }}" class="img-thumbnail" style="max-width: 200px;">
                                <p class="text-muted small mt-1">Current Image</p>
                            </div>
                        @endif
                        <input type="file" class="form-control" name="image" accept="image/*" id="image-input">
                        <small class="text-muted">Leave empty to keep current image</small>
                    </div>

                    <div class="mb-3" id="image-preview-container" style="display: none;">
                        <label class="form-label">New Image Preview:</label><br>
                        <img id="image-preview" src="" alt="Preview" class="img-thumbnail" style="max-width: 200px;">
                    </div>

                    <button type="submit" class="btn btn-primary" id="submit-btn">
                        <i class="fas fa-save"></i> Update Product
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-info text-white">
                <h6 class="mb-0"><i class="fas fa-info-circle"></i> Product Info</h6>
            </div>
            <div class="card-body">
                <p><strong>Created:</strong> {{ $product->created_at->format('d M, Y') }}</p>
                <p><strong>Updated:</strong> {{ $product->updated_at->format('d M, Y') }}</p>
                <p><strong>Slug:</strong> <code>{{ $product->slug }}</code></p>
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
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Updating...');

        const formData = new FormData(this);

        $.ajax({
            url: '{{ route("admin.products.update", $product->id) }}',
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
                alert('Failed to update product. Please check all fields.');
                btn.prop('disabled', false).html('<i class="fas fa-save"></i> Update Product');
            }
        });
    });
</script>
@endpush
@endsection