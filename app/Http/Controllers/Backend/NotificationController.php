<?php

namespace App\Http\Controllers\Backend;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    /**
     * Afficher toutes les notifications
     */
    public function index()
    {
        $notifications = Auth::user()->notifications()->paginate(20);
        return view('backend.pages.notifications.index', compact('notifications'));
    }

    /**
     * Marquer une notification comme lue
     */
    public function markAsRead($id)
    {
        $notification = Auth::user()->notifications()->findOrFail($id);
        $notification->markAsRead();
        
        // Rediriger vers l'URL de la notification si elle existe
        if (isset($notification->data['url'])) {
            return redirect($notification->data['url']);
        }
        
        return back();
    }

    /**
     * Marquer toutes les notifications comme lues
     */
    public function markAllAsRead()
    {
        Auth::user()->unreadNotifications->markAsRead();
        return back()->with('success', 'Toutes les notifications ont été marquées comme lues');
    }

    /**
     * Récupérer les notifications non lues (pour l'API/AJAX)
     */
    public function unread()
    {
        $notifications = Auth::user()->unreadNotifications;
        return response()->json([
            'count' => $notifications->count(),
            'notifications' => $notifications->take(5)
        ]);
    }
}

