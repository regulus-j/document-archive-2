<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Models\Notifications;
use Illuminate\Support\Facades\Auth;

class NotificationController extends Controller
{
    // Fetch notifications for the authenticated user
    public function index()
    {
        $notifications = Notifications::where('user_id', Auth::id())
            ->orderBy('created_at', 'desc')
            ->paginate(20);
        return view('notifications.index', compact('notifications'));
    }

    // Mark a notification as read, then optionally redirect to the document
    public function markAsRead(Request $request, $id)
    {
        $notification = Notifications::where('id', $id)
            ->where('user_id', Auth::id())
            ->firstOrFail();
        $notification->markAsRead();

        // If a document_id was passed, redirect directly to that document
        $documentId = $request->input('document_id');
        if ($documentId) {
            return redirect()->route('documents.show', $documentId);
        }

        return redirect()->back();
    }
}
