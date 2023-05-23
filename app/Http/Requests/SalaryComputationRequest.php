<?php

namespace App\Http\Requests;

class SalaryComputationRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'basic_salary' => 'filled|required_without_all:hourly_rate',
            'hourly_rate' => 'filled|required_without_all:basic_salary',
            'daily_rate' => self::NUMERIC,
            'overtime_rate' => self::REQUIRED_NUMERIC,
            'night_diff_rate' => self::REQUIRED_NUMERIC,
            'regular_holiday_rate' => self::REQUIRED_NUMERIC,
            'special_holiday_rate' => self::REQUIRED_NUMERIC,
            'sick_leaves' => self::REQUIRED_NUMERIC,
            'vacation_leaves' => self::REQUIRED_NUMERIC,
            'unit' => 'required|in:hour,day'
        ];
    }
}
