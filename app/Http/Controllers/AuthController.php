<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    // show pages
    public function showLogin()   { return view('auth.login'); }
    public function showRegister(){ return view('auth.register'); }

    // handle login
    public function login(Request $request)
    {
        $credentials = $request->validate([
            'email'    => ['required','email'],
            'password' => ['required','string','min:6'],
        ]);

        $remember = $request->boolean('remember');

        if (Auth::attempt($credentials, $remember)) {
            $request->session()->regenerate();
            $user = Auth::user();

            // redirect by role
            if ($user->role === 'admin') {
                return redirect()->route('admin.dashboard')->with('status', '👑 Welcome, Admin!');
            } else {
                return redirect()->route('home')->with('status', '👋 Welcome back!');
            }
        }

        return back()->withErrors(['email' => 'Invalid credentials'])->onlyInput('email');
    }

    // handle signup
    public function register(Request $request)
    {
        $data = $request->validate([
            'first_name' => ['required','string','max:100'],
            'last_name'  => ['required','string','max:100'],
            'email'      => ['required','email','max:255','unique:users,email'],
            'phone'      => ['nullable','string','max:30'],
            'address'    => ['nullable','string','max:1000'],
            'role'       => ['required','in:user,admin'],
            'password'   => ['required','string','min:6'],
        ]);

        User::create([
            'first_name' => $data['first_name'],
            'last_name'  => $data['last_name'],
            'email'      => $data['email'],
            'phone'      => $data['phone'] ?? null,
            'address'    => $data['address'] ?? null,
            'role'       => $data['role'],
            'password'   => Hash::make($data['password']),
        ]);

        // redirect with success message
        return redirect()
            ->route('login')
            ->with('success', '🎉 Account created successfully! Redirecting to login page...');
    }

    // logout
    public function logout(Request $request)
    {
        Auth::logout();
        $request->session()->invalidate();
        $request->session()->regenerateToken();
        return redirect()->route('home')->with('status','👋 Logged out successfully.');
    }
    public function checkEmail(Request $request)
{
    $exists = \App\Models\User::where('email', $request->email)->exists();
    return response()->json(['exists' => $exists]);
}

}
