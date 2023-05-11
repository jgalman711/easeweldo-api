<?php

namespace App\Http\Requests;

class SalaryComputationRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'basic_salary' => self::REQUIRED_NUMERIC,
            'overtime_rate' => self::REQUIRED_NUMERIC,
            'night_diff_rate' => self::REQUIRED_NUMERIC,
            'regular_holiday_rate' => self::REQUIRED_NUMERIC,
            'special_holiday_rate' => self::REQUIRED_NUMERIC,
            'sick_leaves' => self::REQUIRED_NUMERIC,
            'vacation_leaves' => self::REQUIRED_NUMERIC,
        ];
    }
}
