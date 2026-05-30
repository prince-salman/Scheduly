<?php

namespace App\Http\Controllers;

use App\Models\Notification;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    public function index(Request $request)
    {
        $userId = Auth::id();

        $query = Notification::forUser($userId)->with('task')->latest();

        // Filter by type chip
        if ($type = $request->get('type')) {
            $query->ofType($type);
        }

        $notifications = $query->paginate(20)->withQueryString();

        // Section grouping: today / yesterday / older
        $grouped = $notifications->getCollection()->groupBy(function ($n) {
            if ($n->created_at->isToday())     return 'Hari Ini';
            if ($n->created_at->isYesterday()) return 'Kemarin';
            return $n->created_at->locale('id')->isoFormat('D MMMM YYYY');
        });

        $unreadCount = Notification::forUser($userId)->unread()->count();

        return view('notifications.index', compact('notifications', 'grouped', 'unreadCount'));
    }

    // ── Mark one as read (PATCH /notifications/{id}/read) ─────────

    public function markRead(Notification $notification)
    {
        abort_if($notification->user_id !== Auth::id(), 403);

        $notification->markAsRead();

        if (request()->wantsJson()) {
            return response()->json(['ok' => true]);
        }

        return back();
    }

    // ── Mark all as read (PATCH /notifications/read-all) ──────────

    public function markAllRead()
    {
        Notification::forUser(Auth::id())
            ->unread()
            ->update(['read_at' => now()]);

        if (request()->wantsJson()) {
            return response()->json(['ok' => true]);
        }

        return back()->with('success', 'Semua notifikasi ditandai dibaca.');
    }

    // ── Unread count badge (GET /notifications/unread-count) ───────

    public function unreadCount()
    {
        return response()->json([
            'count' => Notification::forUser(Auth::id())->unread()->count(),
        ]);
    }
}