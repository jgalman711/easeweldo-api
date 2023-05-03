<?php

namespace App\Http\Controllers;

use App\Http\Requests\EmployeeScheduleRequest;
use App\Http\Resources\BaseResource;
use App\Models\Employee;
use App\Models\EmployeeSchedule;
use App\Models\WorkSchedule;
use Illuminate\Http\JsonResponse;

class EmployeeScheduleController extends Controller
{
    public function store(EmployeeScheduleRequest $request, Employee $employee): JsonResponse
    {
        $input = $request->validated();
        $workSchedule = WorkSchedule::find($input['work_schedule_id']);
        if (!$workSchedule || $employee->company_id != $workSchedule->company_id) {
            return $this->sendError("Work schedule not found.");
        }
        $input['employee_id'] = $employee->id;
        $employeeSchedule = EmployeeSchedule::firstOrCreate($input);
        return $this->sendResponse(new BaseResource($employeeSchedule), 'Employee schedule created successfully.');
    }
}
