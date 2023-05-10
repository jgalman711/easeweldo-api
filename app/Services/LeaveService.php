<?php

namespace App\Services;

use App\Models\Company;
use App\Models\Employee;
use App\Models\Leave;
use App\Models\TimeRecord;
use Illuminate\Support\Facades\Auth;

class LeaveService
{
    protected $timeRecordService;

    public function __construct(TimeRecordService $timeRecordService)
    {
        $this->timeRecordService = $timeRecordService;
    }

    public function getLeaves(Company $company, $employee): array
    {
        $leaves = array();
        if ($employee == 'all') {
            $employees = $company->employees;
            foreach ($employees as $employee) {
                $leaves = array_merge($leaves, $employee->leaves->toArray());
            }
        } else {
            $employee = $company->getEmployeeById($employee);
            $leaves = array_merge($leaves, $employee->leaves->toArray());
        }
        return $leaves;
    }

    public function applyLeave(array $data): Leave
    {
        $createdByUser = Auth::user();
        $data['created_by'] = $createdByUser->id;
        $data['status'] = Leave::PENDING;
        $leave = Leave::create($data);

        if ($createdByUser->hasRole('business-admin') || $createdByUser->hasRole('super-admin')) {
            $this->approve($leave);
        }
        return $leave;
    }

    public function approve(Leave $leave): void
    {
        $leave->status = Leave::APPROVED;
        $leave->save();
        $this->insertToTimeRecords($leave);
    }

    public function insertToTimeRecords(Leave $leave): TimeRecord
    {
        $employee = $leave->employee;
        $timeRecord = TimeRecord::whereDate(
            'created_at',
            '=',
            date('Y-m-d', strtotime($leave->start_date))
        )->where('employee_id', $employee->id)->first();
        if (!$timeRecord) {
            $timeRecord = $this->timeRecordService->create($employee, $leave->start_date, $leave->end_date);
        }
        $timeRecord->clock_in = $leave->start_date;
        $timeRecord->clock_out = $leave->end_date;
        $timeRecord->save();
        return $timeRecord;
    }
}
