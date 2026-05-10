<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

/**
 * Contrôleur pour gérer les notifications via l'API mobile.
 */
class NotificationController extends Controller
{
    /**
     * Récupère les 20 dernières notifications de l'utilisateur.
     */
    public function index(Request $request)
    {
        return response()->json(
            $request->user()->notifications()->take(20)->get()
        );
    }

    /**
     * Marque toutes les notifications non lues comme lues.
     */
    public function markAsRead(Request $request)
    {
        $request->user()->unreadNotifications->markAsRead();

        return response()->json([
            'message' => 'Notifications marked as read'
        ]);
    }
}
