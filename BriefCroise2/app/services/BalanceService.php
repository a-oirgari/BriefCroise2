<?php

namespace App\Services;

use App\Models\Colocation;
use App\Models\Payment;
use \App\Models\User;

class BalanceService
{
    public function calculateBalances(Colocation $colocation): array
    {
        $activeMemberships = $colocation->activeMembers()->get();
        $activeUserIds     = $activeMemberships->pluck('user_id')->toArray();
        $memberCount       = count($activeUserIds);

        if ($memberCount === 0) {
            return [];
        }

        $balances = [];
        foreach ($activeUserIds as $uid) {
            $balances[$uid] = 0.0;
        }

        $expenses = $colocation->expenses()->get();
        foreach ($expenses as $expense) {
            $share = (float) $expense->amount / $memberCount;

            if (isset($balances[$expense->payer_id])) {
                $balances[$expense->payer_id] += (float) $expense->amount;
            }

            foreach ($activeUserIds as $uid) {
                $balances[$uid] -= $share;
            }
        }

        $payments = $colocation->payments()->get();
        foreach ($payments as $payment) {
            if (isset($balances[$payment->payer_id])) {
                $balances[$payment->payer_id] -= (float) $payment->amount;
            }
            if (isset($balances[$payment->receiver_id])) {
                $balances[$payment->receiver_id] += (float) $payment->amount;
            }
        }

        return $balances;
    }

    
    public function calculateSettlements(Colocation $colocation): array
    {
        $balances = $this->calculateBalances($colocation);

        $debtors   = []; 
        $creditors = []; 

        foreach ($balances as $userId => $balance) {
            $balance = round($balance, 2);
            if ($balance < -0.01) {
                $debtors[$userId] = abs($balance);
            } elseif ($balance > 0.01) {
                $creditors[$userId] = $balance;
            }
        }

        $settlements = [];

        $allUserIds = array_unique(array_merge(array_keys($debtors), array_keys($creditors)));
        $users = User::whereIn('id', $allUserIds)->get()->keyBy('id');

        while (!empty($debtors) && !empty($creditors)) {
            $debtorId   = array_key_first($debtors);
            $creditorId = array_key_first($creditors);

            $debtAmount   = $debtors[$debtorId];
            $creditAmount = $creditors[$creditorId];

            $transfer = min($debtAmount, $creditAmount);
            $transfer = round($transfer, 2);

            $settlements[] = [
                'from'           => $debtorId,
                'from_name'      => $users[$debtorId]->name ?? 'Unknown',
                'to'             => $creditorId,
                'to_name'        => $users[$creditorId]->name ?? 'Unknown',
                'amount'         => $transfer,
            ];

            $debtors[$debtorId]   -= $transfer;
            $creditors[$creditorId] -= $transfer;

            if ($debtors[$debtorId] < 0.01) {
                unset($debtors[$debtorId]);
            }
            if ($creditors[$creditorId] < 0.01) {
                unset($creditors[$creditorId]);
            }
        }

        return $settlements;
    }

    public function getUserBalance(Colocation $colocation, int $userId): float
    {
        $balances = $this->calculateBalances($colocation);
        return round($balances[$userId] ?? 0.0, 2);
    }

    public function userHasDebt(Colocation $colocation, int $userId): bool
    {
        return $this->getUserBalance($colocation, $userId) < -0.01;
    }
}