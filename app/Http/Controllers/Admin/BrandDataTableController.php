<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Brand;
use Illuminate\Http\Request;
use Yajra\DataTables\Facades\DataTables;

class BrandDataTableController extends Controller
{
    public function index()
    {
        return view('admin.brands.datatable'); // Blade view
    }

    public function getData()
    {
        $brands = Brand::withCount('products')->select('brands.*');

        return DataTables::eloquent($brands)
            ->addColumn('logo', function ($brand) {
                if ($brand->logo) {
                    // এখানে HTML হিসেবে image generate করো
                    return '<img src="' . asset('storage/' . $brand->logo) . '" style="width:50px;height:50px;object-fit:contain;">';
                }
                return '<i class="fas fa-tag fa-2x text-muted"></i>';
            })
            ->addColumn('products_count', function ($brand) {
                return '<span class="badge bg-primary">' . $brand->products_count . ' Products</span>';
            })
            ->addColumn('status', function ($brand) {
                $badgeClass = $brand->is_active ? 'success' : 'secondary';
                $status = $brand->is_active ? 'Active' : 'Inactive';
                return '<span class="badge bg-' . $badgeClass . '">' . $status . '</span>';
            })
            ->addColumn('action', function ($brand) {
                return '
                    <a href="' . route("admin.brands.edit", $brand->id) . '" class="btn btn-sm btn-warning">
                        <i class="fas fa-edit"></i>
                    </a>
                    <button class="btn btn-sm btn-danger delete-brand" data-id="' . $brand->id . '">
                        <i class="fas fa-trash"></i>
                    </button>
                ';
            })
            ->rawColumns(['logo', 'products_count', 'status', 'action']) // HTML render করার জন্য
            ->make(true);
    }
}
