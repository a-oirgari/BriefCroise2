<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Colocation;
use App\Models\Membership;
use App\Http\Requests\StoreColocationRequest;
use App\Http\Requests\UpdateColocationRequest;
use App\Services\ReputationService;
use Illuminate\Support\Carbon;

class ColocationController extends Controller
{
    public function __construct(private ReputationService $reputationService) {}

    public function index()
    {
        $user       = User::find(session('user_id'));
        $colocation = $user->getActiveColocation();

        if (!$colocation) {
            return view('colocations.no-colocation', compact('user'));
        }

        return redirect()->route('colocations.show', $colocation->id);
    }

    public function create()
    {
        $user = User::find(session('user_id'));

        if ($user->hasActiveColocation()) {
            return redirect()->route('colocations.index')
                ->with('error', 'Vous avez déjà une colocation active.');
        }

        return view('colocations.create', compact('user'));
    }

    public function store(StoreColocationRequest $request)
    {
        $user = User::find(session('user_id'));

        if ($user->hasActiveColocation()) {
            return redirect()->route('colocations.index')
                ->with('error', 'Vous avez déjà une colocation active.');
        }

        $colocation = Colocation::create([
            'name'     => $request->name,
            'owner_id' => $user->id,
            'status'   => 'active',
        ]);

        Membership::create([
            'user_id'       => $user->id,
            'colocation_id' => $colocation->id,
            'role'          => 'owner',
            'joined_at'     => Carbon::now(),
        ]);

        return redirect()->route('colocations.show', $colocation->id)
            ->with('success', 'Colocation créée avec succès !');
    }

    public function show(Colocation $colocation, \Illuminate\Http\Request $request)
    {
        $user = User::find(session('user_id'));

        $membership = $colocation->activeMembers()->where('user_id', $user->id)->first();
        if (!$membership) {
            return redirect()->route('dashboard')->with('error', 'Accès non autorisé.');
        }

        if ($colocation->isCancelled()) {
            return redirect()->route('dashboard')->with('error', 'Cette colocation a été annulée.');
        }

        $activeMembers = $colocation->activeMembers()->with('user')->get();

        $selectedMonth = $request->get('month');
        $expensesQuery = $colocation->expenses()->with(['payer', 'category'])->orderBy('date', 'desc');

        if ($selectedMonth) {
            $expensesQuery->whereYear('date', substr($selectedMonth, 0, 4))
                          ->whereMonth('date', substr($selectedMonth, 5, 2));
        }

        $expenses   = $expensesQuery->get();
        $categories = $colocation->categories()->get();

        $availableMonths = $colocation->expenses()
            ->selectRaw('DATE_FORMAT(date, "%Y-%m") as month')
            ->distinct()
            ->orderBy('month', 'desc')
            ->pluck('month');

        return view('colocations.show', compact(
            'colocation', 'user', 'membership', 'activeMembers',
            'expenses', 'categories', 'selectedMonth', 'availableMonths'
        ));
    }

    public function edit(Colocation $colocation)
    {
        $user = User::find(session('user_id'));
        $this->assertOwner($colocation, $user);

        return view('colocations.edit', compact('colocation', 'user'));
    }

    public function update(UpdateColocationRequest $request, Colocation $colocation)
    {
        $user = User::find(session('user_id'));
        $this->assertOwner($colocation, $user);

        $colocation->update(['name' => $request->name]);

        return redirect()->route('colocations.show', $colocation->id)
            ->with('success', 'Colocation mise à jour.');
    }

    public function cancel(Colocation $colocation)
    {
        $user = User::find(session('user_id'));
        $this->assertOwner($colocation, $user);

        if ($colocation->isCancelled()) {
            return back()->with('error', 'Cette colocation est déjà annulée.');
        }

        $activeMembers = $colocation->activeMembers()->with('user')->get();
        foreach ($activeMembers as $membership) {
            $this->reputationService->applyOnLeave($membership->user, $colocation);
            $membership->update(['left_at' => Carbon::now()]);
        }

        $colocation->update([
            'status'       => 'cancelled',
            'cancelled_at' => Carbon::now(),
        ]);

        return redirect()->route('dashboard')->with('success', 'Colocation annulée.');
    }

    public function leave(Colocation $colocation)
    {
        $user       = User::find(session('user_id'));
        $membership = $colocation->activeMembers()->where('user_id', $user->id)->first();

        if (!$membership) {
            return back()->with('error', 'Vous n\'êtes pas membre de cette colocation.');
        }

        if ($membership->role === 'owner') {
            return back()->with('error', 'L\'owner ne peut pas quitter la colocation. Annulez-la.');
        }

        $this->reputationService->applyOnLeave($user, $colocation);
        $membership->update(['left_at' => Carbon::now()]);

        return redirect()->route('dashboard')->with('success', 'Vous avez quitté la colocation.');
    }

    public function removeMember(Colocation $colocation, User $member)
    {
        $user = User::find(session('user_id'));
        $this->assertOwner($colocation, $user);

        if ($member->id === $user->id) {
            return back()->with('error', 'Vous ne pouvez pas vous retirer vous-même.');
        }

        $membership = $colocation->activeMembers()->where('user_id', $member->id)->first();

        if (!$membership) {
            return back()->with('error', 'Ce membre n\'est pas actif dans cette colocation.');
        }

        $this->reputationService->applyOnRemoveMember($member, $user, $colocation);
        $membership->update(['left_at' => Carbon::now()]);

        return back()->with('success', 'Membre retiré avec succès.');
    }

    private function assertOwner(Colocation $colocation, User $user): void
    {
        $membership = $colocation->activeMembers()->where('user_id', $user->id)->first();
        if (!$membership || $membership->role !== 'owner') {
            abort(403, 'Action réservée à l\'owner.');
        }
    }
}