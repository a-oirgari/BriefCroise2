<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Colocation;
use App\Models\Expense;
use Illuminate\Http\Request;

class AdminController extends Controller
{
    public function dashboard()
    {
        $stats = [
            'total_users'       => User::count(),
            'banned_users'      => User::where('is_banned', true)->count(),
            'total_colocations' => Colocation::count(),
            'active_colocations'=> Colocation::where('status', 'active')->count(),
            'total_expenses'    => Expense::count(),
            'total_amount'      => Expense::sum('amount'),
        ];

        $users       = User::orderBy('created_at', 'desc')->paginate(15);
        $colocations = Colocation::with('owner')->orderBy('created_at', 'desc')->paginate(10);

        return view('admin.dashboard', compact('stats', 'users', 'colocations'));
    }

    public function ban(User $user)
    {
        $currentUser = User::find(session('user_id'));

        if ($user->id === $currentUser->id) {
            return back()->with('error', 'Vous ne pouvez pas vous bannir vous-même.');
        }

        $user->update(['is_banned' => true]);

        return back()->with('success', "Utilisateur {$user->name} banni.");
    }

    public function unban(User $user)
    {
        $user->update(['is_banned' => false]);

        return back()->with('success', "Utilisateur {$user->name} débanni.");
    }

    public function users()
    {
        $users = User::orderBy('created_at', 'desc')->paginate(20);
        return view('admin.users', compact('users'));
    }
}