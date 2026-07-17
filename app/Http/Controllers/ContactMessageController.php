<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

use App\Models\ContactMessage;

class ContactMessageController extends Controller
{
    public function index()
    {
        $messages = ContactMessage::orderBy('created_at', 'desc')->paginate(15);
        
        // Ensure the notification badge drops for any unread notifications pointing here
        auth()->user()->unreadNotifications()->where('type', 'App\Notifications\NewContactMessageNotification')->update(['read_at' => now()]);

        return view('contact-messages.index', compact('messages'));
    }

    public function markAsRead(ContactMessage $contactMessage)
    {
        $contactMessage->update(['is_read' => true]);
        
        return redirect()->back()->with('success', __('Message marked as read.'));
    }
}
