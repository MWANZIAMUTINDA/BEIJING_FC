<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\MemberBalance;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class PlayerApiController extends Controller
{
    /**
     * List active players (members) with optional search/filter.
     */
    public function index(Request $request)
    {
        $query = User::with(['team'])->where('role', 'member');

        if ($request->filled('search')) {
            $s = '%' . $request->search . '%';
            $query->where(fn($q) => $q->where('name','like',$s)
                ->orWhere('username','like',$s)
                ->orWhere('phone','like',$s));
        }
        if ($request->filled('position')) {
            $query->where('position', $request->position);
        }
        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }
        if ($request->filled('nationality')) {
            $query->where('nationality', $request->nationality);
        }

        $players = $query->orderBy('name')->paginate(20);

        return response()->json([
            'status' => 'success',
            'data'   => $players,
        ]);
    }

    /**
     * Get a specific player's details.
     */
    public function show(User $user)
    {
        $user->load(['balance', 'team']);

        return response()->json([
            'status' => 'success',
            'data'   => [
                'id'             => $user->id,
                'name'           => $user->name,
                'username'       => $user->username,
                'email'          => $user->email,
                'phone'          => $user->phone,
                'position'       => $user->position,
                'position_label' => $user->positionLabel(),
                'jersey_number'  => $user->jersey_number,
                'nationality'    => $user->nationality,
                'is_active'      => $user->is_active,
                'date_joined'    => $user->date_joined,
                'billing_type'   => $user->billing_type,
                'balance'        => $user->balance?->balance ?? 0,
                'team'           => $user->team?->only(['id','name','short_name','color']),
                'avatar_url'     => $user->avatar_url,
            ],
        ]);
    }

    /**
     * Create a new player (admin only).
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'username'       => 'required|string|max:50|unique:users',
            'name'           => 'required|string|max:100',
            'email'          => 'nullable|email|unique:users',
            'phone'          => 'required|string|max:20|unique:users',
            'position'       => 'required|in:GK,DF,MF,FW',
            'billing_type'   => 'required|in:monthly,match',
            'nationality'    => 'required|string|max:100',
            'jersey_number'  => 'nullable|integer|between:1,99|unique:users,jersey_number',
            'league_team_id' => 'nullable|exists:league_teams,id',
            'date_joined'    => 'nullable|date',
            'password'       => 'required|string|min:8',
        ]);

        $data['role']     = 'member';
        $data['password'] = Hash::make($data['password']);

        $player = User::create($data);
        MemberBalance::recalculate($player->id);
        AuditLog::record('player_api_created', $player, [], ['username' => $player->username]);

        return response()->json([
            'status'  => 'success',
            'message' => 'Player created successfully.',
            'data'    => $player,
        ], 201);
    }

    /**
     * Update an existing player (admin only).
     */
    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name'           => 'sometimes|string|max:100',
            'email'          => 'sometimes|nullable|email|unique:users,email,' . $user->id,
            'phone'          => 'sometimes|string|max:20|unique:users,phone,' . $user->id,
            'position'       => 'sometimes|in:GK,DF,MF,FW',
            'billing_type'   => 'sometimes|in:monthly,match',
            'nationality'    => 'sometimes|string|max:100',
            'jersey_number'  => 'sometimes|nullable|integer|between:1,99|unique:users,jersey_number,' . $user->id,
            'league_team_id' => 'sometimes|nullable|exists:league_teams,id',
            'date_joined'    => 'sometimes|nullable|date',
            'is_active'      => 'sometimes|boolean',
        ]);

        $old = $user->only(array_keys($data));
        $user->update($data);
        MemberBalance::recalculate($user->id);
        AuditLog::record('player_api_updated', $user, $old, $data);

        return response()->json([
            'status'  => 'success',
            'message' => 'Player updated successfully.',
            'data'    => $user->fresh(['team', 'balance']),
        ]);
    }

    /**
     * Delete a player (admin only).
     */
    public function destroy(User $user)
    {
        if ($user->id === auth('sanctum')->id()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'You cannot delete your own account.',
            ], 422);
        }

        AuditLog::record('player_api_deleted', auth('sanctum')->user(), [], [
            'deleted_name'     => $user->name,
            'deleted_username' => $user->username,
        ]);
        $user->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'Player deleted successfully.',
        ]);
    }
}
