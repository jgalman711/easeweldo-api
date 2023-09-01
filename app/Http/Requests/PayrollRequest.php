<?php

namespace App\Http\Requests;

use App\Rules\EarningTypeJsonRule;

class PayrollRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'description' => self::NULLABLE_STRING,
            'overtime_hours' => self::NULLABLE_NUMERIC,
            'late_hours' => self::NULLABLE_NUMERIC,
            'absent_hours' => self::NULLABLE_NUMERIC,
            'undertime_hours' => self::NULLABLE_NUMERIC,
            'regular_holiday_hours_worked' => self::NULLABLE_NUMERIC,
            'special_holiday_hours_worked' => self::NULLABLE_NUMERIC,
            'leaves' => self::NULLABLE_ARRAY,
            'leaves.*.type' => self::NULLABLE_STRING,
            'leaves.*.hours' => self::NULLABLE_NUMERIC,
            'leaves.*.date' => 'nullable|date',
            'taxable_earnings' => [new EarningTypeJsonRule()],
            'non_taxable_earnings' => [new EarningTypeJsonRule()],
            'remarks' => self::NULLABLE_STRING
        ];
    }
}
