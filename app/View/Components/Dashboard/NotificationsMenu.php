<?php
namespace App\View\Components\Dashboard;

use Illuminate\Support\Facades\Auth;
use Illuminate\View\Component;
use Illuminate\Notifications\DatabaseNotification;

class NotificationsMenu extends Component
{
    /** @var \Illuminate\Support\Collection<int, DatabaseNotification> */
    public $notifications;

    /** @var int */
    public $newCount;

    public function __construct(int $count = 10)
    {
        /** @var \App\Models\User|null $user */
        $user = Auth::user();

        if (! $user) {
            $this->notifications = collect();
            $this->newCount = 0;
            return;
        }

        // Use unreadNotifications() with () to get query builder
        $this->notifications = $user
            ->unreadNotifications()
            ->latest()
            ->limit($count)
            ->get();

        $this->newCount = $this->notifications->count();
    }

    public function render()
    {
        return view('components.dashboard.notifications-menu');
    }
}