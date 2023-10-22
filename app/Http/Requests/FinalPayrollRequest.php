<?php

namespace App\Http\Requests;

class FinalPayrollRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'employee_id' => 'required|array',
            'description' => self::REQUIRED_STRING,
            'remarks' => self::NULLABLE_NUMERIC
        ];
    }
}
