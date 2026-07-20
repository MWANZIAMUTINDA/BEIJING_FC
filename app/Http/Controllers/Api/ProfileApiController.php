<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\Rule;

class ProfileApiController extends Controller
{
    /**
     * Get the authenticated user's profile.
     */
    public function show(Request $request)
    {
        return response()->json([
            'status' => 'success',
            'data' => $request->user()
        ]);
    }

    /**
     * Update the authenticated user's profile information.
     */
    public function update(Request $request)
    {
        $user = $request->user();

        $data = $request->validate([
            'name'     => ['required', 'string', 'max:255'],
            'username' => [
                'sometimes', 'required', 'string', 'max:50',
                Rule::unique('users')->ignore($user->id),
            ],
            'phone'    => [
                'sometimes', 'required', 'string', 'max:20',
                Rule::unique('users')->ignore($user->id),
            ],
            'position' => ['sometimes', 'required', 'in:GK,DF,MF,FW'],
            'email'    => [
                'nullable', 'string', 'email', 'max:255',
                Rule::unique('users')->ignore($user->id),
            ],
        ]);

        $user->fill($data);

        if ($user->isDirty('email')) {
            $user->email_verified_at = null;
        }

        $user->save();

        return response()->json([
            'status' => 'success',
            'message' => 'Profile updated successfully.',
            'data' => $user
        ]);
    }

    /**
     * Update the authenticated user's avatar.
     */
    public function updateAvatar(Request $request)
    {
        $request->validate([
            'avatar' => ['required', 'image', 'mimes:jpg,jpeg,png,webp', 'max:2048'],
        ]);

        $user = $request->user();

        // Delete old avatar
        if ($user->avatar && Storage::disk('public')->exists($user->avatar)) {
            Storage::disk('public')->delete($user->avatar);
        }

        $path = $request->file('avatar')->store('avatars', 'public');
        $user->update(['avatar' => $path]);

        return response()->json([
            'status' => 'success',
            'message' => 'Avatar updated successfully.',
            'avatar_url' => $user->avatar_url
        ]);
    }
}
