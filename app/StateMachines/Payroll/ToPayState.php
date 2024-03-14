<?php

namespace App\StateMachines\Payroll;

use App\Enumerators\PayrollEnumerator;

class ToPayState extends BaseState
{
    public function pay(): void
    {
        $this->payroll->update(['status' => PayrollEnumerator::STATUS_PAID]);
        $disbursement = $this->payroll->period;
        $disbursementPayrolls = $disbursement->payrolls;
        if ($disbursementPayrolls->count() ===
            $disbursementPayrolls->where('status', PayrollEnumerator::STATUS_PAID)->count()
        ) {
            $disbursement->state()->pay();
        }
    }

    public function cancel(): void
    {
        $this->payroll->update(['status' => PayrollEnumerator::STATUS_CANCELLED]);
        $disbursement = $this->payroll->period;
        $disbursementPayrolls = $disbursement->payrolls;
        if ($disbursementPayrolls->count() ===
            $disbursementPayrolls->where('status', PayrollEnumerator::STATUS_CANCELLED)->count()
        ) {
            $disbursement->state()->cancel();
        }
    }
}
