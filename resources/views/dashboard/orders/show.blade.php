@extends('layouts.dashboard')

@section('title', 'Order #' . $order->number)

@section('breadcrumb')
    @parent
    <li class="breadcrumb-item">
        <a href="{{ route('dashboard.orders.index') }}">Orders</a>
    </li>
    <li class="breadcrumb-item active">
        #{{ $order->number }}
    </li>
@endsection

@section('content')


<div class="row">

    {{-- LEFT SIDE --}}
    <div class="col-lg-8">

        {{-- ORDER ITEMS --}}
        <div class="card mb-4">
            <div class="card-header fw-bold">
                Order Items
            </div>

            <div class="card-body p-0">
                <table class="table mb-0">
                    <thead class="table-light">
                    <tr>
                        <th>Product</th>
                        <th>Price</th>
                        <th>Qty</th>
                        <th>Total</th>
                    </tr>
                    </thead>

                    <tbody>
                    @foreach($order->items as $item)
                        <tr>
                            <td>{{ $item->product->name }}</td>
                            <td>${{ number_format($item->price, 2) }}</td>
                            <td>{{ $item->quantity }}</td>
                            <td>
                                ${{ number_format($item->price * $item->quantity, 2) }}
                            </td>
                        </tr>
                    @endforeach
                    </tbody>
                </table>
            </div>

            <div class="card-footer text-end fw-bold">
                Total: ${{ number_format($order->total, 2) }}
            </div>
        </div>

        {{-- ORDER TIMELINE --}}
        <div class="card">
            <div class="card-header fw-bold">
                Order Timeline
            </div>

            <div class="card-body">
                @php
                    $steps = ['pending','processing','shipped','completed'];
                @endphp

                <ul class="list-group list-group-flush">
                    @foreach($steps as $step)
                        <li class="list-group-item d-flex justify-content-between">
                            <span>{{ ucfirst($step) }}</span>

                            @if($order->status === $step || array_search($order->status, $steps) > array_search($step, $steps))
                                <span class="badge bg-success">Done</span>
                            @else
                                <span class="badge bg-secondary">Waiting</span>
                            @endif
                        </li>
                    @endforeach
                </ul>
            </div>
        </div>

    </div>

    {{-- RIGHT SIDE --}}
    <div class="col-lg-4">

        {{-- STATUS UPDATE --}}
        <div class="card mb-4">
            <div class="card-header fw-bold">
                Update Status
            </div>

            <div class="card-body">
                <form
                    method="POST"
                    action="{{ route('dashboard.orders.update-status', $order) }}"
                >
                    @csrf
                    @method('PATCH')

                    <select name="status" class="form-select mb-3">
                        @foreach(['pending','processing','shipped','completed','cancelled'] as $s)
                            <option value="{{ $s }}" @selected($order->status === $s)>
                                {{ ucfirst($s) }}
                            </option>
                        @endforeach
                    </select>

                    <button class="btn btn-primary w-100">
                        Update Status
                    </button>
                </form>
            </div>
        </div>

        {{-- CUSTOMER INFO --}}
        <div class="card mb-4">
            <div class="card-header fw-bold">
                Customer
            </div>

            <div class="card-body">
                <p class="mb-1">
                    <strong>Name:</strong>
                    {{ $order->user?->name ?? 'Guest Customer' }}
                </p>

                <p class="mb-0">
                    <strong>Email:</strong>
                    {{ $order->user?->email ?? '—' }}
                </p>
            </div>
        </div>

        {{-- PAYMENT INFO --}}
        <div class="card mb-4">
            <div class="card-header fw-bold">
                Payment
            </div>

            <div class="card-body">
                @forelse($order->payments as $payment)
                    <p class="mb-1">
                        <strong>Method:</strong> {{ ucfirst($payment->method) }}
                    </p>
                    <p class="mb-1">
                        <strong>Status:</strong> {{ ucfirst($payment->status) }}
                    </p>
                    <p class="mb-0">
                        <strong>Amount:</strong> ${{ number_format($payment->amount, 2) }}
                    </p>
                @empty
                    <p class="text-muted mb-0">No payment recorded.</p>
                @endforelse
            </div>
        </div>

        {{-- DELIVERY INFO --}}
        <div class="card">
            <div class="card-header fw-bold">
                Delivery
            </div>

            <div class="card-body">
                @if($order->delivery)
                    <p class="mb-1">
                        <strong>Status:</strong> {{ ucfirst($order->delivery->status) }}
                    </p>
                    <p class="mb-0">
                        <strong>Address:</strong>
                        {{ $order->shippingAddress?->full_address ?? '—' }}
                    </p>
                @else
                    <p class="text-muted mb-0">
                        No delivery assigned yet.
                    </p>
                @endif
            </div>
        </div>

    </div>
</div>

@endsection
