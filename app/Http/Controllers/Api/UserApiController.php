<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\MemberBalance;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class UserApiController extends Controller
{
    /**
     * Get users list with optional search and role/status filters.
     */
    public function index(Request $request)
    {
        $query = User::with(['team', 'balance']);

        if ($request->filled('search')) {
            $s = '%' . $request->search . '%';
            $query->where(fn($q) => $q->where('name', 'like', $s)
                ->orWhere('username', 'like', $s)
                ->orWhere('phone', 'like', $s));
        }

        if ($request->filled('role')) {
            $query->where('role', $request->role);
        }

        if ($request->filled('status')) {
            $query->where('is_active', $request->status === 'active');
        }

        $users = $query->orderBy('name')->paginate(20);

        return response()->json([
            'status' => 'success',
            'data'   => $users
        ]);
    }

    /**
     * Get specific user details.
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
                'role'           => $user->role,
                'role_label'     => $user->role_label,
                'position'       => $user->position,
                'is_active'      => $user->is_active,
                'billing_type'   => $user->billing_type,
                'balance'        => $user->balance ? $user->balance->balance : 0,
                'avatar_url'     => $user->avatar_url,
                'nationality'    => $user->nationality,
                'jersey_number'  => $user->jersey_number,
                'date_joined'    => $user->date_joined,
                'team'           => $user->team ? $user->team->only(['id', 'name', 'short_name']) : null,
            ]
        ]);
    }

    /**
     * Create a new user (admin only).
     */
    public function store(Request $request)
    {
        $data = $request->validate([
            'username'          => 'required|string|max:50|unique:users',
            'name'              => 'required|string|max:100',
            'email'             => 'nullable|email|unique:users',
            'phone'             => 'required|string|max:20|unique:users',
            'role'              => 'required|in:admin,treasurer,coach,member',
            'password'          => 'required|string|min:8',
            'position'          => 'nullable|in:GK,DF,MF,FW',
            'jersey_number'     => 'nullable|integer|between:1,99|unique:users,jersey_number',
            'billing_type'      => 'nullable|in:monthly,match',
            'nationality'       => 'nullable|string|max:100',
            'league_team_id'    => 'nullable|exists:league_teams,id',
            'date_joined'       => 'nullable|date',
        ]);

        $data['password'] = Hash::make($data['password']);

        // Default nationality and billing type if member
        if ($data['role'] === 'member') {
            $data['nationality'] = $data['nationality'] ?? 'Kenyan';
            $data['billing_type'] = $data['billing_type'] ?? 'monthly';
        }

        $user = User::create($data);

        if ($user->role === 'member') {
            MemberBalance::recalculate($user->id);
        }

        AuditLog::record('user_api_created', $user, [], ['username' => $user->username, 'role' => $user->role]);

        return response()->json([
            'status'  => 'success',
            'message' => 'User created successfully.',
            'data'    => $user
        ], 201);
    }

    /**
     * Update user details (admin only).
     */
    public function update(Request $request, User $user)
    {
        $data = $request->validate([
            'name'              => 'sometimes|string|max:100',
            'email'             => 'sometimes|nullable|email|unique:users,email,' . $user->id,
            'phone'             => 'sometimes|string|max:20|unique:users,phone,' . $user->id,
            'role'              => 'sometimes|in:admin,treasurer,coach,member',
            'is_active'         => 'sometimes|boolean',
            'position'          => 'sometimes|nullable|in:GK,DF,MF,FW',
            'jersey_number'     => 'sometimes|nullable|integer|between:1,99|unique:users,jersey_number,' . $user->id,
            'billing_type'      => 'sometimes|nullable|in:monthly,match',
            'nationality'       => 'sometimes|nullable|string|max:100',
            'league_team_id'    => 'sometimes|nullable|exists:league_teams,id',
            'date_joined'       => 'sometimes|nullable|date',
        ]);

        $old = $user->only(array_keys($data));
        $user->update($data);

        if ($user->role === 'member') {
            MemberBalance::recalculate($user->id);
        }

        AuditLog::record('user_api_updated', $user, $old, $data);

        return response()->json([
            'status'  => 'success',
            'message' => 'User updated successfully.',
            'data'    => $user->fresh(['team', 'balance'])
        ]);
    }

    /**
     * Delete a user (admin only).
     */
    public function destroy(User $user)
    {
        if ($user->id === auth('sanctum')->id()) {
            return response()->json([
                'status'  => 'error',
                'message' => 'You cannot delete your own profile.'
            ], 422);
        }

        AuditLog::record('user_api_deleted', auth('sanctum')->user(), [], [
            'deleted_name'     => $user->name,
            'deleted_username' => $user->username
        ]);

        $user->delete();

        return response()->json([
            'status'  => 'success',
            'message' => 'User profile deleted successfully.'
        ]);
    }
}
