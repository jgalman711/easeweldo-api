<?php

namespace App\Services;

use App\Models\SalaryComputation;

class SalaryComputationService
{
    public function initialize(array $data): SalaryComputation
    {
        $data['sick_leaves'] = $data['unit'] == SalaryComputation::UNIT_DAY
            ?  $data['sick_leaves'] : $data['sick_leaves'] * SalaryComputation::EIGHT_HOURS;
        $data['vacation_leaves'] = $data['unit'] == SalaryComputation::UNIT_DAY
            ?  $data['vacation_leaves'] : $data['vacation_leaves'] * SalaryComputation::EIGHT_HOURS;

        $data['available_sick_leaves'] = $data['total_sick_leaves'] = $data['sick_leaves'];
        $data['available_vacation_leaves'] = $data['total_vacation_leaves'] = $data['vacation_leaves'];

        if (isset($data['hourly_rate']) && $data['hourly_rate']) {
            unset($data['basic_salary']);
        } elseif (isset($data['basic_salary']) && $data['basic_salary']) {
            $data['daily_rate'] = $data['basic_salary'] / SalaryComputation::TYPICAL_WORK_DAYS_PER_MONTH;
            $data['hourly_rate'] = $data['daily_rate'] / SalaryComputation::EIGHT_HOURS;
        }
        return SalaryComputation::create($data);
    }
}
