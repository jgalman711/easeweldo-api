<?php

namespace App\Http\Requests;

use App\Models\Leave;

class LeaveRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'from_date' => self::REQUIRED_DATE,
            'to_date' => self::REQUIRED_DATE,
            'type' => 'required|in:' . implode("," , Leave::TYPES),
            'hours' => self::REQUIRED_NUMERIC,
            'description' => self::REQUIRED_STRING,
            'remarks' => self::NULLABLE_STRING
        ];
    }
}
