<?php

namespace App\Services;

use App\Http\Requests\EmployeeScheduleRequest;
use App\Models\Employee;
use App\Models\EmployeeSchedule;
use App\Models\WorkSchedule;

class EmployeeScheduleService
{
    public function create(EmployeeScheduleRequest $request, Employee $employee): EmployeeSchedule
    {
        $input = $request->validated();
        if ($request->has('work_schedule_id') && $request->work_schedule_id == 'custom') {
            $workSchedule = WorkSchedule::updateOrCreate([
                'company_id' => $employee->company->id,
                'type' => WorkSchedule::CUSTOM,
                'name' => WorkSchedule::CUSTOM.'-'.lcfirst($employee->last_name),
            ], $request->all());
            unset($input['work_schedule_id']);
        } else {
            $workSchedule = WorkSchedule::findOrFail($request->work_schedule_id);
        }

        return EmployeeSchedule::updateOrCreate([
            'employee_id' => $employee->id,
            'work_schedule_id' => $workSchedule->id,
            'start_date' => $request->start_date,
        ], $input);
    }
}
