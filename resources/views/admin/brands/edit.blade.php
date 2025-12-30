@extends('admin.layouts.app')

@section('title', 'Edit Brand')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-edit"></i> Edit Brand</h2>
    <a href="{{ route('admin.brands.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back
    </a>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <form id="brand-form" enctype="multipart/form-data">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label class="form-label">Brand Name *</label>
                        <input type="text" class="form-control" name="name" value="{{ $brand->name }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Description</label>
                        <textarea class="form-control" name="description" rows="4">{{ $brand->description }}</textarea>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Brand Logo</label>
                        @if($brand->logo)
                            <div class="mb-2">
                                <img src="{{ asset('storage/' . $brand->logo) }}" class="img-thumbnail" style="max-width: 200px;">
                            </div>
                        @endif
                        <input type="file" class="form-control" name="logo" accept="image/*" id="logo-input">
                    </div>

                    <div class="mb-3" id="logo-preview-container" style="display: none;">
                        <img id="logo-preview" src="" class="img-thumbnail" style="max-width: 200px;">
                    </div>

                    <div class="mb-3 form-check">
                        <input type="checkbox" class="form-check-input" id="is_active" name="is_active" {{ $brand->is_active ? 'checked' : '' }}>
                        <label class="form-check-label" for="is_active">Active</label>
                    </div>

                    <button type="submit" class="btn btn-primary" id="submit-btn">
                        <i class="fas fa-save"></i> Update Brand
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $('#logo-input').change(function() {
        const file = this.files[0];
        if (file) {
            const reader = new FileReader();
            reader.onload = function(e) {
                $('#logo-preview').attr('src', e.target.result);
                $('#logo-preview-container').show();
            }
            reader.readAsDataURL(file);
        }
    });

    $('#brand-form').submit(function(e) {
        e.preventDefault();
        const btn = $('#submit-btn');
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Updating...');

        $.ajax({
            url: '{{ route("admin.brands.update", $brand->id) }}',
            type: 'POST',
            data: new FormData(this),
            processData: false,
            contentType: false,
            success: function(response) {
                alert(response.message);
                window.location.href = '{{ route("admin.brands.index") }}';
            },
            error: function() {
                alert('Failed!');
                btn.prop('disabled', false).html('<i class="fas fa-save"></i> Update Brand');
            }
        });
    });
</script>
@endpush
@endsection