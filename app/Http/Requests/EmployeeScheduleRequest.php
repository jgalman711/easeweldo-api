<?php

namespace App\Http\Requests;

class EmployeeScheduleRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'work_schedule_id' => 'required',
            'start_date' => self::REQUIRED_DATE_AFTER_TODAY,
            'remarks' => self::NULLABLE_STRING,
        ];
    }
}
