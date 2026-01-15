<?php

namespace App\View\Components\Dashboard;

use Illuminate\Support\Facades\Auth;
use Illuminate\View\Component;

class NotificationsMenu extends Component
{
    public $notifications;
    public $newCount;

    public function __construct(int $count = 10)
    {
        // Get logged-in admin
        $admin = Auth::guard('admin')->user();

        if (!$admin) {
            $this->notifications = collect();
            $this->newCount = 0;
            return;
        }

        // Get unread notifications from query builder
        $this->notifications = $admin->unreadNotifications() // <- method, not property
            ->orderBy('created_at', 'desc')
            ->limit($count)
            ->get();

        $this->newCount = $this->notifications->count();
    }

    public function render()
    {
        return view('components.dashboard.notifications-menu');
    }
}
