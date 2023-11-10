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
            'pay_date' => 'required|date|after_or_equal:today',
            'remarks' => self::NULLABLE_STRING
        ];
    }
}
