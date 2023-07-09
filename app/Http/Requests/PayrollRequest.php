<?php

namespace App\Http\Requests;

class PayrollRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'employee_id' => 'exists:employees,id',
            'period_id' => 'required|exists:periods,id',
            'description' => self::NULLABLE_STRING,
            'overtime_minutes' => self::NULLABLE_NUMERIC,
            'late_minutes' => self::NULLABLE_NUMERIC,
            'absent_minutes' => self::NULLABLE_NUMERIC,
            'undertime_minutes' => self::NULLABLE_NUMERIC,
            'regular_holiday_hours_worked' => self::NULLABLE_NUMERIC,
            'special_holiday_hours_worked' => self::NULLABLE_NUMERIC,
            'leaves' => self::PRESENT_NULLABLE_ARRAY,
            'leaves.*.type' => self::NULLABLE_STRING,
            'leaves.*.hours' => self::NULLABLE_NUMERIC,
            'leaves.*.date' => 'nullable|date',
            'allowances' => self::PRESENT_NULLABLE_ARRAY,
            'allowances.*.type' => self::NULLABLE_STRING,
            'allowances.*.pay' => self::NULLABLE_NUMERIC,
            'commissions' => self::PRESENT_NULLABLE_ARRAY,
            'commissions.*.type' => self::NULLABLE_STRING,
            'commissions.*.pay' => self::NULLABLE_NUMERIC,
            'other_compensations' => self::PRESENT_NULLABLE_ARRAY,
            'other_compensations.*.type' => self::NULLABLE_STRING,
            'other_compensations.*.pay' => self::NULLABLE_NUMERIC,
            'remarks' => self::NULLABLE_STRING
        ];
    }
}
