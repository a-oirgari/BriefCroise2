<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AuthMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        if (!session('user_id')) {
            return redirect()->route('login')->with('error', 'Veuillez vous connecter.');
        }

        $user = \App\Models\User::find(session('user_id'));

        if (!$user) {
            session()->flush();
            return redirect()->route('login')->with('error', 'Session invalide.');
        }

        if ($user->is_banned) {
            session()->flush();
            return redirect()->route('login')->with('error', 'Votre compte a été banni.');
        }

        view()->share('authUser', $user);
        $request->merge(['authUser' => $user]);

        return $next($request);
    }
}