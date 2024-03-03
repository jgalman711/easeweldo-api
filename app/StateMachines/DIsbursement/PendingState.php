<?php

namespace App\StateMachine\Disbursement;

use App\Enumerators\DisbursementEnumerator;

class PendingState extends BaseState
{
    public function pay(): void
    {
        $this->disbursement->update(['status', DisbursementEnumerator::STATUS_COMPLETED]);
    }

    public function cancel(): void
    {
        $this->disbursement->update(['status', DisbursementEnumerator::STATUS_CANCELLED]);
    }
}
