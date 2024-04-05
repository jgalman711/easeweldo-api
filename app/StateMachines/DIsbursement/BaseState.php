<?php

namespace App\StateMachines\Disbursement;

use App\Exceptions\InvalidStateTransitionException;
use App\Models\Period as Disbursement;
use App\StateMachines\Contracts\DisbursementStateContract;

class BaseState implements DisbursementStateContract
{
    protected $disbursement;

    public function __construct(Disbursement $disbursement)
    {
        $this->disbursement = $disbursement;
    }

    public function initialize(): void
    {
        throw new InvalidStateTransitionException();
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
