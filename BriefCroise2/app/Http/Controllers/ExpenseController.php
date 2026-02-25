<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Colocation;
use App\Models\Expense;
use App\Http\Requests\StoreExpenseRequest;

class ExpenseController extends Controller
{
    public function store(StoreExpenseRequest $request, Colocation $colocation)
    {
        $user = User::find(session('user_id'));

        $this->assertActiveMember($colocation, $user);
        $payerMembership = $colocation->activeMembers()
            ->where('user_id', $request->payer_id)
            ->first();

        if (!$payerMembership) {
            return back()->with('error', 'Le payeur sélectionné n\'est pas un membre actif.');
        }

        Expense::create([
            'colocation_id' => $colocation->id,
            'payer_id'      => $request->payer_id,
            'category_id'   => $request->category_id,
            'title'         => $request->title,
            'amount'        => $request->amount,
            'date'          => $request->date,
        ]);

        return back()->with('success', 'Dépense ajoutée avec succès.');
    }

    public function destroy(Colocation $colocation, Expense $expense)
    {
        $user       = User::find(session('user_id'));
        $membership = $this->assertActiveMember($colocation, $user);

        if ($expense->payer_id !== $user->id && $membership->role !== 'owner') {
            return back()->with('error', 'Vous n\'êtes pas autorisé à supprimer cette dépense.');
        }

        $expense->delete();

        return back()->with('success', 'Dépense supprimée.');
    }

    private function assertActiveMember(Colocation $colocation, User $user)
    {
        $membership = $colocation->activeMembers()->where('user_id', $user->id)->first();
        if (!$membership) {
            abort(403, 'Accès non autorisé.');
        }
        return $membership;
    }
}