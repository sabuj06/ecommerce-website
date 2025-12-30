@extends('admin.layouts.app')

@section('title', 'Create Color')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-plus"></i> Create New Color</h2>
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
                    
                    <div class="mb-3">
                        <label class="form-label">Color Name *</label>
                        <input type="text" class="form-control" name="name" placeholder="e.g. Red" required>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Hex Color Code *</label>
                        <div class="input-group">
                            <input type="color" class="form-control form-control-color" id="color-picker" value="#000000">
                            <input type="text" class="form-control" name="code" id="color-code" value="#000000" placeholder="#FF0000" required>
                        </div>
                        <small class="text-muted">Select color or enter hex code</small>
                    </div>

                    <div class="mb-3">
                        <label class="form-label">Preview</label>
                        <div id="color-preview" style="width: 100px; height: 100px; background-color: #000000; border: 2px solid #ddd; border-radius: 10px;"></div>
                    </div>

                    <button type="submit" class="btn btn-primary" id="submit-btn">
                        <i class="fas fa-save"></i> Create Color
                    </button>
                </form>
            </div>
        </div>
    </div>

    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-info text-white">
                <h6 class="mb-0"><i class="fas fa-info-circle"></i> Popular Colors</h6>
            </div>
            <div class="card-body">
                <div class="row g-2">
                    <div class="col-3"><div class="color-suggestion" data-name="Red" data-code="#FF0000" style="width:100%; height:50px; background:#FF0000; cursor:pointer; border-radius:5px;" title="Red"></div></div>
                    <div class="col-3"><div class="color-suggestion" data-name="Blue" data-code="#0000FF" style="width:100%; height:50px; background:#0000FF; cursor:pointer; border-radius:5px;" title="Blue"></div></div>
                    <div class="col-3"><div class="color-suggestion" data-name="Green" data-code="#00FF00" style="width:100%; height:50px; background:#00FF00; cursor:pointer; border-radius:5px;" title="Green"></div></div>
                    <div class="col-3"><div class="color-suggestion" data-name="Yellow" data-code="#FFFF00" style="width:100%; height:50px; background:#FFFF00; cursor:pointer; border-radius:5px;" title="Yellow"></div></div>
                    <div class="col-3"><div class="color-suggestion" data-name="Black" data-code="#000000" style="width:100%; height:50px; background:#000000; cursor:pointer; border-radius:5px;" title="Black"></div></div>
                    <div class="col-3"><div class="color-suggestion" data-name="White" data-code="#FFFFFF" style="width:100%; height:50px; background:#FFFFFF; cursor:pointer; border-radius:5px; border:1px solid #ddd;" title="White"></div></div>
                    <div class="col-3"><div class="color-suggestion" data-name="Purple" data-code="#800080" style="width:100%; height:50px; background:#800080; cursor:pointer; border-radius:5px;" title="Purple"></div></div>
                    <div class="col-3"><div class="color-suggestion" data-name="Orange" data-code="#FFA500" style="width:100%; height:50px; background:#FFA500; cursor:pointer; border-radius:5px;" title="Orange"></div></div>
                </div>
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

    $('.color-suggestion').click(function() {
        const name = $(this).data('name');
        const code = $(this).data('code');
        $('input[name="name"]').val(name);
        $('#color-picker').val(code);
        $('#color-code').val(code);
        $('#color-preview').css('background-color', code);
    });

    $('#color-form').submit(function(e) {
        e.preventDefault();
        const btn = $('#submit-btn');
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i> Creating...');

        $.post('{{ route("admin.colors.store") }}', $(this).serialize(), function(response) {
            alert(response.message);
            window.location.href = '{{ route("admin.colors.index") }}';
        }).fail(function() {
            alert('Failed!');
            btn.prop('disabled', false).html('<i class="fas fa-save"></i> Create Color');
        });
    });
</script>
@endpush
@endsection