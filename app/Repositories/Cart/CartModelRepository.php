<?php

namespace App\Repositories\Cart;

use App\Models\Cart;
use App\Models\Product;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Log;

class CartModelRepository implements CartRepository
{
    protected $items;

    public function __construct()
    {
        $this->items = collect([]);
    }

    public function get() : Collection
    {
        if (!$this->items->count()) {
            // ✅ Always eager load product
            $this->items = Cart::with('product')->get();
        }

        return $this->items;
    }

    public function add(Product $product, $quantity = 1)
    {
        // ✅ Ensure we filter by cookie_id AND product_id
        $item = Cart::where('cookie_id', Cart::getCookieId())
            ->where('product_id', $product->id)
            ->first();

        if (!$item) {
            // ✅ Save cookie_id when creating cart row
            $cart = Cart::create([
                'cookie_id'  => Cart::getCookieId(),
                'user_id'    => Auth::id(),
                'product_id' => $product->id,
                'quantity'   => $quantity,
            ]);

            $this->get()->push($cart);
            return $cart;
        }

        $item->increment('quantity', $quantity);
        return $item;
    }

    public function update($id, $quantity)
    {
        Cart::where('id', $id)
            ->update([
                'quantity' => $quantity,
            ]);
    }

    public function delete($id)
    {
        Cart::where('id', $id)->delete();
    }

    public function empty()
    {
        Cart::query()->delete();
    }

    public function total() : float
    {
        // ✅ Calculate using product relationship
        return $this->get()->sum(function ($item) {
            if (!$item->product) {
                 Log::warning("Cart item {$item->id} has no product loaded");
                return 0;
            }
            return $item->quantity * $item->product->price;
        });
    }
}
