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
                $latestTimeRecord->clock_in = Carbon::now();
                $message = 'Clock in successful.';
            } else {
                $latestTimeRecord->clock_out = Carbon::now();
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
