<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class MarkNotificationAsRead
{
    public function handle(Request $request, Closure $next)
    {
        $notification_id = $request->query('notification_id');

        if ($notification_id) {
            $admin = auth('admin')->user();

            if ($admin) {
                // Use parentheses to get query builder, not Collection
                $notification = $admin->unreadNotifications()->find($notification_id);

                if ($notification) {
                    $notification->markAsRead();
                }
            }
        }

        return $next($request);
    }
}
