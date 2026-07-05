<?php

namespace App\Http\Controllers\Auth;

use App\Http\Controllers\Controller;
use App\Models\User;
use Illuminate\Auth\Events\Registered;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Validation\ValidationException;
use Illuminate\View\View;

class RegisteredUserController extends Controller
{
    /**
     * Display the registration view.
     */
    public function create(): View
    {
        return view('auth.register');
    }

    /**
     * Handle an incoming registration request.
     *
     * @throws ValidationException
     */
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => ['required', 'string', 'max:255'],

            // ✅ NEW FIELDS ADDED
            'username' => ['required', 'string', 'max:255', 'unique:users,username'],
            'phone' => ['required', 'string', 'max:20', 'unique:users,phone'],
            'position' => ['required', 'in:GK,DF,MF,FW'],

            // email now optional (since your form says optional)
            'email' => ['nullable', 'string', 'email', 'max:255', 'unique:users,email'],

            'password' => ['required', 'confirmed', Rules\Password::defaults()],
        ]);

        $user = User::create([
            'name' => $request->name,
            'username' => $request->username,   // ✅ FIXED
            'phone' => $request->phone,         // ✅ FIXED
            'position' => $request->position,   // ✅ FIXED
            'email' => $request->email,
            'password' => Hash::make($request->password),
        ]);

        event(new Registered($user));

        Auth::login($user);

        return redirect(route('dashboard', absolute: false));
    }
}