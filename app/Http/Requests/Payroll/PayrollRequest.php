<?php

namespace App\Http\Requests\Payroll;

use App\Enumerators\PayrollEnumerator;
use App\Http\Requests\BaseRequest;
use App\Rules\EarningTypeJsonRule;
use App\Rules\LeavesJsonRule;

class PayrollRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'status' => 'nullable|string|in:' . implode(',', PayrollEnumerator::STATUSES),
            'description' => self::NULLABLE_STRING,
            'pay_date' => self::NULLABLE_DATE_AFTER_TODAY,
            'overtime_hours' => self::NULLABLE_NUMERIC,
            'late_hours' => self::NULLABLE_NUMERIC,
            'absent_hours' => self::NULLABLE_NUMERIC,
            'undertime_hours' => self::NULLABLE_NUMERIC,
            'regular_holiday_hours' => self::NULLABLE_NUMERIC,
            'regular_holiday_hours_worked' => self::NULLABLE_NUMERIC,
            'special_holiday_hours' => self::NULLABLE_NUMERIC,
            'special_holiday_hours_worked' => self::NULLABLE_NUMERIC,
            'leaves' => [new LeavesJsonRule()],
            'taxable_earnings' => [new EarningTypeJsonRule()],
            'non_taxable_earnings' => [new EarningTypeJsonRule()],
            'remarks' => self::NULLABLE_STRING
        ];
    }
}
