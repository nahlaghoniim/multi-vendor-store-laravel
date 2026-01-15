@php
    $newCount = $newCount ?? 0;
    $notifications = $notifications ?? collect();
@endphp

<li class="nav-item dropdown">
    <a class="nav-link" data-toggle="dropdown" href="#">
        <i class="far fa-bell"></i>
        <span id="notification-count" class="badge badge-warning navbar-badge">
            {{ $newCount }}
        </span>
    </a>
    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
        <span class="dropdown-header" id="dropdown-header">{{ $newCount }} Notifications</span>
        <div class="dropdown-divider"></div>
        <div id="notification-items">
            @foreach($notifications as $notification)
                <a href="{{ route('dashboard.index') }}?notification_id={{ $notification->id }}"
                   class="dropdown-item text-wrap @if ($notification->read_at === null) text-bold @endif"
                   data-id="{{ $notification->id }}">
                    <i class="{{ $notification->data['icon'] }} mr-2"></i> 
                    {{ $notification->data['body'] }}
                    <span class="float-right text-muted text-sm">
                        {{ $notification->created_at->longAbsoluteDiffForHumans() }}
                    </span>
                </a>
                <div class="dropdown-divider"></div>
            @endforeach
        </div>
        <a href="#" class="dropdown-item dropdown-footer">See All Notifications</a>
    </div>
</li>
