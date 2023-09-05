<?php

namespace App\Http\Requests;

class LeaveRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'type' => 'required',
            'start_date' => self::REQUIRED_DATE,
            'end_date' => self::REQUIRED_DATE . '|after:start_date'
        ];
    }
}
