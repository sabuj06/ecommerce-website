<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Product;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class ProductDataTableController extends Controller
{
    public function index()
    {
        return view('admin.products.datatable');
    }

    public function getData()
    {
        $products = Product::with(['category', 'brand', 'color'])
            ->select('products.*');

        return DataTables::eloquent($products)
            ->addColumn('image', function ($product) {
                if ($product->image) {
                    return '<img src="' . asset('storage/' . $product->image) . '" alt="' . $product->name . '" style="width: 50px; height: 50px; object-fit: cover; border-radius: 5px;">';
                }
                return '<div style="width: 50px; height: 50px; background: #ddd; border-radius: 5px;"></div>';
            })
            ->addColumn('category_name', function ($product) {
                return $product->category ? $product->category->name : '-';
            })
            ->addColumn('brand_name', function ($product) {
                return $product->brand ? '<span class="badge bg-info">' . $product->brand->name . '</span>' : '-';
            })
            ->addColumn('color_name', function ($product) {
                if ($product->color) {
                    return '<span class="badge bg-dark">' . $product->color->name . '</span>';
                }
                return '-';
            })
            ->addColumn('price_formatted', function ($product) {
                return '₹' . number_format($product->price, 2);
            })
            ->addColumn('stock_status', function ($product) {
                $badgeClass = $product->stock > 10 ? 'success' : ($product->stock > 0 ? 'warning' : 'danger');
                return '<span class="badge bg-' . $badgeClass . '">' . $product->stock . '</span>';
            })
            ->addColumn('action', function ($product) {
                return '
                    <a href="' . route('admin.products.edit', $product->id) . '" class="btn btn-sm btn-warning">
                        <i class="fas fa-edit"></i>
                    </a>
                    <button class="btn btn-sm btn-danger delete-product" data-id="' . $product->id . '">
                        <i class="fas fa-trash"></i>
                    </button>
                ';
            })
            ->rawColumns(['image', 'brand_name', 'color_name', 'stock_status', 'action'])
            ->make(true);
    }
}