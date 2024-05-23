<?php

namespace App\Services;

use App\Enumerators\LeaveEnumerator;
use App\Models\Employee;
use App\Models\Leave;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Collection;
use Illuminate\Support\Facades\Auth;

class LeaveService
{
    protected $timeRecordService;

    public function __construct(TimeRecordService $timeRecordService)
    {
        $this->timeRecordService = $timeRecordService;
    }

    public function apply(Employee $employee, array $data): Collection
    {
        $leaves = [];
        $fromDate = Carbon::parse($data['from_date']);
        $toDate = Carbon::parse($data['to_date']);
        $days = $fromDate->diffInDays($toDate) + 1;
        if ($data['type'] !== LeaveEnumerator::TYPE_WITHOUT_PAY) {
            $availableHoursLeaveType = "available_{$data['type']}_hours";
            $remainingLeaveHours = $employee->salaryComputation->{$availableHoursLeaveType};
            $totalHoursLeave = $data['hours'] * $days;
            throw_if(
                $remainingLeaveHours <= $totalHoursLeave,
                new Exception('No available leaves left for this type.')
            );
        }
        $now = Carbon::now();
        $date = $now->toDateString();
        while ($fromDate->lte($toDate)) {
            $leaves[] = [
                'company_id' => $employee->company->id,
                'employee_id' => $employee->id,
                'created_by' => Auth::id(),
                'title' => $data['title'],
                'type' => $data['type'],
                'description' => $data['description'],
                'hours' => $data['hours'],
                'date' => $fromDate->toDateString(),
                'submitted_date' => $date,
                'remarks' => $data['remarks'] ?? null,
                'status' => LeaveEnumerator::SUBMITTED,
                'created_at' => $now,
                'updated_at' => $now
            ];
            $fromDate->addDay();
        }
        Leave::insert($leaves);
        return collect($leaves);
    }
}
