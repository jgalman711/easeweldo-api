<?php

namespace App\Http\Requests;

use App\Enumerators\LeaveEnumerator;

class LeaveRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'employee_id' => self::REQUIRED_NUMERIC,
            'from_date' => self::REQUIRED_DATE,
            'to_date' => self::REQUIRED_DATE.'|after_or_equal:from_date',
            'type' => 'required|in:'.implode(',', LeaveEnumerator::TYPES),
            'hours' => self::REQUIRED_NUMERIC,
            'description' => self::REQUIRED_STRING
        ];
    }
}
