<?php

namespace App\Services;

use App\Models\Employee;
use App\Models\SalaryComputation;

class SalaryComputationService
{
    private const MONTHS_12 = 12;

    public function initialize(Employee $employee, array $data): SalaryComputation
    {
        if ($employee->employment_type == Employee::FULL_TIME) {
            $workDaysPerWeek = Employee::FIVE_DAYS_PER_WEEK
                ? SalaryComputation::FIVE_DAYS_PER_WEEK_WORK_DAYS
                : SalaryComputation::SIX_DAYS_PER_WEEK_WORK_DAYS;
            $data['daily_rate'] = $data['basic_salary'] * self::MONTHS_12 / $workDaysPerWeek;
            $data['hourly_rate'] = $data['daily_rate'] / $employee->working_hours_per_day;
        }
        return SalaryComputation::create($data);
    }
}
