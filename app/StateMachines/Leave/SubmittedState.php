<?php

namespace App\StateMachines\Leave;

use App\Enumerators\LeaveEnumerator;
use App\Models\Leave;
use Illuminate\Support\Facades\Auth;

class SubmittedState extends BaseState
{
    protected $user;

    public function __construct(Leave $leave)
    {
        parent::__construct($leave);
        $this->user = Auth::user();
    }

    public function approve(string $reason = null): void
    {
        $this->leave->update([
            'status' => LeaveEnumerator::APPROVED,
            'remarks' => $reason
        ]);
        $this->leave->approve($reason, $this->user);
    }

    public function decline(string $reason = null): void
    {
        $this->leave->update([
            'status' => LeaveEnumerator::DECLINED,
            'remarks' => $reason
        ]);
        $this->leave->decline($reason, $this->user);
    }

    public function discard(string $reason = null): void
    {
        $this->leave->update([
            'status' => LeaveEnumerator::DISCARDED,
            'remarks' => $reason
        ]);
        $this->leave->discard($reason, $this->user);
    }
}
