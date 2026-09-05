<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showLogin()
    {
        if (Auth::check()) {
            return redirect()->route('home');
        }

        return view('auth.login');
    }

    public function login(Request $request)
    {
        $request->validate([
            'username' => 'required|string',
            'password' => 'required|string',
        ]);

        $user = User::where('name', $request->username)->first();

        if (! $user) {
            return redirect()->route('login')->withErrors(['username' => 'Invalid username or password.']);
        }

        if ($user->password === $request->password || Hash::check($request->password, $user->password)) {
            if ($user->password !== $request->password) {
                $user->password = Hash::make($request->password);
                $user->save();
            }

            Auth::login($user);
            return redirect()->route('home');
        }

        return redirect()->route('login')->withErrors(['username' => 'Invalid username or password.']);
    }

    public function logout()
    {
        Auth::logout();
        return redirect()->route('login')->with('status', 'You have been logged out.');
    }
}
