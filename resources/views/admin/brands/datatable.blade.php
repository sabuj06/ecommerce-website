@extends('admin.layouts.app')

@section('title', 'Brands - DataTable')

@section('content')
<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-tag"></i> Brands - DataTable</h2>
    <div>
        <a href="{{ route('admin.brands.index') }}" class="btn btn-secondary me-2">
            <i class="fas fa-list"></i> Normal View
        </a>
        <a href="{{ route('admin.brands.create') }}" class="btn btn-primary">
            <i class="fas fa-plus"></i> Add New Brand
        </a>
    </div>
</div>

<div class="card">
    <div class="card-body">
        <div class="table-responsive">
            <table class="table table-bordered table-hover" id="brands-table" style="width:100%">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Logo</th>
                        <th>Name</th>
                        <th>Slug</th>
                        <th>Products</th>
                        <th>Status</th>
                        <th>Created</th>
                        <th>Actions</th>
                    </tr>
                </thead>
            </table>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<link rel="stylesheet" href="https://cdn.datatables.net/1.13.6/css/dataTables.bootstrap5.min.css">
<link rel="stylesheet" href="https://cdn.datatables.net/buttons/2.4.1/css/buttons.bootstrap5.min.css">

<script src="https://cdn.datatables.net/1.13.6/js/jquery.dataTables.min.js"></script>
<script src="https://cdn.datatables.net/1.13.6/js/dataTables.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/dataTables.buttons.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.bootstrap5.min.js"></script>
<script src="https://cdn.datatables.net/buttons/2.4.1/js/buttons.html5.min.js"></script>

<script>
$(document).ready(function() {
    var table = $('#brands-table').DataTable({
        processing: true,
        serverSide: true,
        ajax: '{{ route("admin.brands.datatable.data") }}',
        columns: [
            { data: 'id', name: 'id' },
            { data: 'logo', name: 'logo', orderable: false, searchable: false },
            { data: 'name', name: 'name' },
            { data: 'slug', name: 'slug' },
            { data: 'products_count', name: 'products_count' },
            { data: 'status', name: 'is_active' },
            { data: 'created_at', name: 'created_at' },
            { data: 'action', name: 'action', orderable: false, searchable: false }
        ],
        order: [[0, 'desc']],
        dom: 'Bfrtip',
        buttons: ['copy', 'excel', 'pdf', 'print'],
        pageLength: 25
    });

    // Delete brand
    $('#brands-table').on('click', '.delete-brand', function() {
        if(!confirm('Are you sure?')) return;
        const btn = $(this);
        const brandId = btn.data('id');
        btn.prop('disabled', true).html('<i class="fas fa-spinner fa-spin"></i>');

        $.ajax({
            url: '/admin/brands/' + brandId,
            type: 'DELETE',
            success: function(response) {
                if(response.success) {
                    alert(response.message);
                    table.ajax.reload();
                } else {
                    alert(response.message);
                    btn.prop('disabled', false).html('<i class="fas fa-trash"></i>');
                }
            }
        });
    });
});
</script>
@endpush