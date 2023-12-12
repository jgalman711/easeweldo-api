<?php

namespace App\Http\Requests\Payroll;

use App\Enumerators\PayrollEnumerator;

class UpdatePayrollRequest extends SpecialPayrollRequest
{
    public function rules()
    {
        return [
            'status' => 'nullable|string|in:' . implode(',', PayrollEnumerator::STATUSES),
            'description' => self::REQUIRED_STRING,
            'pay_amount' => self::REQUIRED_NUMERIC,
            'pay_date' => self::REQUIRED_DATE,
            'remarks' => self::NULLABLE_STRING
        ];
    }
}
