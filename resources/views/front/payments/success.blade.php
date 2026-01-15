<x-front-layout title="Order Success">
    <div class="container text-center py-5">
        <div class="success-icon mb-4">
            <svg xmlns="http://www.w3.org/2000/svg" width="80" height="80" fill="currentColor" class="bi bi-check-circle text-success" viewBox="0 0 16 16">
                <path d="M8 15A7 7 0 1 1 8 1a7 7 0 0 1 0 14zm0 1A8 8 0 1 0 8 0a8 8 0 0 0 0 16z"/>
                <path d="M10.97 4.97a.235.235 0 0 0-.02.022L7.477 9.417 5.384 7.323a.75.75 0 0 0-1.06 1.06L6.97 11.03a.75.75 0 0 0 1.079-.02l3.992-4.99a.75.75 0 0 0-1.071-1.05z"/>
            </svg>
        </div>

        <h1 class="mb-3">Payment Successful!</h1>
        <p class="lead mb-4">Your order #{{ $order->number }} has been paid successfully.</p>

        <div class="alert alert-success mx-auto" style="max-width: 600px;">
            <strong>Order Total:</strong> ${{ number_format($order->total, 2) }}<br>
            <strong>Payment Method:</strong> {{ ucfirst($order->payment_method) }}<br>
            <strong>Status:</strong> {{ ucfirst($order->status) }}
        </div>

        <div class="mt-4">
            <a href="{{ route('orders.show', $order) }}" class="btn btn-primary me-2">
                View Order Details
            </a>

           

            <a href="{{ route('home') }}" class="btn btn-secondary">
                Continue Shopping
            </a>
        </div>
    </div>
</x-front-layout>