<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class OrderDataTableController extends Controller
{
    public function index()
    {
        return view('admin.orders.datatable');
    }

    public function getData()
    {
        $orders = Order::with('orderItems')->select('orders.*');

        return DataTables::eloquent($orders)
            ->addColumn('order_id', function ($order) {
                return '#' . $order->id . '';
            })
            ->addColumn('items_count', function ($order) {
                return '' . $order->orderItems->count() . ' items';
            })
            ->addColumn('amount', function ($order) {
                return '৳' . number_format($order->total_amount, 2) . '';
            })
            ->addColumn('status', function ($order) {
                $badges = [
                    'pending' => 'warning',
                    'processing' => 'info',
                    'completed' => 'success',
                    'cancelled' => 'danger'
                ];
                $badgeClass = $badges[$order->status] ?? 'secondary';
                return '' . ucfirst($order->status) . '';
            })
            ->addColumn('date', function ($order) {
                return $order->created_at->format('d M, Y h:i A');
            })
            ->addColumn('action', function ($order) {
                return '
                    
                         View
                    
                ';
            })
            ->rawColumns(['order_id', 'items_count', 'amount', 'status', 'action'])
            ->make(true);
    }
}
