<?php

namespace App\Http\Requests;

use App\Models\Leave;

class LeaveUpdateRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'date' => self::REQUIRED_DATE,
            'type' => 'required|in:' . implode("," , Leave::TYPES),
            'hours' => self::REQUIRED_NUMERIC,
            'description' => self::REQUIRED_STRING,
            'remarks' => self::NULLABLE_STRING
        ];
    }
}
