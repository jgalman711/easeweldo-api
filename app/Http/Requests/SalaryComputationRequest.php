<?php

namespace App\Http\Requests;

use App\Rules\EarningTypeJsonRule;

class SalaryComputationRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'basic_salary' => ! $this->filled('hourly_rate') ? self::REQUIRED_NUMERIC : '',
            'hourly_rate' => ! $this->filled('basic_salary') ? self::REQUIRED_NUMERIC : '',
            'daily_rate' => self::NUMERIC,
            'taxable_earnings' => [new EarningTypeJsonRule()],
            'non_taxable_earnings' => [new EarningTypeJsonRule()],
            'working_hours_per_day' => self::NUMERIC,
            'working_days_per_week' => self::NUMERIC,
            'overtime_rate' => self::REQUIRED_NUMERIC,
            'night_diff_rate' => self::REQUIRED_NUMERIC,
            'regular_holiday_rate' => self::REQUIRED_NUMERIC,
            'special_holiday_rate' => self::REQUIRED_NUMERIC,
            'total_sick_leave_hours' => self::REQUIRED_NUMERIC,
            'total_vacation_leave_hours' => self::REQUIRED_NUMERIC,
        ];
    }
}
