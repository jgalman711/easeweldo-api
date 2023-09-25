<?php

namespace App\Http\Requests;

class LeaveRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'type' => 'required',
            'employee_id' => 'required|exists:employees,id',
            'from_date' => self::REQUIRED_DATE,
            'to_date' => self::REQUIRED_DATE,
            'description' => self::REQUIRED_STRING,
            'remarks' => self::NULLABLE_STRING
        ];
    }
}
