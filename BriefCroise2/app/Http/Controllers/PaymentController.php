<?php

namespace App\Http\Controllers;

use App\Models\User;
use App\Models\Colocation;
use App\Models\Payment;
use App\Http\Requests\StorePaymentRequest;
use App\Services\BalanceService;
use Illuminate\Support\Carbon;

class PaymentController extends Controller
{
    public function __construct(private BalanceService $balanceService) {}

    public function index(Colocation $colocation)
    {
        $user = User::find(session('user_id'));

        $membership = $colocation->activeMembers()->where('user_id', $user->id)->first();
        if (!$membership) {
            return redirect()->route('dashboard')->with('error', 'Accès non autorisé.');
        }

        $settlements = $this->balanceService->calculateSettlements($colocation);
        $balances    = $this->balanceService->calculateBalances($colocation);
        $members     = $colocation->activeMembers()->with('user')->get();

        return view('payments.index', compact(
            'colocation', 'user', 'settlements', 'balances', 'members'
        ));
    }

    public function store(StorePaymentRequest $request, Colocation $colocation)
    {
        $user = User::find(session('user_id'));

        $membership = $colocation->activeMembers()->where('user_id', $user->id)->first();
        if (!$membership) {
            return back()->with('error', 'Accès non autorisé.');
        }

        Payment::create([
            'colocation_id' => $colocation->id,
            'payer_id'      => $request->payer_id,
            'receiver_id'   => $request->receiver_id,
            'amount'        => $request->amount,
            'paid_at'       => Carbon::now(),
        ]);

        return back()->with('success', 'Paiement enregistré avec succès.');
    }
}