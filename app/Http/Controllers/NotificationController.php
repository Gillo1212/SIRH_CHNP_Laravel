<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class NotificationController extends Controller
{
    /**
     * Page "Toutes les notifications"
     */
    public function index()
    {
        $notifications = auth()->user()
            ->notifications()
            ->latest()
            ->paginate(20);

        // Marquer comme lues les non-lues affichées
        auth()->user()->unreadNotifications()->update(['read_at' => now()]);

        return view('notifications.index', compact('notifications'));
    }

    /**
     * Marquer une notification comme lue et rediriger vers son URL
     */
    public function markAsRead(Request $request, string $id)
    {
        $notification = auth()->user()
            ->notifications()
            ->findOrFail($id);

        $notification->markAsRead();

        $url = $notification->data['url'] ?? route('dashboard');

        if ($request->expectsJson()) {
            return response()->json(['status' => 'ok']);
        }

        return redirect($url);
    }

    /**
     * Marquer toutes les notifications comme lues (AJAX ou redirect)
     */
    public function markAllAsRead(Request $request)
    {
        auth()->user()->unreadNotifications()->update(['read_at' => now()]);

        if ($request->expectsJson()) {
            return response()->json(['status' => 'ok', 'count' => 0]);
        }

        return back()->with('success', 'Toutes les notifications ont été marquées comme lues.');
    }

    /**
     * Supprimer une notification
     */
    public function destroy(string $id)
    {
        auth()->user()
            ->notifications()
            ->findOrFail($id)
            ->delete();

        return back()->with('success', 'Notification supprimée.');
    }

    /**
     * Vider toutes les notifications
     */
    public function destroyAll()
    {
        auth()->user()->notifications()->delete();

        return back()->with('success', 'Toutes les notifications ont été supprimées.');
    }
}
