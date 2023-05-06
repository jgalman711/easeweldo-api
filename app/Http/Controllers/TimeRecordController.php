<?php

namespace App\Http\Controllers;

use App\Http\Resources\BaseResource;
use App\Models\Company;
use App\Models\TimeRecord;
use App\Services\TimeRecordService;
use Carbon\Carbon;

class TimeRecordController extends Controller
{
    protected $timeRecordService;

    public function __construct(TimeRecordService $timeRecordService)
    {
        $this->timeRecordService = $timeRecordService;
    }

    public function clockIn(Company $company, int $employeeId)
    {
        $employee = $company->getEmployeeById($employeeId);
        $latestTimeRecord = $employee->timeRecords()
            ->whereDate('created_at', Carbon::today())
            ->first();
        if ($latestTimeRecord && $latestTimeRecord->clock_in != null && $latestTimeRecord->clockOut == null) {
            return $this->sendError('Employee is already clocked in');
        }
        if (!$latestTimeRecord) {
            $latestTimeRecord = $this->timeRecordService->create($employee);
        }
        $latestTimeRecord->employee_id = $employee->id;
        $latestTimeRecord->clock_in = now()->format('H:i:s');
        $latestTimeRecord->save();
        return $this->sendResponse(new BaseResource($latestTimeRecord), 'Clock in successful.');
    }

    public function clockOut(Company $company, int $employeeId)
    {
        $employee = $company->getEmployeeById($employeeId);
        $latestTimeRecord = $employee->timeRecords()->latest()->first();
        if ($latestTimeRecord && $latestTimeRecord->clock_out || !$latestTimeRecord) {
            return $this->sendError('Employee is already clocked out');
        }
        $latestTimeRecord->clock_out = now()->format('H:i:s');
        $latestTimeRecord->save();
        return $this->sendResponse(new BaseResource($latestTimeRecord), 'Clock out successful.');
    }

    public function getTimeRecords(Company $company, int $employeeId)
    {
        $employee = $company->getEmployeeById($employeeId);
        return $this->sendResponse(new BaseResource($employee->timeRecords), 'Time records retrieved successfully.');
    }
}
