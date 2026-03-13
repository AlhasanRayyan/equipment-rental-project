<?php

namespace App\Http\Controllers;

use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Auth;
use Illuminate\Http\Request;

class NotificationController extends Controller
{
 public function index()
    {
        $user = Auth::user();

        $notifications = $user->notifications()
            ->latest()
            ->paginate(10);

        return view('frontend.notifications.index', compact('notifications'));
    }

    public function read($id)
    {
        $n = Auth::user()->notifications()->findOrFail($id);
        $n->markAsRead();

        return back();
    }

    public function destroy($id)
    {
        $n = Auth::user()->notifications()->findOrFail($id);
        $n->delete();

        return back();
    }

    public function readAll()
    {
        Auth::user()->unreadNotifications->markAsRead();
        return back();
    }
}
