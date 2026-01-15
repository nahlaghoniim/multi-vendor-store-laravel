<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Repositories\Cart\CartRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrdersController extends Controller

{
    public function index()
{
    $orders = Order::where('user_id', auth()->id())->latest()->get();
    return view('front.orders.index', compact('orders'));
}
     public function show(Order $order)
    {
        // Make sure user owns this order
        abort_if($order->user_id !== auth()->id(), 403);

        // Get delivery with the new column names
        $delivery = $order->delivery()
            ->select([
                'id',
                'order_id',
                'status',
                'latitude as lat',
                'longitude as lng',
                'created_at',
                'updated_at',
            ])
            ->first();

        return view('front.orders.show', [
            'order' => $order->load(['items.product', 'billingAddress', 'shippingAddress']),
            'delivery' => $delivery,
        ]);
    }

    /**
     * Create new order from cart
     */
    public function store(Request $request, CartRepository $cart)
    {
        $total = $cart->total();

        if ($total <= 0) {
            return back()->with('error', 'Your cart is empty');
        }

        DB::beginTransaction();

        try {

            $order = Order::create([
                'store_id' => 1, // or get from cart/products
                'user_id' => auth()->id(),
                'total' => $total,                     // ✅ CRITICAL
                'status' => 'pending',
                'payment_status' => 'unpaid',
            ]);

            // Move cart items to order_items table
            foreach ($cart->get() as $item) {

                if (!$item->product) {
                    continue;
                }

                $order->items()->create([
                    'product_id' => $item->product_id,
                    'product_name' => $item->product->name,
                    'price' => $item->product->price,
                    'quantity' => $item->quantity,
                    'options' => json_encode($item->options),
                ]);
            }

            // Empty cart
            $cart->empty();

            DB::commit();

            return redirect()->route('orders.payments.create', $order);

        } catch (\Throwable $e) {

            DB::rollBack();

            report($e);

            return back()->with('error', 'Failed to create order');

        }
    }
     public function tracking(Order $order)
    {
        // Make sure user owns this order
        abort_if($order->user_id !== auth()->id(), 403);

        // Refresh to get latest data
        $order = $order->fresh(['delivery']);

        // Get delivery with aliased column names for the view
        $delivery = null;
        
        if ($order->delivery) {
            $delivery = (object) [
                'id' => $order->delivery->id,
                'order_id' => $order->delivery->order_id,
                'status' => $order->delivery->status,
                'lat' => $order->delivery->latitude,
                'lng' => $order->delivery->longitude,
                'created_at' => $order->delivery->created_at,
                'updated_at' => $order->delivery->updated_at,
            ];
        }

        return view('front.orders.tracking', compact('order', 'delivery'));
    }
}
