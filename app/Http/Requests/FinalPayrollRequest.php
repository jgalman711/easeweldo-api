<?php

namespace App\Http\Requests;

class FinalPayrollRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'employee_id' => 'required|array',
            'description' => self::REQUIRED_STRING,
            'pay_date' => 'required|date|after_or_equal:today',
            'remarks' => self::NULLABLE_STRING
        ];
    }
}
