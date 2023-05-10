<?php

namespace App\Http\Controllers;

use App\Http\Resources\BaseResource;
use App\Models\Company;
use App\Services\TimeRecordService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;

class TimeRecordController extends Controller
{
    protected $timeRecordService;

    public function __construct(TimeRecordService $timeRecordService)
    {
        $this->timeRecordService = $timeRecordService;
    }

    public function clockIn(Company $company, int $employeeId)
    {
        try {
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
        } catch (Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    public function clockOut(Company $company, int $employeeId): JsonResponse
    {
        try {
            $employee = $company->getEmployeeById($employeeId);
            $latestTimeRecord = $employee->timeRecords()->latest()->first();
            if ($latestTimeRecord && $latestTimeRecord->clock_out || !$latestTimeRecord) {
                return $this->sendError('Employee is already clocked out');
            }
            $latestTimeRecord->clock_out = now()->format('H:i:s');
            $latestTimeRecord->save();
            return $this->sendResponse(new BaseResource($latestTimeRecord), 'Clock out successful.');
        } catch (Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    public function clock(Company $company, int $employeeId): JsonResponse
    {
        try {
            $employee = $company->getEmployeeById($employeeId);

            $latestTimeRecord = $employee->timeRecords()->latest()->first();
            $currentTime = Carbon::now();
            $isRecentlyClocked = false;
            if (!$latestTimeRecord || ($latestTimeRecord->clock_in && $latestTimeRecord->clock_out)) {
                if ($currentTime->diffInMinutes($latestTimeRecord->clock_out) <= 1) {
                    $message = 'You cannot clock in yet. Please wait for at least 1 minute.';
                    $isRecentlyClocked = true;
                }
                $latestTimeRecord = $this->timeRecordService->create($employee);
            } elseif (!$latestTimeRecord->clock_out && $currentTime->diffInMinutes($latestTimeRecord->clock_in) <= 1) {
                $message = 'You cannot clock out yet. Please wait for at least 1 minute.';
                $isRecentlyClocked = true;
            }

            if ($isRecentlyClocked) {
                return $this->sendError($message);
            }

            if ($latestTimeRecord->clock_in == null) {
                $latestTimeRecord->clock_in = now()->format('H:i:s');
                $message = 'Clock in successful.';
            } else {
                $latestTimeRecord->clock_out = now()->format('H:i:s');
                $message = 'Clock out successful.';
            }
            $latestTimeRecord->save();

            return $this->sendResponse(new BaseResource($latestTimeRecord), $message);
        } catch (Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    public function getTimeRecords(Company $company, int $employeeId)
    {
        $employee = $company->getEmployeeById($employeeId);
        return $this->sendResponse(new BaseResource($employee->timeRecords), 'Time records retrieved successfully.');
    }
}
