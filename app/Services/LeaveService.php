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

    public function applyLeave(Employee $employee, array $data): Leave
    {
        $createdByUser = Auth::user();
        $data['created_by'] = $createdByUser->id;
        if ($createdByUser->hasRole('business-admin') || $createdByUser->hasRole('super-admin')) {
            $data['status'] = Leave::APPROVED;
            $this->insertToTimeRecords($employee, $data);
        } else {
            $data['status'] = Leave::PENDING;
        }
        return Leave::create($data);
    }

    public function insertToTimeRecords(Employee $employee, array $data): TimeRecord
    {
        $timeRecord = TimeRecord::whereDate(
            'created_at',
            '=',
            date('Y-m-d', strtotime($data['start_date']))
        )->where('employee_id', $employee->id)->first();
        if (!$timeRecord) {
            $timeRecord = $this->timeRecordService->create($employee, $data['start_date'], $data['end_date']);
        }
        $timeRecord->clock_in = $data['start_date'];
        $timeRecord->clock_out = $data['end_date'];
        $timeRecord->save();
        return $timeRecord;
    }
}
