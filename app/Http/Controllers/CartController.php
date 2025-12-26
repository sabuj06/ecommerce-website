<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Cart;
use App\Models\Product;

class CartController extends Controller
{
    /**
     * Get or create a unique session ID for the cart.
     */
    private function getSessionId()
    {
        if (!session()->has('cart_session')) {
            session(['cart_session' => uniqid('cart_', true)]);
        }
        return session('cart_session');
    }

    /**
     * Display cart items.
     */
    public function index()
    {
        $sessionId = $this->getSessionId();
        $cartItems = Cart::where('session_id', $sessionId)->with('product')->get();
        $total = $cartItems->sum(function($item) {
            return $item->product->price * $item->quantity;
        });
        return view('cart.index', compact('cartItems', 'total'));
    }

    /**
     * Add a product to the cart.
     */
    public function add(Request $request)
    {
        $sessionId = $this->getSessionId();
        $productId = $request->product_id;
        $quantity = $request->quantity ?? 1;

        $product = Product::findOrFail($productId);

        $cartItem = Cart::where('session_id', $sessionId)
            ->where('product_id', $productId)
            ->first();

        if ($cartItem) {
            $cartItem->quantity += $quantity;
            $cartItem->save();
        } else {
            Cart::create([
                'session_id' => $sessionId,
                'product_id' => $productId,
                'quantity' => $quantity
            ]);
        }

        $cartCount = Cart::where('session_id', $sessionId)->sum('quantity');

        return response()->json([
            'success' => true,
            'message' => 'Product added to cart',
            'cartCount' => $cartCount
        ]);
    }

    /**
     * Update cart item quantity.
     */
    public function update(Request $request)
    {
        $cartItem = Cart::findOrFail($request->cart_id);
        $cartItem->quantity = $request->quantity;
        $cartItem->save();

        $sessionId = $this->getSessionId();
        $cartItems = Cart::where('session_id', $sessionId)->with('product')->get();
        $total = $cartItems->sum(function($item) {
            return $item->product->price * $item->quantity;
        });

        return response()->json([
            'success' => true,
            'total' => number_format($total, 2)
        ]);
    }

    /**
     * Remove an item from the cart.
     */
    public function remove(Request $request)
    {
        $cartItem = Cart::findOrFail($request->cart_id);
        $cartItem->delete();

        $sessionId = $this->getSessionId();
        $cartCount = Cart::where('session_id', $sessionId)->sum('quantity');
        $cartItems = Cart::where('session_id', $sessionId)->with('product')->get();
        $total = $cartItems->sum(function($item) {
            return $item->product->price * $item->quantity;
        });

        return response()->json([
            'success' => true,
            'cartCount' => $cartCount,
            'total' => number_format($total, 2)
        ]);
    }

    /**
     * Get total cart item count.
     */
    public function count()
    {
        $sessionId = $this->getSessionId();
        $count = Cart::where('session_id', $sessionId)->sum('quantity');
        return response()->json(['count' => $count]);
    }
}
