<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\Leave;
use Carbon\Carbon;
use Exception;
class LeaveService
{
    protected $timeRecordService;

    public function __construct(TimeRecordService $timeRecordService)
    {
        $this->timeRecordService = $timeRecordService;
    }

    public function apply(Employee $employee, array $data): array
    {
        $leaves = [];
        $fromDate = Carbon::parse($data['from_date']);
        $toDate = Carbon::parse($data['to_date']);
        $days = $fromDate->diffInDays($toDate) + 1;
        if ($data['type'] !== Leave::TYPE_WITHOUT_PAY) {
            $availableHoursLeaveType = "available_{$data['type']}_hours";
            $remainingLeaveHours = $employee->salaryComputation->{$availableHoursLeaveType};
            $totalHoursLeave = $data['hours'] * $days;
            throw_if(
                $remainingLeaveHours <= $totalHoursLeave,
                new Exception('No available leaves left for this type.')
            );
        }
        $now = Carbon::now();
        while ($fromDate->lte($toDate)) {
            $leave = Leave::create([
                'company_id' => $employee->company->id,
                'employee_id' => $employee->id,
                'created_by' => $employee->user->id,
                'type' => $data['type'],
                'description' => $data['description'],
                'hours' => $data['hours'],
                'date' => $fromDate,
                'submitted_date' => $now->toDateString(),
                'remarks' => $data['remarks'] ?? null,
                'status' => Leave::PENDING,
            ]);
            array_push($leaves, $leave);
            $fromDate->addDay();
        }
        return $leaves;
    }
}
