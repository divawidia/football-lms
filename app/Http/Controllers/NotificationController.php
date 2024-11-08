<?php

namespace App\Http\Controllers;

class NotificationController extends Controller
{
    public function markAsRead($id)
    {
        $notification = auth()->user()->unreadNotifications()->find($id);

        if ($notification) {
            $notification->markAsRead();
            return response()->json(['status' => 'success', 'message' => 'Notification marked as read']);
        }

        return response()->json(['status' => 'error', 'message' => 'Notification not found'], 404);
    }
}
