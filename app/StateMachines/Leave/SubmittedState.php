'<?php

namespace App\StateMachines\Leave;

use App\Enumerators\LeaveEnumerator;
use App\Models\Leave;

class SubmittedState extends BaseState
{
    public function approve(string $reason = null): void
    {
        $this->leave->update([
            'status' => LeaveEnumerator::APPROVED,
            'remarks' => $reason
        ]);
    
        if ($this->leave->type !== LeaveEnumerator::TYPE_WITHOUT_PAY) {
            $this->leave->load('employee.salaryComputation');
            $salaryComputation = $this->leave->employee->salaryComputation;
            if ($this->leave->type == LeaveEnumerator::TYPE_SICK_LEAVE) {
                $salaryComputation->available_sick_leave_hours -= $this->leave->hours;
            } elseif ($this->leave->type == LeaveEnumerator::TYPE_VACATION_LEAVE) {
                $salaryComputation->available_vacation_leave_hours -= $this->leave->hours;
            }
            $salaryComputation->save();
        }
    }

    public function decline(string $reason = null): void
    {
        $this->leave->update([
            'status' => LeaveEnumerator::DECLINED,
            'remarks' => $reason
        ]);
    }

    public function discard(string $reason = null): void
    {
        $this->leave->update([
            'status' => LeaveEnumerator::DISCARDED,
            'remarks' => $reason
        ]);
    }
}
