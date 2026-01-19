<?php

namespace App\Notifications;

use Illuminate\Notifications\Messages\MailMessage;
use Illuminate\Notifications\Messages\BroadcastMessage;
use Illuminate\Notifications\Notification;
use App\Models\Order;

class OrderCreatedNotification extends Notification
{
    protected $order;

    public function __construct(Order $order)
    {
        $this->order = $order;
    }

    public function via($notifiable)
    {
        return ['database', 'broadcast'];
    }

    public function toMail($notifiable)
    {
        $store = $this->order->store;
        $customerName = $this->order->user ? $this->order->user->name : 'Guest Customer';

        return (new MailMessage)
            ->subject("New Order #{$this->order->number}")
            ->from('notification@ajyal-store.ps', 'AJYAL Store')
            ->greeting("Hi {$notifiable->name},")
            ->line("A new order (#{$this->order->number}) created by {$customerName} at {$store->name}.")
            ->action('View Order', url('/admin/dashboard/orders/' . $this->order->id))
            ->line('Thank you for using our application!');
    }

    public function toDatabase($notifiable)
    {
        $store = $this->order->store;
        $customerName = $this->order->user ? $this->order->user->name : 'Guest Customer';

        return [
            'body' => "A new order (#{$this->order->number}) created by {$customerName} at {$store->name}.",
            'icon' => 'fas fa-file',
            'url' => url('/admin/dashboard/orders/' . $this->order->id),
            'order_id' => $this->order->id,
        ];
    }

    public function toBroadcast($notifiable)
    {
        $store = $this->order->store;
        $customerName = $this->order->user ? $this->order->user->name : 'Guest Customer';

        return new BroadcastMessage([
            'body' => "A new order (#{$this->order->number}) created by {$customerName} at {$store->name}.",
            'icon' => 'fas fa-file',
            'url' => url('/admin/dashboard/orders/' . $this->order->id),
            'order_id' => $this->order->id,
        ]);
    }
}