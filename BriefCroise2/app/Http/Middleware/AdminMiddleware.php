<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = \App\Models\User::find(session('user_id'));

        if (!$user || $user->role !== 'admin') {
            return redirect()->route('dashboard')->with('error', 'Accès réservé aux administrateurs.');
        }

        return $next($request);
    }
}