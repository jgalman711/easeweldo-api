<?php

namespace App\Http\Requests\Payroll;

use App\Http\Requests\BaseRequest;

class SpecialPayrollRequest extends BaseRequest
{
    public function rules()
    {
        return [
            'employee_id' => 'required|array',
            'description' => self::REQUIRED_STRING,
            'pay_amount' => self::REQUIRED_NUMERIC,
            'pay_date' => 'required|date|after_or_equal:today',
            'remarks' => self::NULLABLE_STRING,
        ];
    }
}
