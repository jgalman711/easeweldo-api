<?php

namespace App\Http\Requests;

use App\Models\Leave;

class LeaveRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'type' => 'required|in:' . implode("," , Leave::TYPES),
            'from_date' => self::REQUIRED_DATE,
            'to_date' => self::REQUIRED_DATE,
            'description' => self::REQUIRED_STRING,
            'remarks' => self::NULLABLE_STRING
        ];
    }
}
