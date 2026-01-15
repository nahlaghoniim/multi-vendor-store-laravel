@extends('layouts.dashboard')

@section('title', 'Dashboard')

@section('breadcrumb')
    @parent
    <li class="breadcrumb-item active">Dashboard</li>
@endsection

@section('content')

<!-- Info boxes -->
<div class="row">
    <!-- Today's Revenue -->
    <div class="col-lg-3 col-6">
        @php
            $todayRevenue = \App\Models\Order::whereDate('created_at', today())->sum('total');
        @endphp
        <div class="small-box bg-info">
            <div class="inner">
                <h3>${{ number_format($todayRevenue, 2) }}</h3>
                <p>Today's Revenue</p>
            </div>
            <div class="icon">
                <i class="fas fa-dollar-sign"></i>
            </div>
            <a href="{{ route('dashboard.orders.index') }}" class="small-box-footer">View Details <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>

    <!-- New Orders Today -->
    <div class="col-lg-3 col-6">
        @php
            $newOrders = \App\Models\Order::whereDate('created_at', today())->count();
        @endphp
        <div class="small-box bg-success">
            <div class="inner">
                <h3>{{ $newOrders }}</h3>
                <p>New Orders</p>
            </div>
            <div class="icon">
                <i class="fas fa-shopping-cart"></i>
            </div>
            <a href="{{ route('dashboard.orders.index') }}" class="small-box-footer">View Orders <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>

    <!-- Pending Orders -->
    <div class="col-lg-3 col-6">
        @php
            $pendingOrders = \App\Models\Order::where('status', 'pending')->count();
        @endphp
        <div class="small-box bg-warning">
            <div class="inner">
                <h3>{{ $pendingOrders }}</h3>
                <p>Pending Orders</p>
            </div>
            <div class="icon">
                <i class="fas fa-clock"></i>
            </div>
            <a href="{{ route('dashboard.orders.index', ['status' => 'pending']) }}" class="small-box-footer">Process Now <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>

    <!-- Low Stock Items -->
    <div class="col-lg-3 col-6">
        @php
            $lowStock = \App\Models\Product::where('quantity', '<=', 5)->count();
        @endphp
        <div class="small-box bg-danger">
            <div class="inner">
                <h3>{{ $lowStock }}</h3>
                <p>Low Stock Items</p>
            </div>
            <div class="icon">
                <i class="fas fa-exclamation-triangle"></i>
            </div>
            <a href="{{ route('dashboard.products.index') }}" class="small-box-footer">Restock Now <i class="fas fa-arrow-circle-right"></i></a>
        </div>
    </div>
</div>

<div class="row">
    <!-- Sales Chart -->
    <div class="col-lg-8">
        @php
            $salesLabels = collect([]);
            $salesRevenue = collect([]);
            $salesOrders = collect([]);
            for ($i = 6; $i >= 0; $i--) {
                $date = today()->subDays($i);
                $salesLabels->push($date->format('D'));
                $salesRevenue->push(\App\Models\Order::whereDate('created_at', $date)->sum('total'));
                $salesOrders->push(\App\Models\Order::whereDate('created_at', $date)->count());
            }
        @endphp

        <div class="card">
            <div class="card-header border-0 d-flex justify-content-between">
                <h3 class="card-title">Sales Overview</h3>
                <select class="form-control form-control-sm" style="width: 150px;">
                    <option>Last 7 Days</option>
                    <option>Last 30 Days</option>
                    <option>Last 3 Months</option>
                </select>
            </div>
            <div class="card-body">
                <div class="position-relative mb-4">
                    <canvas id="sales-chart" height="200"></canvas>
                </div>
                <div class="d-flex flex-row justify-content-end">
                    <span class="mr-2"><i class="fas fa-square text-primary"></i> Revenue</span>
                    <span><i class="fas fa-square text-gray"></i> Orders</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Recent Orders -->
    <div class="col-lg-4">
        @php
            $recentOrders = \App\Models\Order::latest()->take(5)->get();
        @endphp

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Recent Orders</h3>
            </div>
            <div class="card-body p-0">
                <ul class="products-list product-list-in-card pl-2 pr-2">
                    @foreach($recentOrders as $order)
                    <li class="item">
                        <div class="product-info">
                            <a href="{{ route('dashboard.orders.show', $order) }}" class="product-title">
                                #ORD-{{ $order->id }}
                                <span class="badge badge-{{ $order->status_class }} float-right">{{ ucfirst($order->status) }}</span>
                            </a>
                            <span class="product-description">
                                {{ $order->customer_name }} - ${{ number_format($order->total, 2) }}
                            </span>
                        </div>
                    </li>
                    @endforeach
                </ul>
            </div>
            <div class="card-footer text-center">
                <a href="{{ route('dashboard.orders.index') }}" class="uppercase">View All Orders</a>
            </div>
        </div>
    </div>
</div>

<div class="row">
   <!-- Top Selling Products -->
<div class="col-lg-6">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Top Selling Products</h3>
        </div>

        <div class="card-body p-0">
            @if($topProducts->isEmpty())
                <p class="text-center py-3">No products sold yet.</p>
            @else
                <table class="table table-striped mb-0">
                    <thead>
                        <tr>
                            <th>Product</th>
                            <th>Sold Quantity</th>
                            <th>Total Sales</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach($topProducts as $product)
                            <tr>
                                <td>{{ $product->name }}</td>
                                <td>{{ $product->order_items_count }}</td>
                                <td>${{ number_format($product->total_sales ?? 0, 2) }}</td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            @endif
        </div>

        <div class="card-footer text-center">
            <a href="{{ route('dashboard.orders.index') }}" class="uppercase">View All Orders</a>
        </div>
    </div>
</div>


    <!-- Quick Stats -->
    <div class="col-lg-6">
        @php
            $totalProducts = \App\Models\Product::count();
$activeCustomers = \App\Models\User::where('type', 'user')->count();
        @endphp
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Store Statistics</h3>
            </div>
            <div class="card-body">
                <div class="row">
                    <div class="col-6 text-center">
                        <input type="text" class="knob" value="{{ $totalProducts > 0 ? round(($totalProducts / 1000) * 100) : 0 }}" data-width="90" data-height="90" data-fgColor="#3c8dbc" readonly>
                        <div class="knob-label">Conversion Rate</div>
                    </div>
                    <div class="col-6 text-center">
                        <input type="text" class="knob" value="{{ $totalProducts }}" data-width="90" data-height="90" data-fgColor="#00a65a" readonly>
                        <div class="knob-label">Total Products</div>
                    </div>
                </div>
                <div class="row mt-4">
                    <div class="col-12">
                        <div class="info-box bg-gradient-info">
                            <span class="info-box-icon"><i class="fas fa-users"></i></span>
                            <div class="info-box-content">
                                <span class="info-box-text">Active Customers</span>
                                <span class="info-box-number">{{ $activeCustomers }}</span>
                                <div class="progress">
                                    <div class="progress-bar" style="width: 70%"></div>
                                </div>
                                <span class="progress-description">
                                    +12% from last month
                                </span>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js@3.9.1/dist/chart.min.js"></script>
<script>
document.addEventListener('DOMContentLoaded', function() {
    var salesChartCanvas = document.getElementById('sales-chart').getContext('2d');
    var salesChart = new Chart(salesChartCanvas, {
        type: 'line',
        data: {
            labels: @json($salesLabels),
            datasets: [
                {
                    label: 'Revenue',
                    data: @json($salesRevenue),
                    borderColor: '#007bff',
                    backgroundColor: 'rgba(0,123,255,0.1)',
                    tension: 0.4,
                    fill: true
                },
                {
                    label: 'Orders',
                    data: @json($salesOrders),
                    borderColor: '#6c757d',
                    backgroundColor: 'rgba(108,117,125,0.1)',
                    tension: 0.4,
                    fill: true
                }
            ]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: { legend: { display: false } },
            scales: {
                y: { beginAtZero: true, grid: { display: true, color: 'rgba(0,0,0,0.05)' } },
                x: { grid: { display: false } }
            }
        }
    });
});
</script>
@endpush
