<?php

namespace App\Http\Requests;

class EmployeeScheduleRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'work_schedule_id' => 'required',
            'start_date' => 'required|date',
        ];
    }
}
