<?php

namespace App\Http\Requests;

class OvertimeRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'employee_id' => 'required|exists:employees,id',
            'from_date' => self::REQUIRED_DATE,
            'to_date' => self::REQUIRED_DATE,
            'reason' => self::REQUIRED_STRING,
            'remarks' => self::NULLABLE_STRING,
            'status' => self::NULLABLE_STRING,
            'approved_date' => self::NULLABLE_DATE,
            'submitted_date' => self::NULLABLE_DATE,
        ];
    }
}
