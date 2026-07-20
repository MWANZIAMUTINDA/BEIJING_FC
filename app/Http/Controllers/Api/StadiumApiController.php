<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\Stadium;
use App\Models\AuditLog;
use Illuminate\Http\Request;

class StadiumApiController extends Controller
{
    /**
     * List active stadiums.
     */
    public function index(Request $request)
    {
        $query = Stadium::query();

        if ($request->filled('search')) {
            $s = '%' . $request->search . '%';
            $query->where(fn($q) => $q->where('name','like',$s)->orWhere('location','like',$s));
        }
        if ($request->filled('surface')) {
            $query->where('surface', $request->surface);
        }
        if ($request->boolean('active_only', true)) {
            $query->where('is_active', true);
        }

        $stadiums = $query->orderBy('name')->get();

        return response()->json([
            'status' => 'success',
            'data'   => $stadiums,
        ]);
    }

    /**
     * Get stadium details.
     */
    public function show(Stadium $stadium)
    {
        return response()->json([
            'status' => 'success',
            'data'   => [
                'id'           => $stadium->id,
                'name'         => $stadium->name,
                'location'     => $stadium->location,
                'capacity'     => $stadium->capacity,
                'surface'      => $stadium->surface,
                'surface_label'=> $stadium->surface_label,
                'notes'        => $stadium->notes,
                'is_active'    => $stadium->is_active,
            ],
        ]);
    }

    /**
     * Create a stadium (admin only).
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'name'      => 'required|string|max:100|unique:stadiums,name',
            'location'  => 'nullable|string|max:200',
            'capacity'  => 'nullable|integer|min:0',
            'surface'   => 'required|in:grass,artificial,indoor',
            'notes'     => 'nullable|string|max:1000',
            'is_active' => 'boolean',
        ]);

        $stadium = Stadium::create($data);
        AuditLog::record('stadium_api_created', $stadium, [], ['name' => $stadium->name]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Stadium created successfully.',
            'data'    => $stadium,
        ], 201);
    }

    /**
     * Update a stadium (admin only).
     */
    public function update(Request $request, Stadium $stadium)
    {
        $data = $request->validate([
            'name'      => 'sometimes|string|max:100|unique:stadiums,name,' . $stadium->id,
            'location'  => 'sometimes|nullable|string|max:200',
            'capacity'  => 'sometimes|nullable|integer|min:0',
            'surface'   => 'sometimes|in:grass,artificial,indoor',
            'notes'     => 'sometimes|nullable|string|max:1000',
            'is_active' => 'sometimes|boolean',
        ]);

        $old = $stadium->only(array_keys($data));
        $stadium->update($data);
        AuditLog::record('stadium_api_updated', $stadium, $old, $data);

        return response()->json([
            'status'  => 'success',
            'message' => 'Stadium updated successfully.',
            'data'    => $stadium,
        ]);
    }

    /**
     * Delete a stadium (admin only).
     */
    public function destroy(Stadium $stadium)
    {
        AuditLog::record('stadium_api_deleted', auth('sanctum')->user(), [], ['name' => $stadium->name]);
        $stadium->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Stadium deleted successfully.',
        ]);
    }
}
