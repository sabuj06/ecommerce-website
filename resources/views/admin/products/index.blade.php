@extends('admin.layouts.app')

@section('title', 'Manage Products')

@section('content')

<div class="d-flex justify-content-between align-items-center mb-4">
    <h2><i class="fas fa-palette"></i> Manage Products</h2>
    <a href="{{ route('admin.products.create') }}" class="btn btn-primary">
        <i class="fas fa-plus"></i> Add New Products
    </a>
</div>


<div class="table-responsive">
    <table class="table table-hover align-middle text-center">
        <thead class="table-light">
            <tr>
                <th>ID</th>
                <th>Image</th>
                <th>Name</th>
                <th>Category</th>
                <th>Brand</th>
                <th>Color</th>
                <th>Price</th>
                <th>Stock</th>
                <th>Created</th>
                <th>Actions</th>
            </tr>
        </thead>
        <tbody>
            @foreach($products as $product)
                <tr>
                    <td>{{ $product->id }}</td>

                    <td>
                        @if($product->image)
                            <img src="{{ asset('storage/' . $product->image) }}"
                                 alt="{{ $product->name }}"
                                 class="rounded"
                                 style="width:50px;height:50px;object-fit:cover;">
                        @else
                            <div class="bg-secondary rounded"
                                 style="width:50px;height:50px;margin:auto;"></div>
                        @endif
                    </td>

                    <td class="fw-semibold">{{ $product->name }}</td>

                    <td>
                        <span class="badge bg-secondary px-3">
                            {{ $product->category->name }}
                        </span>
                    </td>

                    <td>
                        <span class="badge bg-info text-dark px-3">
                            {{ $product->brand->name ?? 'No Brand' }}
                        </span>
                    </td>

                    <td>
                        <span class="badge bg-dark px-3">
                            {{ $product->color->name ?? 'No Color' }}
                        </span>
                    </td>

                    <td class="fw-bold text-success">
                        ₹{{ number_format($product->price, 2) }}
                    </td>

                    <td>
                        <span class="badge bg-{{ $product->stock > 10 ? 'success' : ($product->stock > 0 ? 'warning' : 'danger') }} px-3">
                            {{ $product->stock }}
                        </span>
                    </td>

                    <td>
                        {{ $product->created_at->format('d M, Y') }}
                    </td>

                    <td>
                        <div class="d-flex justify-content-center gap-1">
                            <a href="{{ route('admin.products.edit', $product->id) }}"
                               class="btn btn-sm btn-warning">
                                <i class="fas fa-edit"></i>
                            </a>
                            <button class="btn btn-sm btn-danger delete-product"
                                    data-id="{{ $product->id }}">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</div>
@endsection
