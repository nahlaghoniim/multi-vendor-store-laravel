<?php

namespace App\Http\Controllers\Front;

use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Repositories\Cart\CartRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class OrdersController extends Controller
{
    public function show(Order $order)
    {
        $delivery = $order->delivery()->select([
            'id',
            'order_id',
            'status',
            DB::raw("ST_Y(current_location) AS lat"),
            DB::raw("ST_X(current_location) AS lng"),
        ])->first();

        return view('front.orders.show', [
            'order' => $order,
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
}
