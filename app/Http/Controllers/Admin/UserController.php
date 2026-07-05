<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Models\User;
use App\Models\MemberBalance;
use App\Models\AuditLog;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\DB;

class UserController extends Controller
{
    public function index(Request $request)
    {
        $query = User::with('balance')->where('role', 'member');

        if ($request->filled('search')) {
            $query->where(function ($q) use ($request) {
                $q->where('name', 'like', '%' . $request->search . '%')
                  ->orWhere('username', 'like', '%' . $request->search . '%')
                  ->orWhere('phone', 'like', '%' . $request->search . '%')
                  ->orWhere('jersey_number', 'like', '%' . $request->search . '%');
            });
        }
        if ($request->filled('position')) $query->where('position', $request->position);
        if ($request->filled('status'))  $query->where('is_active', $request->status === 'active');

        $members = $query->orderBy('name')->paginate(20)->withQueryString();

        $stats = [
            'total'    => User::where('role', 'member')->count(),
            'active'   => User::where('role', 'member')->where('is_active', true)->count(),
            'in_debt'  => MemberBalance::where('balance', '<', 0)->count(),
            'up_to_date' => MemberBalance::where('balance', '>=', 0)->count(),
        ];

        return view('admin.users.index', compact('members', 'stats'));
    }

    public function create()
    {
        return view('admin.users.create');
    }

    public function store(Request $request)
    {
        $data = $request->validate([
            'username'          => 'required|string|max:50|unique:users',
            'name'              => 'required|string|max:100',
            'email'             => 'nullable|email|unique:users',
            'phone'             => 'required|string|max:20|unique:users',
            'position'          => 'required|in:GK,DF,MF,FW',
            'role'              => 'required|in:admin,treasurer,coach,member',
            'password'          => 'required|min:8|confirmed',
            'emergency_contact' => 'nullable|string|max:100',
            'emergency_phone'   => 'nullable|string|max:20',
            'jersey_number'     => 'nullable|integer|between:1,99|unique:users,jersey_number',
            'date_joined'       => 'nullable|date',
            'billing_type'      => 'required|in:monthly,match',
            'avatar'            => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('avatar')) {
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        $data['password'] = Hash::make($data['password']);
        $user = User::create($data);

        // Create and calculate balance record
        MemberBalance::recalculate($user->id);
        AuditLog::record('user_created', $user, [], ['username' => $user->username, 'role' => $user->role]);

        return redirect()->route('admin.users.index')
            ->with('success', "Member {$user->name} created successfully!");
    }

    public function show(User $user)
    {
        $user->load(['balance', 'payments', 'availabilities.match']);
        return view('admin.users.show', compact('user'));
    }

    public function edit(User $user)
    {
        $user->load('balance', 'payments');
        return view('admin.users.edit', compact('user'));
    }

    public function update(Request $request, User $user)
    {
        $old = $user->only(['name', 'phone', 'position', 'role', 'is_active', 'emergency_contact', 'emergency_phone', 'jersey_number', 'date_joined']);

        $data = $request->validate([
            'name'              => 'required|string|max:100',
            'email'             => 'nullable|email|unique:users,email,' . $user->id,
            'phone'             => 'required|string|max:20|unique:users,phone,' . $user->id,
            'position'          => 'required|in:GK,DF,MF,FW',
            'role'              => 'required|in:admin,treasurer,coach,member',
            'is_active'         => 'boolean',
            'emergency_contact' => 'nullable|string|max:100',
            'emergency_phone'   => 'nullable|string|max:20',
            'jersey_number'     => 'nullable|integer|between:1,99|unique:users,jersey_number,' . $user->id,
            'date_joined'       => 'nullable|date',
            'billing_type'      => 'required|in:monthly,match',
            'avatar'            => 'nullable|image|max:2048',
        ]);

        if ($request->hasFile('avatar')) {
            // Delete old avatar if exists
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }
            $data['avatar'] = $request->file('avatar')->store('avatars', 'public');
        }

        if ($request->filled('password')) {
            $request->validate(['password' => 'min:8|confirmed']);
            $data['password'] = Hash::make($request->password);
        }

        // Handle is_active explicitly if not passed
        if (!$request->has('is_active')) {
            $data['is_active'] = false;
        }

        $user->update($data);
        MemberBalance::recalculate($user->id);
        AuditLog::record('user_updated', $user, $old, $data);

        return redirect()->route('admin.users.index')
            ->with('success', "Member {$user->name} updated successfully!");
    }

    public function destroy(User $user)
    {
        // Don't allow self-deletion
        if ($user->id === auth()->id()) {
            return back()->with('error', 'You cannot delete your own profile.');
        }

        DB::transaction(function () use ($user) {
            // Clean up related records
            MemberBalance::where('user_id', $user->id)->delete();
            $user->availabilities()->delete();
            $user->payments()->delete();
            
            // Nullify expense dependencies
            $user->expenses()->update(['paid_by' => null]);
            
            // Delete avatar
            if ($user->avatar) {
                Storage::disk('public')->delete($user->avatar);
            }

            $user->delete();
        });

        AuditLog::record('user_deleted', auth()->user(), [], ['deleted_name' => $user->name, 'deleted_username' => $user->username]);

        return redirect()->route('admin.users.index')
            ->with('success', "Member {$user->name} profile was deleted successfully.");
    }

    public function toggleStatus(User $user)
    {
        $user->update(['is_active' => !$user->is_active]);
        AuditLog::record($user->is_active ? 'user_activated' : 'user_deactivated', $user);
        return back()->with('success', 'Member status updated.');
    }
}
