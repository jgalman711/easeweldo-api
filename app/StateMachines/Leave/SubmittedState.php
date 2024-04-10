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
        $this->leave->load('employee.salaryComputation');
        $salaryComputation = $this->leave->employee->salaryComputation;
        if ($this->leave->type == LeaveEnumerator::TYPE_SICK_LEAVE) {
            $salaryComputation->available_sick_leave_hours -= $this->leave->hours;
        } elseif ($this->leave->type == LeaveEnumerator::TYPE_VACATION_LEAVE) {
            $salaryComputation->available_vacation_leave_hours -= $this->leave->hours;
        }
        $salaryComputation->save();
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
