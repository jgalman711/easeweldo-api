<?php

namespace App\Http\Requests\Payroll;

use App\Enumerators\PayrollEnumerator;
use App\Http\Requests\BaseRequest;

class SpecialPayrollRequest extends BaseRequest
{
    public function rules()
    {
        return [
            'type' => 'required|in:' . implode(',', [
                PayrollEnumerator::TYPE_SPECIAL,
                PayrollEnumerator::TYPE_FINAL,
                PayrollEnumerator::TYPE_NTH_MONTH_PAY,
            ]),
            'employee_id' => self::NULLABLE_ARRAY,
            'description' => self::REQUIRED_STRING,
            'pay_amount' => self::REQUIRED_NUMERIC,
            'pay_date' => 'required|date|after_or_equal:today',
            'remarks' => self::NULLABLE_STRING,
        ];
    }
}
