<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;

class TestNotification extends Notification
{
    public function via($notifiable)
    {
        return ['broadcast', 'database'];
    }

    public function toDatabase($notifiable)
    {
        return [
            'body' => 'TEST: This is a test notification at ' . now()->format('H:i:s'),
            'icon' => 'fas fa-bell',
            'url' => url('/admin/dashboard'),
        ];
    }

    public function toBroadcast($notifiable)
    {
        return new BroadcastMessage([
            'body' => 'TEST: This is a test notification at ' . now()->format('H:i:s'),
            'icon' => 'fas fa-bell',
            'url' => url('/admin/dashboard'),
        ]);
    }
}