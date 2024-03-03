<?php

namespace App\StateMachines\Disbursement;

use App\Enumerators\DisbursementEnumerator;

class UninitializedState extends BaseState
{
    public function initialize(): void
    {
        $this->disbursement->update(['status' => DisbursementEnumerator::STATUS_PENDING]);
    }
}
