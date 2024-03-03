<?php

namespace App\StateMachine\Payroll;

use App\Exceptions\InvalidStateTransitionException;
use App\Models\Payroll;
use App\StateMachine\Contracts\PayrollStateContract;

class BaseState implements PayrollStateContract
{
    protected $payroll;

    public function __construct(Payroll $payroll)
    {
        $this->payroll = $payroll;
    }

    public function pay(): void
    {
        throw new InvalidStateTransitionException();
    }

    public function cancel(): void
    {
        throw new InvalidStateTransitionException();
    }
}
