<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Setting;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class SettingController extends Controller
{
    public function index()
    {
        $settings = [
            'club_name'       => Setting::get('club_name', 'Beijing FC'),
            'monthly_fee'      => Setting::get('monthly_fee', '2080'),
            'match_fee'        => Setting::get('match_fee', '350'),
            'paybill_number'   => Setting::get('paybill_number', '174379'),
            'sms_sender'       => Setting::get('sms_sender', 'BeijingFC'),
            'current_season'   => Setting::get('current_season', '2025/2026'),
            'league_rules'     => Setting::get('league_rules', 'Standard Round Robin. 3 Points for Win, 1 Point for Draw.'),
        ];

        return view('admin.settings', compact('settings'));
    }

    public function update(Request $request)
    {
        $data = $request->validate([
            'club_name'       => 'required|string|max:100',
            'monthly_fee'      => 'required|numeric|min:0',
            'match_fee'        => 'required|numeric|min:0',
            'paybill_number'   => 'required|string|max:20',
            'sms_sender'       => 'required|string|max:20',
            'current_season'   => 'required|string|max:20',
            'league_rules'     => 'required|string',
        ]);

        foreach ($data as $key => $val) {
            Setting::set($key, $val);
        }

        AuditLog::record('settings_updated', null, [], $data);

        return back()->with('success', 'Club configurations updated successfully.');
    }
}
