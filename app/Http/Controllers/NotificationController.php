<?php

namespace App\Http\Controllers;

class NotificationController extends Controller
{
    /**
     * Display a listing of the resource.
     */
    public function index()
    {
        $user = auth()->user();
        $notifications = $user->notifications;

        $unreadNotifications = $user->unreadNotifications;

        return view('notifications.index', [
            'notifications' => $notifications,
            'unreadNotifications' => $unreadNotifications,
        ]);
    }

    /**
     * Mark a notification as read without deleting it
     */
    public function read($id)
    {
        $notification = auth()->user()->notifications()->findOrFail($id);
        $notification->markAsRead();

        return redirect($notification->data['action_url']);
    }

    /**
     * Mark all notifications as read
     */
    public function markAllAsRead()
    {
        auth()->user()->unreadNotifications->markAsRead();

        return back()->with('status', 'All notifications marked as read');
    }
}
