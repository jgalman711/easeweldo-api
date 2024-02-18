<?php

namespace App\Http\Controllers;

use App\Http\Requests\EmployeeScheduleRequest;
use App\Http\Resources\BaseResource;
use App\Http\Resources\EmployeeScheduleResource;
use App\Models\Company;
use App\Models\Employee;
use App\Models\EmployeeSchedule;
use App\Models\WorkSchedule;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EmployeeScheduleController extends Controller
{
    public function __construct()
    {
        $this->setCacheIdentifier('employee-schedules');
    }

    /**
     * @OA\Get(
     *     path="/api/companies/{company-slug}/employees/{employee-id}/work-schedules",
     *     summary="List Employee Schedules",
     *     security={{"bearerAuth":{}}},
     *     tags={"Employee Schedules"},
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
     *         name="page",
     *         in="query",
     *         description="Page number for pagination",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="per_page",
     *         in="query",
     *         description="Number of items per page",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Parameter(
     *         name="sort",
     *         in="query",
     *         description="Sort order (e.g., name)",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Parameter(
     *         name="search",
     *         in="query",
     *         description="Search term",
     *         @OA\Schema(type="string")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Employee Schedules retrieved successfully"
     *     ),
     *     @OA\Response(
     *         response="404",
     *         description="No employee schedules found"
     *     )
     * )
     */
    public function index(Request $request, Company $company, Employee $employee)
    {
        try {
            $company->getEmployeeById($employee->id);
            $employeeSchedule = $this->applyFilters($request, $employee->employeeSchedules(), [
                'name'
            ]);
            return $this->sendResponse(
                EmployeeScheduleResource::collection($employeeSchedule),
                'Employee schedules retrieved successfully.'
            );
        } catch (Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    /**
     * @OA\Post(
     *     path="/api/companies/{company-slug}/employees/{employee-id}/work-schedules",
     *     summary="Create Employee Schedule",
     *     security={{"bearerAuth":{}}},
     *     tags={"Employee Schedules"},
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
     *             @OA\Property(
     *                  property="work_schedule_id",
     *                  type="integer",
     *                  description="ID of the work schedule",
     *                  example="123"
     *             ),
     *             @OA\Property(
     *                  property="start_date",
     *                  type="string",
     *                  format="date",
     *                  description="Start date of the schedule",
     *                  example="2024-01-15"
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Employee Schedule created successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Employee Schedule created successfully."),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="work_schedule_id", type="integer", example=123),
     *                 @OA\Property(property="start_date", type="string", format="date", example="2024-01-15"),
     *                 @OA\Property(property="created_at", type="string", format="date-time", example="2024-01-15T12:00:00Z")
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response="422",
     *         description="Validation errors",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="The given data was invalid.")
     *         )
     *     )
     * )
     */
    public function store(EmployeeScheduleRequest $request, Company $company, int $employeeId): JsonResponse
    {
        try {
            $employee = $company->getEmployeeById($employeeId);
            $input = $request->validated();
            $company->getWorkScheduleById($request->work_schedule_id);
            $input['employee_id'] = $employee->id;
            $employeeSchedule = EmployeeSchedule::firstOrCreate($input);
            $this->forget($company);
            return $this->sendResponse(
                new EmployeeScheduleResource($employeeSchedule),
                'Employee schedule created successfully.'
            );
        } catch (Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    /**
     * @OA\Get(
     *     path="/api/companies/{company-slug}/employees/{employee-id}/work-schedules/{schedule-id}",
     *     summary="Get Employee Schedule",
     *     security={{"bearerAuth":{}}},
     *     tags={"Employee Schedules"},
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
     *         name="schedule-id",
     *         in="path",
     *         required=true,
     *         description="ID of the employee schedule or 'latest' to get the latest schedule",
     *         @OA\Schema(type="string", default="latest"),
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Employee Schedule retrieved successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Employee Schedule retrieved successfully."),
     *             @OA\Property(
     *                 property="data",
     *                 type="object",
     *                 @OA\Property(property="work_schedule_id", type="integer", example=123),
     *                 @OA\Property(property="start_date", type="string", format="date", example="2024-01-15"),
     *                 @OA\Property(
     *                      property="created_at",
     *                      type="string",
     *                      format="date-time",
     *                      example="2024-01-15T12:00:00Z"
     *                 )
     *             )
     *         )
     *     ),
     *     @OA\Response(
     *         response="404",
     *         description="Employee Schedule not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Employee Schedule not found.")
     *         )
     *     )
     * )
     */
    public function show(Company $company, Employee $employee, $employeeSchedule): JsonResponse
    {
        try {
            $company->getEmployeeById($employee->id);
            if ($employeeSchedule === 'latest') {
                $employeeSchedule = $employee->employeeSchedules()
                    ->latest('start_date')
                    ->first();
            } else {
                $employeeSchedule = $employee->employeeSchedules()->find($employeeSchedule);
            }
            throw_unless(
                $employeeSchedule,
                'Work schedule not found.'
            );
            return $this->sendResponse(
                new EmployeeScheduleResource($employeeSchedule),
                'Employee schedule retrieved successfully.'
            );
        } catch (Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    /**
     * @OA\Delete(
     *     path="/api/companies/{company-slug}/employees/{employee-id}/work-schedules/{schedule-id}",
     *     summary="Delete Employee Schedule",
     *     security={{"bearerAuth":{}}},
     *     tags={"Employee Schedules"},
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
     *         name="schedule-id",
     *         in="path",
     *         required=true,
     *         description="ID of the employee schedule",
     *         @OA\Schema(type="integer")
     *     ),
     *     @OA\Response(
     *         response="200",
     *         description="Employee Schedule deleted successfully",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=true),
     *             @OA\Property(property="message", type="string", example="Employee Schedule deleted successfully.")
     *         )
     *     ),
     *     @OA\Response(
     *         response="404",
     *         description="Employee Schedule not found",
     *         @OA\JsonContent(
     *             type="object",
     *             @OA\Property(property="success", type="boolean", example=false),
     *             @OA\Property(property="message", type="string", example="Employee Schedule not found.")
     *         )
     *     )
     * )
     */
    public function destroy(Company $company, Employee $employee, int $employeeScheduleId): JsonResponse
    {
        try {
            $company->getEmployeeById($employee->id);
            $employeeSchedule = $employee->employeeSchedules()->find($employeeScheduleId);
            throw_unless(
                $employeeSchedule,
                'Work schedule not found.'
            );
            $employeeSchedule->delete();
            return $this->sendResponse(
                new EmployeeScheduleResource($employeeSchedule),
                'Employee schedule retrieved successfully.'
            );
        } catch (Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }
}
