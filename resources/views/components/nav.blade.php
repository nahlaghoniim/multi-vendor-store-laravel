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
];
@endphp

<nav class="mt-2">
    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">

        @foreach($items as $item)
            @php
                // Check if current route matches item
                $isActive = request()->routeIs($item['active'] ?? '');
            @endphp

            <li class="nav-item">
                @if(isset($item['route']))
                    <a href="{{ route($item['route']) }}" class="nav-link {{ $isActive ? 'active' : '' }}">
                        <i class="nav-icon {{ $item['icon'] }}"></i>
                        <p>{{ $item['title'] }}</p>
                    </a>
                @else
                    <a href="#" class="nav-link">
                        <i class="nav-icon {{ $item['icon'] }}"></i>
                        <p>{{ $item['title'] }}</p>
                    </a>
                @endif
            </li>

        @endforeach

    </ul>
</nav>
