<?php

namespace App\Http\Controllers;

use App\Http\Requests\EmployeeScheduleRequest;
use App\Http\Resources\BaseResource;
use App\Http\Resources\EmployeeScheduleResource;
use App\Models\Company;
use App\Models\EmployeeSchedule;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class EmployeeScheduleController extends Controller
{
    public function __construct()
    {
        $this->setCacheIdentifier('employee-schedules');
    }

    public function index(Request $request, Company $company, int $employeeId)
    {
        try {
            $employee = $company->getEmployeeById($employeeId);
            $employeeSchedule = $this->applyFilters($request, $employee->schedules()->withPivot('start_date'), [
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

    public function store(EmployeeScheduleRequest $request, Company $company, int $employeeId): JsonResponse
    {
        try {
            $employee = $company->getEmployeeById($employeeId);
            $input = $request->validated();
            $company->getWorkScheduleById($request->work_schedule_id);
            $input['employee_id'] = $employee->id;
            $employeeSchedule = EmployeeSchedule::firstOrCreate($input);
            $this->forget($company);
            return $this->sendResponse(new BaseResource($employeeSchedule), 'Employee schedule created successfully.');
        } catch (Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    public function show(Company $company, int $employeeId, int $scheduleId): JsonResponse
    {
        try {
            $employeeSchedule = $this->remember($company, function () use ($company, $employeeId, $scheduleId) {
                $employee = $company->getEmployeeById($employeeId);
                return $employee->schedules->where('id', $scheduleId);
            }, $scheduleId);
            return $this->sendResponse(
                new BaseResource($employeeSchedule),
                'Employee schedule retrieved successfully.'
            );
        } catch (Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    public function update(Company $company, int $employeeId, int $scheduleId): JsonResponse
    {
        try {
            $employee = $company->getEmployeeById($employeeId);
            $employeeSchedule = $employee->schedules->where('id', $scheduleId);
            $this->forget($company, $scheduleId);
            return $this->sendResponse(
                new BaseResource($employeeSchedule),
                'Employee schedule updated successfully.'
            );
        } catch (Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    public function destroy(Company $company, int $employeeId, int $scheduleId): JsonResponse
    {
        try {
            $employee = $company->getEmployeeById($employeeId);
            $employee->schedules()->detach($scheduleId);
            $this->forget($company, $scheduleId);
            return response()->json([
                'message' =>  'Employee schedule deleted successfully.'
            ], 200);
        } catch (Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }
}
