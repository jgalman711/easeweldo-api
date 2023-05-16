<?php

namespace App\Http\Controllers;

use App\Http\Requests\EmployeeScheduleRequest;
use App\Http\Resources\BaseResource;
use App\Models\Company;
use App\Models\EmployeeSchedule;
use Exception;
use Illuminate\Http\JsonResponse;

class EmployeeScheduleController extends Controller
{
    public function store(EmployeeScheduleRequest $request, Company $company, int $employeeId): JsonResponse
    {
        try {
            $employee = $company->getEmployeeById($employeeId);
            $input = $request->validated();
            $company->getWorkScheduleById($request->work_schedule_id);
            $input['employee_id'] = $employee->id;
            $employeeSchedule = EmployeeSchedule::firstOrCreate($input);
            return $this->sendResponse(new BaseResource($employeeSchedule), 'Employee schedule created successfully.');
        } catch (Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    public function show(Company $company, int $employeeId): JsonResponse
    {
        try {
            $employee = $company->getEmployeeById($employeeId);
            return $this->sendResponse(
                new BaseResource($employee->schedules),
                'Employee schedule retrieved successfully.'
            );
        } catch (Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }
}
