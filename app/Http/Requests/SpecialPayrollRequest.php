<?php

namespace App\Http\Requests;

class SpecialPayrollRequest extends BaseRequest
{
    public function rules()
    {
        return [
            'employee_id' => 'required',
            'description' => self::REQUIRED_STRING,
            'pay_amount' => self::REQUIRED_NUMERIC,
            'pay_date' => 'required|date|after_or_equal:today',
            'remarks' => self::NULLABLE_NUMERIC,
        ];
    }
}
