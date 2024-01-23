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

    /**
     * @OA\Get(
     *     path="/api/companies/{company-slug}/employees/{employee-id}/time-records",
     *     summary="Get time records for a specific employee within a date range",
     *     security={{"bearerAuth":{}}},
     *     tags={"Time Records"},
     *     @OA\Parameter(
     *         name="company-slug",
     *         in="path",
     *         required=true,
     *         description="Slug of the company",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="employee-id",
     *         in="path",
     *         required=true,
     *         description="ID of the employee",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="date_from",
     *         in="query",
     *         required=true,
     *         description="Start date of the time records (YYYY-MM-DD)",
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Parameter(
     *         name="date_to",
     *         in="query",
     *         required=true,
     *         description="End date of the time records (YYYY-MM-DD)",
     *         @OA\Schema(type="string", format="date")
     *     ),
     *     @OA\Parameter(
     *         name="sort",
     *         in="query",
     *         description="Sort order for time records. Prefix with '-' for descending order (e.g., -id)",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Time records retrieved successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Time records retrieved successfully")
     *         )
     *     ),
     *     @OA\Response(response="404", description="Employee not found"),
     *     @OA\Response(response="500", description="Internal server error")
     * )
     */
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

    /**
     * @OA\Post(
     *     path="/api/companies/{company-slug}/employees/{employee-id}/time-records",
     *     summary="Create a new time record for a given employee",
     *     security={{"bearerAuth":{}}},
     *     tags={"Time Records"},
     *     @OA\Parameter(
     *         name="company-slug",
     *         in="path",
     *         required=true,
     *         description="Slug of the company",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="employee-id",
     *         in="path",
     *         required=true,
     *         description="ID of the employee",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"clock_in", "clock_out", "expected_clock_in", "expected_clock_out"},
     *             @OA\Property(property="clock_in", type="string", format="date-time", example="2023-09-27 01:42:04"),
     *             @OA\Property(property="clock_out", type="string", format="date-time", example="2023-09-27 02:14:11"),
     *             @OA\Property(property="expected_clock_in", type="string", format="date-time", example="2023-06-26 13:00:00"),
     *             @OA\Property(property="expected_clock_out", type="string", format="date-time", example="2023-06-26 17:00:00"),
     *             @OA\Property(property="attendance_status", type="string", example="present", nullable=true),
     *             @OA\Property(property="remarks", type="string", example="Late arrival", nullable=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response="201",
     *         description="Time record created successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Time record created successfully")
     *         )
     *     ),
     *     @OA\Response(response="400", description="Invalid input data"),
     *     @OA\Response(response="404", description="Employee not found"),
     *     @OA\Response(response="500", description="Internal server error")
     * )
     */
    public function store(TimeRecordRequest $request, Company $company, int $employeeId): JsonResponse
    {
        $employee = $company->getEmployeeById($employeeId);
        $input = $request->validated();
        $input['employee_id'] = $employee->id;
        return TimeRecord::create($input);
    }

    /**
     * @OA\Get(
     *     path="/api/companies/{company-slug}/employees/{employee-id}/time-records/{time-record-id}",
     *     summary="Get a specific time record for a given employee",
     *     security={{"bearerAuth":{}}},
     *     tags={"Time Records"},
     *     @OA\Parameter(
     *         name="company-slug",
     *         in="path",
     *         required=true,
     *         description="Slug of the company",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="employee-id",
     *         in="path",
     *         required=true,
     *         description="ID of the employee",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="time-record-id",
     *         in="path",
     *         required=true,
     *         description="ID of the time record",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Time record retrieved successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Time record retrieved successfully")
     *         )
     *     ),
     *     @OA\Response(response="404", description="Employee or time record not found"),
     *     @OA\Response(response="500", description="Internal server error")
     * )
     */
    public function show(Company $company, int $employeeId, int $timeRecordId): JsonResponse
    {
        $employee = $company->getEmployeeById($employeeId);
        $timeRecord = $employee->timeRecords()->findOrFail($timeRecordId);
        return $this->sendResponse(new BaseResource($timeRecord), 'Time records updated successfully.');
    }

    /**
     * @OA\Put(
     *     path="/api/companies/{company-slug}/employees/{employee-id}/time-records/{time-record-id}",
     *     summary="Update an existing time record for a given employee",
     *     security={{"bearerAuth":{}}},
     *     tags={"Time Records"},
     *     @OA\Parameter(
     *         name="company-slug",
     *         in="path",
     *         required=true,
     *         description="Slug of the company",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="employee-id",
     *         in="path",
     *         required=true,
     *         description="ID of the employee",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="time-record-id",
     *         in="path",
     *         required=true,
     *         description="ID of the time record",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\RequestBody(
     *         required=true,
     *         @OA\JsonContent(
     *             required={"clock_in", "clock_out", "expected_clock_in", "expected_clock_out"},
     *             @OA\Property(property="clock_in", type="string", format="date-time", example="2023-09-27 01:42:04"),
     *             @OA\Property(property="clock_out", type="string", format="date-time", example="2023-09-27 02:14:11"),
     *             @OA\Property(property="expected_clock_in", type="string", format="date-time", example="2023-06-26 13:00:00"),
     *             @OA\Property(property="expected_clock_out", type="string", format="date-time", example="2023-06-26 17:00:00"),
     *             @OA\Property(property="attendance_status", type="string", example="present", nullable=true),
     *             @OA\Property(property="remarks", type="string", example="Late arrival", nullable=true)
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Time record updated successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Time record updated successfully")
     *         )
     *     ),
     *     @OA\Response(response="400", description="Invalid input data"),
     *     @OA\Response(response="404", description="Employee or time record not found"),
     *     @OA\Response(response="500", description="Internal server error")
     * )
     */
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

    /**
     * @OA\Delete(
     *     path="/api/companies/{company-slug}/employees/{employee-id}/time-records/{time-record-id}",
     *     summary="Delete a time record for a given employee",
     *     security={{"bearerAuth":{}}},
     *     tags={"Time Records"},
     *     @OA\Parameter(
     *         name="company-slug",
     *         in="path",
     *         required=true,
     *         description="Slug of the company",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="employee-id",
     *         in="path",
     *         required=true,
     *         description="ID of the employee",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="time-record-id",
     *         in="path",
     *         required=true,
     *         description="ID of the time record",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Time record deleted successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Time record deleted successfully")
     *         )
     *     ),
     *     @OA\Response(response="404", description="Employee or time record not found"),
     *     @OA\Response(response="500", description="Internal server error")
     * )
     */
    public function destroy(Company $company, int $employeeId, int $timeRecordId): JsonResponse
    {
        $employee = $company->getEmployeeById($employeeId);
        $timeRecord = $employee->timeRecords()->findOrFail($timeRecordId);
        $timeRecord->delete();
        return $this->sendResponse(new BaseResource($timeRecord), 'Time records deleted successfully.');
    }

    /**
     * @OA\Post(
     *     path="/api/companies/{company-slug}/employees/{employee-id}/clock",
     *     summary="Clock in or out for a given employee",
     *     security={{"bearerAuth":{}}},
     *     tags={"Time In / Time Out"},
     *     @OA\Parameter(
     *         name="company-slug",
     *         in="path",
     *         required=true,
     *         description="Slug of the company",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="employee-id",
     *         in="path",
     *         required=true,
     *         description="ID of the employee",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Clock action recorded successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Clock action recorded successfully")
     *         )
     *     ),
     *     @OA\Response(response="404", description="Employee not found"),
     *     @OA\Response(response="500", description="Internal server error")
     * )
     */
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
