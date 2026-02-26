<?php

namespace App\Services;

use App\Models\User;
use App\Models\Colocation;
use App\Services\BalanceService;

class ReputationService
{
    public function __construct(private BalanceService $balanceService) {}

    
    public function applyOnLeave(User $user, Colocation $colocation): void
    {
        $hasDebt = $this->balanceService->userHasDebt($colocation, $user->id);
        $this->applyReputation($user, $hasDebt ? -1 : 1);
    }


    public function applyOnRemoveMember(User $removedUser, User $owner, Colocation $colocation): void
    {
        $hasDebt = $this->balanceService->userHasDebt($colocation, $removedUser->id);

        
        $this->applyReputation($removedUser, $hasDebt ? -1 : 1);

        
        if ($hasDebt) {
            $this->applyReputation($owner, -1);
        }
    }

    private function applyReputation(User $user, int $delta): void
    {
        $user->increment('reputation', $delta);
    }
}