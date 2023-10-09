<?php

namespace App\Http\Controllers;

use App\Http\Requests\TimeRecordRequest;
use App\Http\Resources\BaseResource;
use App\Models\Company;
use App\Models\TimeRecord;
use App\Services\ClockService;
use App\Services\TimeRecordService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class TimeRecordController extends Controller
{
    protected $clockService;

    protected $timeRecordService;

    public function __construct(ClockService $clockService, TimeRecordService $timeRecordService)
    {
        $this->clockService = $clockService;
        $this->timeRecordService = $timeRecordService;
    }

    public function index(Request $request, Company $company, int $employeeId): JsonResponse
    {
        $employee = $company->getEmployeeById($employeeId);
        $timeRecords = $this->timeRecordService->getTimeRecordsByDateRange(
            $employee->timeRecords(),
            $request->date_from,
            $request->date_to,
        );
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

        if (!$timeRecord->original_clock_in && $timeRecord->clock_in != $input['clock_in']) {
            $input['original_clock_in'] = $timeRecord->clock_in;
        }

        if (!$timeRecord->original_clock_out && $timeRecord->clock_out != $input['clock_out']) {
            $input['original_clock_out'] = $timeRecord->clock_out;
        }

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
            list($timeRecord, $message) = $this->clockService->clockAction($employee);
            return $this->sendResponse(new BaseResource($timeRecord), $message);
        } catch (Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }
}
