<?php

namespace App\Http\Controllers;

use App\Http\Requests\TimeRecordRequest;
use App\Http\Resources\BaseResource;
use App\Models\Company;
use App\Models\TimeRecord;
use App\Services\TimeRecordService;
use App\Traits\Filter;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TimeRecordController extends Controller
{
    use Filter;

    protected $timeRecordService;

    public function __construct(TimeRecordService $timeRecordService)
    {
        $this->timeRecordService = $timeRecordService;
    }

    public function index(Request $request, Company $company, int $employeeId): JsonResponse
    {
        $employee = $company->getEmployeeById($employeeId);
        $timeRecords = $this->timeRecordService->getTimeRecordsByDateRange($request, $employee->timeRecords());
        $timeRecords = $this->applyFilters($request, $timeRecords);
        return $this->sendResponse(BaseResource::collection($timeRecords), 'Time records retrieved successfully.');
    }

    public function store(TimeRecordRequest $request, Company $company, int $employeeId): JsonResponse
    {
        $employee = $company->getEmployeeById($employeeId);
        $input = $request->validated();
        $input['employee_id'] = $employee->id;
        return TimeRecord::create($input);
    }

    public function update(
        TimeRecordRequest $request,
        Company $company,
        int $employeeId,
        int $timeRecordId
    ): JsonResponse {
        $employee = $company->getEmployeeById($employeeId);
        $timeRecord = $employee->timeRecords()->findOrFail($timeRecordId);
        $input = $request->validated();
        $timeRecord->update($input);
        return $this->sendResponse(new BaseResource($timeRecord), 'Time records updated successfully.');
    }

    public function destroy(Company $company, int $employeeId, int $timeRecordId): JsonResponse
    {
        $employee = $company->getEmployeeById($employeeId);
        $timeRecord = $employee->timeRecords()->findOrFail($timeRecordId);
        $timeRecord->delete();
        return $this->sendResponse(new BaseResource($timeRecord), 'Time records updated successfully.');
    }

    public function clock(Company $company, int $employeeId): JsonResponse
    {
        try {
            $employee = $company->getEmployeeById($employeeId);
            $currentTime = Carbon::now();
            $currentDate = $currentTime->copy()->format('Y-m-d');
            $timeRecord = $employee->timeRecords()->firstOrNew([
                'expected_clock_in' => $currentDate,
                'attendance_status' => null
            ]);
            $timeRecord->company_id = $company->id;
            if (!$timeRecord) {
                $timeRecord = new TimeRecord();
                $timeRecord->employee_id = $employeeId;
            }

            if ($timeRecord->clock_in == null) {
                $timeRecord->clock_in = $currentTime;
                $message = 'Clock in successful.';
            } elseif ($timeRecord->clock_out == null) {
                throw_if(
                    $currentTime->diffInMinutes($timeRecord->clock_in) <= 1,
                    new Exception("You cannot clock out yet. Please wait for at least 1 minute.")
                );
                $timeRecord->clock_out = $currentTime;
                $message = 'Clock out successful.';
            } else {
                throw new Exception("Time record creation failed. User already clocked out.");
            }
            $timeRecord->save();
            return $this->sendResponse(new BaseResource($timeRecord), $message);
        } catch (Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }
}
