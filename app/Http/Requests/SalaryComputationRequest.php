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
            'allowances' => self::PRESENT_NULLABLE_ARRAY,
            'allowances.*.type' => self::NULLABLE_STRING,
            'allowances.*.pay' => self::NULLABLE_NUMERIC,
            'commissions' => self::PRESENT_NULLABLE_ARRAY,
            'commissions.*.type' => self::NULLABLE_STRING,
            'commissions.*.pay' => self::NULLABLE_NUMERIC,
            'other_compensations' => self::PRESENT_NULLABLE_ARRAY,
            'other_compensations.*.type' => self::NULLABLE_STRING,
            'other_compensations.*.pay' => self::NULLABLE_NUMERIC,
            'working_hours_per_day' => self::NUMERIC,
            'working_days_per_week' =>  self::NUMERIC,
            'overtime_rate' => self::REQUIRED_NUMERIC,
            'night_diff_rate' => self::REQUIRED_NUMERIC,
            'regular_holiday_rate' => self::REQUIRED_NUMERIC,
            'special_holiday_rate' => self::REQUIRED_NUMERIC,
            'total_sick_leave_hours' => self::REQUIRED_NUMERIC,
            'total_vacation_leave_hours' => self::REQUIRED_NUMERIC,
            'available_sick_leave_hours' => self::REQUIRED_NUMERIC,
            'available_vacation_leave_hours' => self::REQUIRED_NUMERIC,
        ];
    }
}
