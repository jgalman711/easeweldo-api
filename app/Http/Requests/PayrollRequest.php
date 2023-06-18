<?php

namespace App\Http\Requests;

class PayrollRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'period_id' => 'required|exists:periods,id',
            'basic_salary' => self::REQUIRED_NUMERIC,
            'overtime_minutes' => self::NULLABLE_NUMERIC,
            'late_minutes' => self::NULLABLE_NUMERIC,
            'absent_minutes' => self::NULLABLE_NUMERIC,
            'undertime_minutes' => self::NULLABLE_NUMERIC,
            'regular_holiday_minutes_pay' => self::NULLABLE_NUMERIC,
            'regular_holiday_worked_minutes' => self::NULLABLE_NUMERIC,
            'special_holiday_minutes_pay' => self::NULLABLE_NUMERIC,
            'special_holiday_worked_minutes' => self::NULLABLE_NUMERIC,
            'leaves' => self::NULLABLE_ARRAY,
            'leaves.*.type' => self::REQUIRED_STRING,
            'leaves.*.pay' => self::REQUIRED_NUMERIC,
            'leaves.*.minutes' => self::REQUIRED_NUMERIC,
            'leaves.*.date' => self::REQUIRED_DATE,
            'allowances' => self::NULLABLE_ARRAY,
            'allowances.*.type' => self::REQUIRED_STRING,
            'allowances.*.pay' => self::REQUIRED_NUMERIC,
            'compensations' => self::NULLABLE_ARRAY,
            'compensations.*.type' => self::REQUIRED_STRING,
            'compensations.*.pay' => self::REQUIRED_NUMERIC,
            'other_compensations' => self::NULLABLE_ARRAY,
            'other_compensations.*.type' => self::REQUIRED_STRING,
            'other_compensations.*.pay' => self::REQUIRED_NUMERIC,
        ];
    }
}
