<?php

namespace App\Http\Controllers\Front;

use App\Events\OrderCreated;
use App\Exceptions\InvalidOrderException;
use App\Http\Controllers\Controller;
use App\Models\Order;
use App\Models\OrderItem;
use App\Repositories\Cart\CartRepository;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Log;
use Symfony\Component\Intl\Countries;
use Throwable;

class CheckoutController extends Controller
{
    public function create(CartRepository $cart)
    {
        if ($cart->get()->count() == 0) {
            throw new InvalidOrderException('Cart is empty');
        }

        return view('front.checkout', [
            'cart' => $cart,
            'countries' => Countries::getNames(),
        ]);
    }

   public function store(Request $request, CartRepository $cart)
{
    $request->validate([
        'addr.billing.first_name'   => 'required|string|max:255',
        'addr.billing.last_name'    => 'required|string|max:255',
        'addr.billing.email'        => 'required|string|max:255',
        'addr.billing.phone_number' => 'required|string|max:255',
        'addr.billing.city'         => 'required|string|max:255',
    ]);

    // If shipping address is empty, copy billing
    if (empty($request->input('addr.shipping.first_name'))) {
        $request->merge([
            'addr' => [
                'billing'  => $request->input('addr.billing'),
                'shipping' => $request->input('addr.billing'),
            ],
        ]);
    }

    $items = $cart->get()
        ->filter(fn($item) => $item->product && $item->product->store_id)
        ->groupBy('product.store_id');

    DB::beginTransaction();

    try {
        $orders = []; 

        foreach ($items as $store_id => $cart_items) {
            $subtotal = 0;

            foreach ($cart_items as $item) {
                if (!$item->product || !$item->product->price) {
                    Log::error("Missing product or price for cart item {$item->id}");
                    continue;
                }

                $subtotal += $item->product->price * $item->quantity;
            }

            Log::info("Subtotal for store {$store_id}: {$subtotal}");

            $shipping = 0;
            $tax = 0;
            $discount = 0;

            $total = $subtotal + $shipping + $tax - $discount;

            if ($total <= 0) {
                throw new \Exception("Invalid order total for store {$store_id}");
            }

            $order = Order::create([
                'store_id'       => $store_id,
                'user_id'        => Auth::id(),
                'payment_method' => 'stripe',
                'shipping'       => $shipping,
                'tax'            => $tax,
                'discount'       => $discount,
                'total'          => $total,
                'status'         => 'pending',
                'payment_status' => 'pending',
            ]);

            foreach ($cart_items as $item) {
                OrderItem::create([
                    'order_id'     => $order->id,
                    'product_id'   => $item->product_id,
                    'product_name' => $item->product->name,
                    'price'        => $item->product->price,
                    'quantity'     => $item->quantity,
                ]);
            }

            foreach ($request->post('addr') as $type => $address) {
                $address['type'] = $type;
                $order->addresses()->create($address);
            }

           
            $orders[] = $order; 
        }

        DB::commit();

        
        if (!empty($orders)) {
            event(new OrderCreated($orders[0]));
        }

       

    } catch (Throwable $e) {
        DB::rollBack();
        throw $e;
    }

    // Redirect to payment page for the last order created
    return redirect()->route('orders.payments.create', $orders[count($orders) - 1]);
}
}
