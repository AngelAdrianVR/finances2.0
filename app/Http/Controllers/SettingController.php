<?php

namespace App\Http\Controllers;

use App\Models\BankCard;
use App\Models\Setting;
use App\Models\UserLink;
use App\Models\User;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {   
        $bankCards = BankCard::where('user_id', auth()->id())->paginate(20);

        // User links data
        $user = auth()->user();

        // Ensure user has a link_code
        if (!$user->link_code) {
            $user->link_code = User::generateUniqueLinkCode();
            $user->save();
        }

        $linkedUsers = $user->linkedUsers();

        $pendingSent = $user->sentLinks()->where('status', 'pending')->with('linkedUser')->get();
        $pendingReceived = $user->receivedLinks()->where('status', 'pending')->with('user')->get();

        return inertia('Setting/Index', compact(
            'bankCards',
            'linkedUsers',
            'pendingSent',
            'pendingReceived'
        ));
    }

    // Link by code — user enters another user's unique link_code
    public function linkByCode(Request $request)
    {
        $request->validate([
            'link_code' => 'required|string|size:8',
        ]);

        $currentUser = auth()->user();
        $targetUser = User::where('link_code', strtoupper($request->link_code))->first();

        if (! $targetUser) {
            return back()->with('error', 'El código ingresado no es válido.');
        }

        if ($targetUser->id === $currentUser->id) {
            return back()->with('error', 'No puedes vincularte contigo mismo.');
        }

        // Check if already linked
        $existingLink = UserLink::where(function ($q) use ($currentUser, $targetUser) {
            $q->where('user_id', $currentUser->id)
              ->where('linked_user_id', $targetUser->id);
        })->orWhere(function ($q) use ($currentUser, $targetUser) {
            $q->where('user_id', $targetUser->id)
              ->where('linked_user_id', $currentUser->id);
        })->first();

        if ($existingLink) {
            if ($existingLink->status === 'accepted') {
                return back()->with('error', 'Ya estás vinculado con este usuario.');
            }
            if ($existingLink->status === 'pending') {
                return back()->with('error', 'Ya existe una solicitud pendiente con este usuario.');
            }
            // If rejected, reactivate
            $existingLink->update([
                'status'         => 'pending',
                'user_id'        => $currentUser->id,
                'linked_user_id' => $targetUser->id,
            ]);
        } else {
            UserLink::create([
                'user_id'        => $currentUser->id,
                'linked_user_id' => $targetUser->id,
                'status'         => 'accepted',
            ]);
        }

        return back()->with('success', 'Vinculación realizada correctamente.');
    }

    // Accept a pending link request
    public function acceptLink(UserLink $userLink)
    {
        if ($userLink->linked_user_id !== auth()->id()) {
            abort(403);
        }

        $userLink->update(['status' => 'accepted']);

        return back()->with('success', 'Vinculación aceptada.');
    }

    // Reject / remove a link
    public function removeLink(UserLink $userLink)
    {
        $currentUserId = auth()->id();
        
        if ($userLink->user_id !== $currentUserId && $userLink->linked_user_id !== $currentUserId) {
            abort(403);
        }

        $userLink->delete();

        return back()->with('success', 'Vinculación eliminada.');
    }

    public function create()
    {
        //
    }

    public function store(Request $request)
    {
        //
    }

    public function show(Setting $setting)
    {
        //
    }

    public function edit(Setting $setting)
    {
        //
    }

    public function update(Request $request, Setting $setting)
    {
        //
    }

    public function destroy(Setting $setting)
    {
        //
    }
}
