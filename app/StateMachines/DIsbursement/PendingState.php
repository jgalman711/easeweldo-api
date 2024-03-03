<?php

namespace App\StateMachines\Disbursement;

use App\Enumerators\DisbursementEnumerator;
use Exception;
use Illuminate\Support\Facades\Log;

class PendingState extends BaseState
{
    public function pay(): void
    {
        foreach ($this->disbursement->payrolls as $payroll) {
            try {
                $payroll->state()->pay();
            } catch (Exception $e) {
                Log::error($e->getMessage());
            }
        }
        $this->disbursement->update(['status' => DisbursementEnumerator::STATUS_COMPLETED]);
    }

    public function cancel(): void
    {
        foreach ($this->disbursement->payrolls as $payroll) {
            try {
                $payroll->state()->cancel();
            } catch (Exception $e) {
                Log::error($e->getMessage());
            }
        }
        $this->disbursement->update(['status' => DisbursementEnumerator::STATUS_CANCELLED]);
    }
}
