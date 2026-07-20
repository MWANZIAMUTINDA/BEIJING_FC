<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\Stadium;
use App\Models\FootballMatch;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class StadiumController extends Controller
{
    public function index(Request $request)
    {
        $query = Stadium::query();

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name',     'like', '%' . $request->search . '%')
                  ->orWhere('location', 'like', '%' . $request->search . '%');
            });
        }

        if ($request->filled('surface')) {
            $query->where('surface', $request->surface);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $stadiums = $query->orderBy('name')->paginate(20)->withQueryString();

        $stats = [
            'total'    => Stadium::count(),
            'active'   => Stadium::where('is_active', true)->count(),
            'inactive' => Stadium::where('is_active', false)->count(),
        ];

        return view('admin.stadiums.index', compact('stadiums', 'stats'));
    }

    public function create()
    {
        return view('admin.stadiums.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'name'      => 'required|string|max:100|unique:stadiums,name',
            'location'  => 'nullable|string|max:200',
            'capacity'  => 'nullable|integer|min:0|max:999999',
            'surface'   => 'required|in:grass,artificial,indoor',
            'notes'     => 'nullable|string|max:1000',
            'is_active' => 'boolean',
        ]);

        $data['is_active'] = $request->boolean('is_active', true);

        $stadium = Stadium::create($data);
        AuditLog::record('stadium_created', $stadium, [], ['name' => $stadium->name]);

        return redirect()->route('admin.stadiums.index')
            ->with('success', "Stadium \"{$stadium->name}\" created successfully!");
    }

    public function show(Stadium $stadium)
    {
        // Get recent matches at this venue
        $recentMatches = FootballMatch::where('venue', 'like', '%' . $stadium->name . '%')
            ->orderByDesc('match_date')
            ->take(10)
            ->get();

        return view('admin.stadiums.show', compact('stadium', 'recentMatches'));
    }

    public function edit(Stadium $stadium)
    {
        return view('admin.stadiums.edit', compact('stadium'));
    }

    public function update(Request $request, Stadium $stadium)
    {
        $data = $request->validate([
            'name'      => 'required|string|max:100|unique:stadiums,name,' . $stadium->id,
            'location'  => 'nullable|string|max:200',
            'capacity'  => 'nullable|integer|min:0|max:999999',
            'surface'   => 'required|in:grass,artificial,indoor',
            'notes'     => 'nullable|string|max:1000',
            'is_active' => 'boolean',
        ]);

        $old = $stadium->only(['name', 'location', 'capacity', 'surface', 'is_active']);
        $data['is_active'] = $request->boolean('is_active', false);

        $stadium->update($data);
        AuditLog::record('stadium_updated', $stadium, $old, $data);

        return redirect()->route('admin.stadiums.index')
            ->with('success', "Stadium \"{$stadium->name}\" updated successfully!");
    }

    public function destroy(Stadium $stadium)
    {
        // Soft-guard: warn if matches reference this stadium
        $matchCount = FootballMatch::where('venue', 'like', '%' . $stadium->name . '%')->count();

        if ($matchCount > 0) {
            return back()->with('error',
                "Cannot delete \"{$stadium->name}\" — it is referenced by {$matchCount} match(es). Deactivate it instead."
            );
        }

        AuditLog::record('stadium_deleted', $stadium, [], ['name' => $stadium->name]);
        $stadium->delete();

        return redirect()->route('admin.stadiums.index')
            ->with('success', "Stadium \"{$stadium->name}\" deleted.");
    }
}
