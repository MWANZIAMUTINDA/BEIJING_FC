<?php

namespace App\Http\Controllers;

use App\Models\Announcement;
use App\Models\NotificationRead;
use App\Models\User;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class AnnouncementController extends Controller
{
    public function index()
    {
        $announcements = Announcement::with('creator')->latest()->paginate(15);
        $unread = 0;

        if (auth()->check()) {
            $unread = Announcement::whereDoesntHave('reads', function ($q) {
                $q->where('user_id', auth()->id());
            })->count();
        }

        return view('announcements.index', compact('announcements', 'unread'));
    }

    public function create()
    {
        $matches = \App\Models\FootballMatch::upcoming()->get();
        return view('announcements.create', compact('matches'));
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'title'    => 'required|string|max:150',
            'body'     => 'required|string',
            'type'     => 'required|in:general,match_reminder,payment_alert,league_update,urgent',
            'send_sms' => 'boolean',
            'match_id' => 'nullable|exists:matches,id',
        ]);

        $data['created_by'] = auth()->id();
        $data['sent_at']    = now();

        $announcement = Announcement::create($data);
        AuditLog::record('announcement_created', $announcement);

        return redirect()->route('announcements.index')
            ->with('success', 'Announcement published!');
    }

    public function markRead(Announcement $announcement)
    {
        NotificationRead::firstOrCreate([
            'announcement_id' => $announcement->id,
            'user_id'         => auth()->id(),
        ], ['read_at' => now()]);

        return response()->json(['status' => 'ok']);
    }

    public function destroy(Announcement $announcement)
    {
        AuditLog::record('announcement_deleted', $announcement);
        $announcement->delete();
        return back()->with('success', 'Announcement deleted.');
    }
}
