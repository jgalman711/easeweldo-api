<?php

namespace App\StateMachines\Payroll;

use App\Enumerators\PayrollEnumerator;

class ToPayState extends BaseState
{
    public function pay(): void
    {
        $this->payroll->update(['status' => PayrollEnumerator::STATUS_PAID]);
    }

    public function cancel(): void
    {
        $this->payroll->update(['status' => PayrollEnumerator::STATUS_CANCELLED]);
    }
}
