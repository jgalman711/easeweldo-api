<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\SalaryComputation;

class SalaryComputationService
{
    private const MONTHS_12 = 12;

    public function initialize(Employee $employee, array $data): SalaryComputation
    {
        $data['employee_id'] = $employee->id;
        if ($employee->employment_type == Employee::FULL_TIME) {
            $data['working_hours_per_day'] = $data['working_hours_per_day'] ?? Employee::EIGHT_HOURS_PER_DAY;
            $data['working_days_per_week'] = $data['working_days_per_week'] ?? Employee::FIVE_DAYS_PER_WEEK;
            $data['daily_rate'] = $data['basic_salary']
                * self::MONTHS_12
                / SalaryComputation::FIVE_DAYS_PER_WEEK_WORK_DAYS;
            $data['hourly_rate'] = $data['daily_rate'] / Employee::EIGHT_HOURS_PER_DAY;
            $data['available_sick_leave_hours'] = $data['available_sick_leave_hours'] ?? $data['total_sick_leave_hours'];
            $data['available_vacation_leave_hours'] = $data['available_vacation_leave_hours'] ?? $data['total_vacation_leave_hours'];
        }
        return SalaryComputation::create($data);
    }
}
