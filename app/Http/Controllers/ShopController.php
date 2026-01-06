<?php
namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    public function index()
    {
        $categories = Category::all();
        $products = Product::with('category')->paginate(12);
        return view('shop.index', compact('categories', 'products'));
    }

    public function category($slug)
    {
        $category = Category::where('slug', $slug)->firstOrFail();
        $categories = Category::all();
        $products = Product::where('category_id', $category->id)->paginate(12);
        return view('shop.category', compact('category', 'categories', 'products'));
    }

    public function show($slug)
    {
        $product = Product::where('slug', $slug)->firstOrFail();
        return view('shop.product', compact('product'));
    }

    public function search(Request $request)
    {
        $query = $request->input('q');
        $products = Product::where('name', 'LIKE', "%{$query}%")
            ->orWhere('description', 'LIKE', "%{$query}%")
            ->paginate(12);
        $categories = Category::all();
        return view('shop.search', compact('products', 'categories', 'query'));
    }

    // Live Search API for AJAX
    public function liveSearch(Request $request)
    {
        $query = $request->input('q');
        
        if (empty($query) || strlen($query) < 2) {
            return response()->json([]);
        }

        $products = Product::where('name', 'LIKE', "%{$query}%")
            ->orWhere('description', 'LIKE', "%{$query}%")
            ->where('is_active', 1)
            ->limit(8)
            ->get(['id', 'name', 'slug', 'price', 'image']);

        return response()->json($products);
    }

    // Brand Products
    public function brand($slug)
    {
        $brand = \App\Models\Brand::where('slug', $slug)->firstOrFail();
        $categories = Category::all();
        $brands = \App\Models\Brand::where('is_active', true)->get();
        $products = Product::where('brand_id', $brand->id)->paginate(12);
        
        return view('shop.brand', compact('brand', 'categories', 'brands', 'products'));
    }
}