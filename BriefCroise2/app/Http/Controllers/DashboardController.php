<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;

class DashboardController extends Controller
{
    public function index()
    {
        $user       = User::find(session('user_id'));
        $colocation = $user->getActiveColocation();

        return view('dashboard', compact('user', 'colocation'));
    }
}