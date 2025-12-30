@extends('admin.layouts.app')

@section('title', 'Edit Color')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-edit"></i> Edit Color</h2>
    <a href="{{ route('admin.colors.index') }}" class="btn btn-secondary">
        <i class="fas fa-arrow-left"></i> Back
    </a>
</div>

<div class="row">
    <div class="col-md-8">
        <div class="card">
            <div class="card-body">
                <form id="color-form">
                    @csrf
                    @method('PUT')
                    
                    <div class="mb-3">
                        <label class="form-label">Color Name *</label>
                        <input type="text" class="form-control" name="name" value="{{ $color->name }}" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Hex Color Code *</label>
                        <div class="input-group">
                            <input type="color" class="form-control form-control-color" id="color-picker" value="{{ $color->code }}">
                            <input type="text" class="form-control" name="code" id="color-code" value="{{ $color->code }}" required>
                        </div>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Preview</label>
                        <div id="color-preview" style="width: 100px; height: 100px; background-color: {{ $color->code }}; border: 2px solid #ddd; border-radius: 10px;"></div>
                    </div>

                    <button type="submit" class="btn btn-primary" id="submit-btn">
                        <i class="fas fa-save"></i> Update Color
                    </button>
                </form>
            </div>
        </div>
    </div>
</div>

@push('scripts')
<script>
    $('#color-picker').on('input', function() {
        const color = $(this).val().toUpperCase();
        $('#color-code').val(color);
        $('#color-preview').css('background-color', color);
    });

    $('#color-code').on('input', function() {
        const color = $(this).val();
        if(/^#[0-9A-F]{6}$/i.test(color)) {
            $('#color-picker').val(color);
            $('#color-preview').css('background-color', color);
        }
    });

    $('#color-form').submit(function(e) {
        e.preventDefault();
        const btn = $('#submit-btn');
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Updating...');

        $.ajax({
            url: '{{ route("admin.colors.update", $color->id) }}',
            type: 'PUT',
            data: $(this).serialize(),
            success: function(response) {
                alert(response.message);
                window.location.href = '{{ route("admin.colors.index") }}';
            },
            error: function() {
                alert('Failed!');
                btn.prop('disabled', false).html('<i class="fas fa-save"></i> Update Color');
            }
        });
    });
</script>
@endpush
@endsection