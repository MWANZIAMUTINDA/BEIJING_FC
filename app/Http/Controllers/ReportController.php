<?php

namespace App\Http\Controllers;

use App\Models\Payment;
use App\Models\Expense;
use App\Models\Availability;
use App\Models\User;
use App\Models\LeagueTeam;
use App\Models\Standing;
use Illuminate\Http\Request;

class ReportController extends Controller
{
    public function index()
    {
        // Require Admin/Treasurer/Coach permissions for reporting
        if (!auth()->user()->hasRole(['admin', 'treasurer', 'coach'])) {
            abort(403, 'Unauthorized access.');
        }

        return view('reports.index');
    }

    public function export(Request $request)
    {
        if (!auth()->user()->hasRole(['admin', 'treasurer', 'coach'])) {
            abort(403, 'Unauthorized access.');
        }

        $type = $request->input('type');
        $filename = "bfc_report_{$type}_" . date('Ymd_His') . ".csv";

        $headers = [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => "attachment; filename={$filename}",
            'Pragma' => 'no-cache',
            'Cache-Control' => 'must-revalidate, post-check=0, pre-check=0',
            'Expires' => '0'
        ];

        switch ($type) {
            case 'financial':
                $callback = function() {
                    $file = fopen('php://output', 'w');
                    fputcsv($file, ['Financial Balance Sheet Report']);
                    fputcsv($file, []);
                    fputcsv($file, ['Total Confirmed Revenue', 'Total Approved Expenses', 'Remaining Club Balance']);
                    
                    $totalRev = Payment::where('status', 'confirmed')->sum('amount');
                    $totalExp = Expense::where('is_approved', true)->sum('amount');
                    fputcsv($file, [$totalRev, $totalExp, $totalRev - $totalExp]);
                    fclose($file);
                };
                break;

            case 'payment':
                $callback = function() {
                    $file = fopen('php://output', 'w');
                    fputcsv($file, ['Payment Transaction History']);
                    fputcsv($file, ['ID', 'User', 'Type', 'Amount', 'Status', 'Mpesa Code', 'Phone', 'Date']);
                    
                    $payments = Payment::with('user')->latest()->get();
                    foreach ($payments as $p) {
                        fputcsv($file, [$p->id, $p->user?->name, $p->type, $p->amount, $p->status, $p->mpesa_code, $p->phone, $p->created_at]);
                    }
                    fclose($file);
                };
                break;

            case 'expense':
                $callback = function() {
                    $file = fopen('php://output', 'w');
                    fputcsv($file, ['Club Expenses List']);
                    fputcsv($file, ['ID', 'Category', 'Description', 'Amount', 'Date Incurred', 'Approved', 'Paid By']);
                    
                    $expenses = Expense::with('paidBy')->latest()->get();
                    foreach ($expenses as $e) {
                        fputcsv($file, [$e->id, $e->category, $e->description, $e->amount, $e->expense_date, $e->is_approved ? 'Yes' : 'No', $e->paidBy?->name]);
                    }
                    fclose($file);
                };
                break;

            case 'attendance':
                $callback = function() {
                    $file = fopen('php://output', 'w');
                    fputcsv($file, ['Match Attendance & Availabilities']);
                    fputcsv($file, ['Availability ID', 'Match ID', 'Player', 'Position', 'Status', 'Notes', 'Locked']);
                    
                    $availabilities = Availability::with(['user', 'match'])->latest()->get();
                    foreach ($availabilities as $a) {
                        fputcsv($file, [$a->id, $a->match_id, $a->user?->name, $a->user?->position, $a->status, $a->reason, $a->is_locked ? 'Yes' : 'No']);
                    }
                    fclose($file);
                };
                break;

            case 'member':
                $callback = function() {
                    $file = fopen('php://output', 'w');
                    fputcsv($file, ['Beijing FC Member Roster']);
                    fputcsv($file, ['ID', 'Name', 'Email', 'Phone', 'Role', 'Position', 'Active']);
                    
                    $users = User::orderBy('name')->get();
                    foreach ($users as $u) {
                        fputcsv($file, [$u->id, $u->name, $u->email, $u->phone, $u->role, $u->position, $u->is_active ? 'Yes' : 'No']);
                    }
                    fclose($file);
                };
                break;

            case 'league':
                $callback = function() {
                    $file = fopen('php://output', 'w');
                    fputcsv($file, ['League Standings Report']);
                    fputcsv($file, ['Position', 'Team Name', 'Played', 'Wins', 'Losses', 'Draws', 'Goals For', 'Goals Against', 'GD', 'Points']);
                    
                    $standings = Standing::with('team')->orderByDesc('points')->get();
                    foreach ($standings as $idx => $s) {
                        fputcsv($file, [$idx + 1, $s->team?->name, $s->played, $s->wins, $s->losses, $s->draws, $s->goals_for, $s->goals_against, $s->goal_difference, $s->points]);
                    }
                    fclose($file);
                };
                break;

            default:
                abort(404, 'Unknown report export type requested.');
        }

        return response()->stream($callback, 200, $headers);
    }
}
