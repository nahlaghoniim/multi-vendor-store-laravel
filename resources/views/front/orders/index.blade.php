<x-front-layout title="My Orders">
    <div class="container">
        <h1>My Orders</h1>
        <table class="table">
            <thead>
                <tr>
                    <th>Order #</th>
                    <th>Status</th>
                    <th>Delivery</th> <!-- <- Add delivery column -->
                    <th>Total</th>
                    <th>Actions</th>
                </tr>
            </thead>
            <tbody>
                @foreach($orders as $order)
                    <tr>
                        <td>{{ $order->number }}</td>
                        <td>{{ ucfirst($order->status) }}</td>
                        <td>
                            <!-- Mini delivery status badge -->
                            <span class="badge 
                                @if($order->delivery && $order->delivery->status == 'delivering') badge-info
                                @elseif($order->delivery && $order->delivery->status == 'completed') badge-success
                                @else badge-secondary
                                @endif">
                                {{ $order->delivery?->status ?? 'Pending' }}
                            </span>
                        </td>
                        <td>${{ number_format($order->total, 2) }}</td>
                        <td>
                            <a href="{{ route('orders.show', $order) }}" class="btn btn-primary btn-sm">View</a>
                            
                            @if($order->status === 'paid' && $order->delivery)
                                <a href="{{ route('orders.success', $order) }}" class="btn btn-success btn-sm">Track Delivery</a>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>
</x-front-layout>
