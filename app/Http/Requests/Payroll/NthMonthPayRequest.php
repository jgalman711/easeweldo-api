<?php

namespace App\Http\Requests\Payroll;

use App\Http\Requests\BaseRequest;

class NthMonthPayRequest extends BaseRequest
{
    public function rules()
    {
        return [
            'employee_id' => 'required|array',
            'description' => self::REQUIRED_STRING,
            'remarks' => self::NULLABLE_STRING,
            'pay_date' => 'required|date|after_or_equal:today'
        ];
    }
}
