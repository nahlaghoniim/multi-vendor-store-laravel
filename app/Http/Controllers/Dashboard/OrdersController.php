<?php

namespace App\Http\Controllers\Dashboard;

use App\Http\Controllers\Controller;
use App\Models\Order;
use Illuminate\Http\Request;

class OrdersController extends Controller
{
    public function index(Request $request)
    {
        $this->authorize('view-any', Order::class);

        $orders = Order::with(['user', 'store'])
            ->when($request->number, fn($q) =>
                $q->where('number', 'like', '%' . $request->number . '%')
            )
            ->when($request->status, fn($q) =>
                $q->where('status', $request->status)
            )
            ->latest()
            ->paginate(15, ['*'], 'page', $request->query('page', 1))
            ->appends($request->query());

        return view('dashboard.orders.index', compact('orders'));
    }

    public function show(Order $order)
    {
        $this->authorize('view', $order);

        $order->load([
            'items.product',
            'user',
            'billingAddress',
            'shippingAddress',
            'payments',
            'delivery'
        ]);

        return view('dashboard.orders.show', compact('order'));
    }

    public function updateStatus(Request $request, Order $order)
    {
        $this->authorize('update', $order);

        $request->validate([
            'status' => 'required|in:pending,processing,shipped,completed,cancelled'
        ]);

        $order->update([
            'status' => $request->status
        ]);

        return back()->with('success', 'Order status updated');
    }
}
