<?php

namespace App\StateMachines\Leave;

use App\Exceptions\InvalidStateTransitionException;
use App\Models\Leave;
use App\StateMachines\Contracts\LeaveStateContract;

class BaseState implements LeaveStateContract
{
    protected $leave;

    public function __construct(Leave $leave)
    {
        $this->leave = $leave;
    }

    public function approve(): void
    {
        throw new InvalidStateTransitionException();
    }

    public function decline(): void
    {
        throw new InvalidStateTransitionException();
    }

    public function discard(): void
    {
        throw new InvalidStateTransitionException();
    }
}
