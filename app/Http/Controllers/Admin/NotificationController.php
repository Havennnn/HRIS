<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Get unread notifications for the bell dropdown.
     */
    public function index(Request $request): JsonResponse
    {
        $admin = $request->user('admin');
        if (! $admin) {
            return response()->json(['notifications' => [], 'unread_count' => 0]);
        }

        $notifications = $admin->notifications()
            ->whereNull('read_at')
            ->latest()
            ->take(10)
            ->get()
            ->map(fn ($n) => [
                'id' => $n->id,
                'type' => $n->type,
                'title' => $n->data['title'] ?? 'Notification',
                'message' => $n->data['message'] ?? '',
                'action_url' => $n->data['action_url'] ?? null,
                'created_at' => $n->created_at->diffForHumans(),
                'created_at_raw' => $n->created_at->toISOString(),
            ]);

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => $admin->unreadNotifications()->count(),
        ]);
    }

    /**
     * Mark a single notification as read.
     */
    public function read(Request $request, string $id): JsonResponse
    {
        $admin = $request->user('admin');
        if (! $admin) {
            return response()->json(['success' => false], 401);
        }

        $admin->notifications()->where('id', $id)->update(['read_at' => now()]);

        return response()->json([
            'success' => true,
            'unread_count' => $admin->unreadNotifications()->count(),
        ]);
    }

    /**
     * Mark all notifications as read.
     */
    public function readAll(Request $request): JsonResponse
    {
        $admin = $request->user('admin');
        if (! $admin) {
            return response()->json(['success' => false], 401);
        }

        $admin->unreadNotifications()->update(['read_at' => now()]);

        return response()->json([
            'success' => true,
            'unread_count' => 0,
        ]);
    }
}
