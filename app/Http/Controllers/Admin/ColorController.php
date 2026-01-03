<?php
namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Color;
use Illuminate\Http\Request;

class ColorController extends Controller
{
    public function index()
    {
        $colors = Color::withCount('products')->paginate(20);
        return view('admin.colors.index', compact('colors'));
    }

    public function create()
    {
        return view('admin.colors.create');
    }

    public function store(Request $request)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:7|unique:colors,code',
        ]);

        Color::create([
            'name' => $request->name,
            'code' => $request->code,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Color created successfully'
        ]);
    }

    public function edit($id)
    {
        $color = Color::findOrFail($id);
        return view('admin.colors.edit', compact('color'));
    }

    public function update(Request $request, $id)
    {
        $request->validate([
            'name' => 'required|string|max:255',
            'code' => 'required|string|max:7|unique:colors,code,' . $id,
        ]);

        $color = Color::findOrFail($id);
        $color->update([
            'name' => $request->name,
            'code' => $request->code,
        ]);

        return response()->json([
            'success' => true,
            'message' => 'Color updated successfully'
        ]);
    }

   public function destroy($id)
{
    try {
        $color = Color::findOrFail($id);
        
        // Check if color has any products
        $productCount = $color->products()->count();
        
        if ($productCount > 0) {
            return response()->json([
                'success' => false,
                'message' => 'Cannot delete! This color has ' . $productCount . ' product(s). Please change the color of those products or delete them first.'
            ], 400);
        }
        
        // Safe to delete
        $color->delete();

        return response()->json([
            'success' => true,
            'message' => 'Color deleted successfully!'
        ]);
        
    } catch (\Exception $e) {
        return response()->json([
            'success' => false,
            'message' => 'Failed to delete color: ' . $e->getMessage()
        ], 500);
    }
}
}