<?php

namespace App\Http\Requests\V2;

use App\Enumerators\LeaveEnumerator;
use App\Http\Requests\BaseRequest;

class EmployeeLeaveRequest extends BaseRequest
{
    public function rules(): array
    {
        $rules = [
            'title' => self::REQUIRED_STRING,
            'hours' => self::REQUIRED_NUMERIC,
            'description' => self::NULLABLE_STRING,
            'type' => 'required|in:'.implode(',', LeaveEnumerator::TYPES)
        ];

        if ($this->method() === "POST") {
            $rules['from_date'] = self::REQUIRED_DATE;
            $rules['to_date'] = self::REQUIRED_DATE.'|after_or_equal:from_date';

        } elseif ($this->method() === "PUT" || $this->method() === "PATCH") {
            $rules['date'] = self::REQUIRED_DATE;
        }
        return $rules;
    }
}
