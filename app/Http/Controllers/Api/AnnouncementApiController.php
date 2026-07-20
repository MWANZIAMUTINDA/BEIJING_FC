<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Announcement;
use App\Models\NotificationRead;
use Illuminate\Http\Request;

class AnnouncementApiController extends Controller
{
    /**
     * Fetch announcements feed with unread count.
     */
    public function index()
    {
        $announcements = Announcement::with('creator')->latest()->paginate(15);
        
        $unreadCount = Announcement::whereDoesntHave('reads', function ($q) {
            $q->where('user_id', auth()->id());
        })->count();

        return response()->json([
            'status' => 'success',
            'data' => [
                'announcements' => $announcements,
                'unread_count' => $unreadCount
            ]
        ]);
    }

    /**
     * Mark an announcement as read.
     */
    public function markRead(Announcement $announcement)
    {
        $read = NotificationRead::firstOrCreate([
            'announcement_id' => $announcement->id,
            'user_id'         => auth()->id(),
        ], [
            'read_at' => now()
        ]);

        return response()->json([
            'status' => 'success',
            'message' => 'Notification marked as read.'
        ]);
    }
}
