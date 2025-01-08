<?php

namespace App\Http\Controllers;

use App\Http\Resources\NotificationResource;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function getNotifications()
    {
        $items = NotificationResource::collection(auth()->user()->notifications);

        return response()->json(compact('items'));
    }

    public function readNotifications(Request $request)
    {
        $unread = auth()->user()->unreadNotifications->count();
        auth()->user()->notifications->markAsRead();

        return response()->json(compact('unread'));
    }

    public function deleteNotifications(Request $request)
    {
        auth()->user()->notifications()->where('id', $request->notification_id)->delete();

        return response()->json(['message' => "Se ha eliminado la notificacion"]);
    }
}
