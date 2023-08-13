<?php

namespace App\Http\Requests;

use App\Rules\EarningTypeJsonRule;

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
            'taxable_earnings' => [new EarningTypeJsonRule()],
            'non_taxable_earnings' => [new EarningTypeJsonRule()],
            'remarks' => self::NULLABLE_STRING
        ];
    }
}
