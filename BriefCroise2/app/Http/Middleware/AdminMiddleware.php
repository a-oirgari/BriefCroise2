<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use \App\Models\User;
class AdminMiddleware
{
    public function handle(Request $request, Closure $next)
    {
        $user = User::find(session('user_id'));

        if (!$user || $user->role !== 'admin') {
            return redirect()->route('dashboard')->with('error', 'Accès réservé aux administrateurs.');
        }

        return $next($request);
    }
}