<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Inertia\Inertia;
use Inertia\Response;

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
            ->map(fn ($n) => $this->formatNotification($n));

        return response()->json([
            'notifications' => $notifications,
            'unread_count' => $admin->unreadNotifications()->count(),
        ]);
    }

    /**
     * Show all notifications page (read + unread, paginated).
     * Optionally selects a specific notification to view in detail.
     */
    public function all(Request $request): Response
    {
        $admin = $request->user('admin');

        $notifications = $admin->notifications()
            ->latest()
            ->paginate(20)
            ->through(fn ($n) => $this->formatNotification($n));

        $selectedNotification = null;
        $selectedId = $request->route('notification');

        if ($selectedId) {
            $n = $admin->notifications()->where('id', $selectedId)->first();
            if ($n) {
                $selectedNotification = $this->formatNotification($n);

                // Mark as read when viewing
                if ($selectedNotification['read_at'] === null) {
                    $n->update(['read_at' => now()]);
                    $selectedNotification['read_at'] = now()->diffForHumans();
                    $selectedNotification['is_read'] = true;
                }
            }
        }

        return Inertia::render('Admin/Notifications/Index', [
            'notifications' => $notifications,
            'unread_count' => $admin->unreadNotifications()->count(),
            'selected_notification' => $selectedNotification,
        ]);
    }

    /**
     * Format a notification record for API/Inertia response.
     */
    private function formatNotification($n): array
    {
        return [
            'id' => $n->id,
            'type' => $n->type,
            'subject' => $n->data['subject'] ?? 'Notification',
            'body' => $n->data['body'] ?? '',
            'action_url' => $n->data['action_url'] ?? null,
            'read_at' => $n->read_at?->diffForHumans() ?? null,
            'is_read' => $n->read_at !== null,
            'created_at' => $n->created_at->diffForHumans(),
            'created_at_raw' => $n->created_at->toISOString(),
        ];
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
