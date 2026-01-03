<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Category;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

class CategoryController extends Controller
{
    // List all categories with products count
    public function index()
    {
        $categories = Category::withCount('products')->paginate(10);
        return view('admin.categories.index', compact('categories'));
    }

    // Show create category form
    public function create()
    {
        return view('admin.categories.create');
    }

    // Store new category
    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        // Generate unique slug
        $slug = Str::slug($request->name);
        $count = Category::where('slug', 'like', $slug.'%')->count();
        if($count > 0) {
            $slug .= '-' . ($count + 1);
        }

        Category::create([
            'name' => $request->name,
            'slug' => $slug,
            'description' => $request->description,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Category created successfully'
        ]);
    }

    // Show edit category form
    public function edit($id)
    {
        $category = Category::findOrFail($id);
        return view('admin.categories.edit', compact('category'));
    }

    // Update existing category
    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'description' => 'nullable|string',
        ]);

        $category = Category::findOrFail($id);

        // Generate unique slug
        $slug = Str::slug($request->name);
        $count = Category::where('slug', 'like', $slug.'%')
                         ->where('id', '!=', $id)
                         ->count();
        if($count > 0) {
            $slug .= '-' . ($count + 1);
        }

        $category->update([
            'name' => $request->name,
            'slug' => $slug,
            'description' => $request->description,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Category updated successfully'
        ]);
    }

    // Delete category
  public function destroy($id)
{
    try {
        $category = Category::findOrFail($id);
        
        // Check if category has any products
        $productCount = $category->products()->count();
        
        if ($productCount > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete! This category has ' . $productCount . ' product(s). Please move or delete those products first.'
            ], 400);
        }
        
        // Safe to delete
        $category->delete();

        return response()->json([
            'success' => true,
            'message' => 'Category deleted successfully!'
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to delete category: ' . $e->getMessage()
        ], 500);
    }
}
}



