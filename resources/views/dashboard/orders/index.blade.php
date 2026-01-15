@extends('layouts.dashboard')

@section('title', 'Orders')

@section('breadcrumb')
    @parent
    <li class="breadcrumb-item active">Orders</li>
@endsection

@section('content')

<h1 class="mb-4">Orders</h1>

{{-- Filters --}}
<form method="GET" class="row g-2 mb-4">
    <div class="col-md-4">
        <input
            type="text"
            name="number"
            value="{{ request('number') }}"
            class="form-control"
            placeholder="Order number"
        >
    </div>

    <div class="col-md-3">
        <select name="status" class="form-select">
            <option value="">All Status</option>
            @foreach(['pending','processing','shipped','completed','cancelled'] as $s)
                <option value="{{ $s }}" @selected(request('status') === $s)>
                    {{ ucfirst($s) }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-2">
        <button class="btn btn-primary w-100">
            Filter
        </button>
    </div>
</form>

{{-- Orders Table --}}
<div class="card">
    <div class="card-body p-0">
        <table class="table table-hover mb-0 align-middle">
            <thead class="table-light">
            <tr>
                <th>#</th>
                <th>Number</th>
                <th>User</th>
                <th>Store</th>
                <th>Total</th>
                <th>Status</th>
                <th>Created At</th>
                <th class="text-end">Actions</th>
            </tr>
            </thead>

            <tbody>
            @forelse($orders as $order)
                <tr>
                    <td>{{ $order->id }}</td>

                    <td class="fw-bold">
                        {{ $order->number }}
                    </td>

                    <td>
                        {{ $order->user?->name ?? 'Guest Customer' }}
                    </td>

                    <td>
                        {{ $order->store?->name ?? '—' }}
                    </td>

                    <td>
                        ${{ number_format($order->total, 2) }}
                    </td>

                    <td>
                        @php
                            $statusColors = [
                                'pending' => 'secondary',
                                'processing' => 'info',
                                'shipped' => 'warning',
                                'completed' => 'success',
                                'cancelled' => 'danger',
                            ];
                        @endphp

                        <span class="badge bg-{{ $statusColors[$order->status] ?? 'secondary' }}">
                            {{ ucfirst($order->status) }}
                        </span>
                    </td>

                    <td>
                        {{ $order->created_at->format('Y-m-d') }}
                    </td>

                    <td class="text-end">
                        <a
                            href="{{ route('dashboard.orders.show', $order) }}"
                            class="btn btn-sm btn-outline-primary"
                        >
                            View
                        </a>
                    </td>
                </tr>
            @empty
                <tr>
                    <td colspan="8" class="text-center text-muted py-4">
                        No orders found.
                    </td>
                </tr>
            @endforelse
            </tbody>
        </table>
    </div>
</div>

{{-- Pagination --}}
<div class="mt-3">
    {{ $orders->withQueryString()->links() }}
</div>

@endsection
