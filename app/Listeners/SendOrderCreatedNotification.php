<?php

namespace App\Listeners;

use App\Events\OrderCreated;
use App\Models\Admin;
use App\Notifications\OrderCreatedNotification;
use Illuminate\Support\Facades\Notification;

class SendOrderCreatedNotification
{
    public function handle(OrderCreated $event)
    {
        $order = $event->order;

        // Send to all admins
        $admins = Admin::all();
        Notification::send($admins, new OrderCreatedNotification($order));
    }
}
