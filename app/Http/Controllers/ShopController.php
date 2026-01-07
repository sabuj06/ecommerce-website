<?php

namespace App\Http\Controllers;

use App\Models\Category;
use App\Models\Product;
use App\Models\Brand;
use Illuminate\Http\Request;

class ShopController extends Controller
{
    /**
     * Display all products with optional price filter
     */
    public function index(Request $request)
    {
        $categories = Category::where('is_active', true)->get();
        
        $query = Product::where('is_active', true)
                        ->with(['category', 'brand']);
        
        // Apply price filter
        $query = $this->applyPriceFilter($query, $request);
        
        $products = $query->paginate(12);
        
        return view('shop.index', compact('categories', 'products'));
    }

    /**
     * Display products by category with optional price filter
     */
    public function category(Request $request, $slug)
    {
        $currentCategory = Category::where('slug', $slug)
                                   ->where('is_active', true)
                                   ->firstOrFail();
        
        $categories = Category::where('is_active', true)->get();
        
        $query = Product::where('category_id', $currentCategory->id)
                        ->where('is_active', true)
                        ->with(['category', 'brand']);
        
        // Apply price filter
        $query = $this->applyPriceFilter($query, $request);
        
        $products = $query->paginate(12);
        
        return view('shop.index', compact('categories', 'products', 'currentCategory'));
    }

    /**
     * Display products by brand with optional price filter
     */
    public function brand(Request $request, $slug)
    {
        $currentBrand = Brand::where('slug', $slug)
                            ->where('is_active', true)
                            ->firstOrFail();
        
        $categories = Category::where('is_active', true)->get();
        
        $query = Product::where('brand_id', $currentBrand->id)
                        ->where('is_active', true)
                        ->with(['category', 'brand']);
        
        // Apply price filter
        $query = $this->applyPriceFilter($query, $request);
        
        $products = $query->paginate(12);
        
        return view('shop.index', compact('categories', 'products', 'currentBrand'));
    }

    /**
     * Display products by category AND brand with optional price filter
     */
    public function categoryBrand(Request $request, $categorySlug, $brandSlug)
    {
        $currentCategory = Category::where('slug', $categorySlug)
                                   ->where('is_active', true)
                                   ->firstOrFail();
        
        $currentBrand = Brand::where('slug', $brandSlug)
                            ->where('is_active', true)
                            ->firstOrFail();
        
        $categories = Category::where('is_active', true)->get();
        
        $query = Product::where('category_id', $currentCategory->id)
                        ->where('brand_id', $currentBrand->id)
                        ->where('is_active', true)
                        ->with(['category', 'brand']);
        
        // Apply price filter
        $query = $this->applyPriceFilter($query, $request);
        
        $products = $query->paginate(12);
        
        return view('shop.index', compact('categories', 'products', 'currentCategory', 'currentBrand'));
    }

    /**
     * Apply price range filter to query
     */
    private function applyPriceFilter($query, Request $request)
    {
        $minPrice = $request->input('min_price');
        $maxPrice = $request->input('max_price');
        
        if ($minPrice !== null && $maxPrice !== null) {
            // Range: min to max
            $query->whereBetween('price', [$minPrice, $maxPrice]);
        } elseif ($minPrice !== null) {
            // Above min price
            $query->where('price', '>=', $minPrice);
        } elseif ($maxPrice !== null) {
            // Below max price
            $query->where('price', '<=', $maxPrice);
        }
        
        return $query;
    }

    /**
     * Display single product details
     */
    public function show($slug)
    {
        $product = Product::where('slug', $slug)
                         ->where('is_active', true)
                         ->with(['category', 'brand'])
                         ->firstOrFail();
        
        return view('shop.product', compact('product'));
    }

    /**
     * Search products with optional price filter
     */
    public function search(Request $request)
    {
        $query = $request->input('q');
        
        $productsQuery = Product::where(function($q) use ($query) {
                               $q->where('name', 'LIKE', "%{$query}%")
                                 ->orWhere('description', 'LIKE', "%{$query}%");
                           })
                           ->where('is_active', true)
                           ->with(['category', 'brand']);
        
        // Apply price filter
        $productsQuery = $this->applyPriceFilter($productsQuery, $request);
        
        $products = $productsQuery->paginate(12);
        $categories = Category::where('is_active', true)->get();
        
        return view('shop.search', compact('products', 'categories', 'query'));
    }

    /**
     * Live search API for AJAX
     */
    public function liveSearch(Request $request)
    {
        $query = $request->input('q');
        
        if (empty($query) || strlen($query) < 2) {
            return response()->json([]);
        }

        $products = Product::where(function($q) use ($query) {
                               $q->where('name', 'LIKE', "%{$query}%")
                                 ->orWhere('description', 'LIKE', "%{$query}%");
                           })
                           ->where('is_active', true)
                           ->limit(8)
                           ->get(['id', 'name', 'slug', 'price', 'image']);
        
        return response()->json($products);
    }
}