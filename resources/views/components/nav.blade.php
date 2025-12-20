@php
$items = $items ?? [
    [
        'title' => 'Dashboard',
        'route' => 'dashboard.index',
        'icon'  => 'fas fa-tachometer-alt',
        'active'=> 'dashboard.index',
    ],

    [
        'title' => 'Categories',
        'route' => 'dashboard.categories.index',
        'icon'  => 'fas fa-list',
        'active'=> 'dashboard.categories.*',
    ],

    [
        'title' => 'Products',
        'route' => 'dashboard.products.index',
        'icon'  => 'fas fa-box',
        'active'=> 'dashboard.products.*',
    ],

    [
        'title' => 'Orders',
        'route' => 'dashboard.orders.index',
        'icon'  => 'fas fa-shopping-cart',
        'active'=> 'dashboard.orders.*',
    ],
];
@endphp

<nav class="mt-2">
    <ul class="nav nav-pills nav-sidebar flex-column"
        data-widget="treeview"
        role="menu"
        data-accordion="false">

        @foreach($items as $item)
            @php
                $isActive = request()->routeIs($item['active'] ?? '');
            @endphp

            <li class="nav-item">
                <a href="{{ route($item['route']) }}"
                   class="nav-link {{ $isActive ? 'active' : '' }}">
                    <i class="nav-icon {{ $item['icon'] }}"></i>
                    <p>{{ $item['title'] }}</p>
                </a>
            </li>

        @endforeach

    </ul>
</nav>
