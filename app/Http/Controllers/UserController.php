<?php

namespace App\Http\Controllers;

use App\Http\Requests\Users\UpdateAvailableMoneyRequest;
use App\Http\Resources\NotificationResource;
use App\Services\TotalMoneyService;
use Illuminate\Http\Request;

class UserController extends Controller
{
    public function __construct(
        private readonly TotalMoneyService $totalMoneyService,
    ) {}

    /**
     * Update the authenticated user's available money (total_money).
     */
    public function updateAvailableMoney(UpdateAvailableMoneyRequest $request)
    {
        $this->totalMoneyService->set($request->user(), (float) $request->validated('total_money'));

        return response()->json([
            'message' => 'Monto disponible actualizado correctamente.',
            'total_money' => round((float) $request->user()->total_money, 2),
        ]);
    }

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
