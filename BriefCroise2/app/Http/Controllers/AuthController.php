<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Http\Requests\RegisterRequest;
use App\Http\Requests\LoginRequest;
use App\Http\Requests\UpdateProfileRequest;
use Illuminate\Support\Facades\Hash;

class AuthController extends Controller
{
    public function showRegister()
    {
        return view('auth.register');
    }

    public function register(RegisterRequest $request)
    {
        $isFirstUser = User::count() === 0;

        $user = User::create([
            'name'     => $request->name,
            'email'    => $request->email,
            'password' => Hash::make($request->password),
            'role'     => $isFirstUser ? 'admin' : 'user',
        ]);

        session(['user_id' => $user->id]);

        return redirect()->route('dashboard')
            ->with('success', 'Inscription réussie ! Bienvenue ' . $user->name);
    }

    public function showLogin()
    {
        return view('auth.login');
    }

    public function login(LoginRequest $request)
    {
        $user = User::where('email', $request->email)->first();

        if (!$user || !Hash::check($request->password, $user->password)) {
            return back()->with('error', 'Email ou mot de passe incorrect.');
        }

        if ($user->is_banned) {
            return back()->with('error', 'Votre compte a été banni de la plateforme.');
        }

        session(['user_id' => $user->id]);

        return redirect()->route('dashboard')->with('success', 'Connexion réussie !');
    }

    public function logout()
    {
        session()->flush();
        return redirect()->route('login')->with('success', 'Déconnexion réussie.');
    }

    public function showProfile()
    {
        $user = User::find(session('user_id'));
        return view('auth.profile', compact('user'));
    }

    public function updateProfile(UpdateProfileRequest $request)
    {
        $user = User::find(session('user_id'));

        $user->name  = $request->name;
        $user->email = $request->email;

        if ($request->filled('password')) {
            $user->password = Hash::make($request->password);
        }

        $user->save();

        return back()->with('success', 'Profil mis à jour avec succès.');
    }
}