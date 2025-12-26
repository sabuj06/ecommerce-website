@extends('layouts.app')

@section('title', 'Order Success')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body text-center py-5">
                    <!-- Success Icon -->
                    <i class="fas fa-check-circle text-success" style="font-size: 80px;"></i>
                    <h2 class="mt-4">Order Placed Successfully!</h2>
                    <p class="lead">Thank you for your order.</p>
                    <p>Order ID: <strong>#{{ $order->id }}</strong></p>

                    <hr class="my-4">

                    <!-- Order Details -->
                    <div class="text-start">
                        <h4>Order Details</h4>
                        <p><strong>Name:</strong> {{ $order->customer_name }}</p>
                        <p><strong>Email:</strong> {{ $order->customer_email }}</p>
                        <p><strong>Phone:</strong> {{ $order->customer_phone }}</p>
                        <p><strong>Address:</strong> {{ $order->customer_address }}</p>

                        <h5 class="mt-4">Ordered Items:</h5>
                        <table class="table">
                            <thead>
                                <tr>
                                    <th>Product</th>
                                    <th>Quantity</th>
                                    <th>Price</th>
                                    <th>Subtotal</th>
                                </tr>
                            </thead>
                            <tbody>
                                @foreach($order->orderItems as $item)
                                    <tr>
                                        <td>{{ $item->product->name }}</td>
                                        <td>{{ $item->quantity }}</td>
                                        <td>৳{{ number_format($item->price, 2) }}</td>
                                        <td>৳{{ number_format($item->price * $item->quantity, 2) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                            <tfoot>
                                <tr>
                                    <th colspan="3" class="text-end">Total:</th>
                                    <th>৳{{ number_format($order->total_amount, 2) }}</th>
                                </tr>
                            </tfoot>
                        </table>
                    </div>

                    <a href="{{ route('shop.index') }}" class="btn btn-primary btn-lg mt-4">
                        Continue Shopping
                    </a>
                </div>
            </div>
        </div>
    </div>
</div>
@endsection