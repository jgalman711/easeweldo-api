<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class WorkScheduleRequest extends BaseRequest
{
    public function rules(): array
    {
        $companyId = $this->company->id;
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('work_schedules', 'name')
                    ->where(function ($query) use ($companyId) {
                        $query->where('company_id', $companyId)
                              ->whereNull('deleted_at');
                    })
                    ->ignore($this->work_schedule->id ?? null)
            ],
            'monday_clock_in_time' => self::NULLABLE_TIME_FORMAT,
            'monday_clock_out_time' => 'nullable|date_format:H:i:s|after:monday_clock_in_time',
            'tuesday_clock_in_time' => self::NULLABLE_TIME_FORMAT,
            'tuesday_clock_out_time' => 'nullable|date_format:H:i:s|after:tuesday_clock_in_time',
            'wednesday_clock_in_time' => self::NULLABLE_TIME_FORMAT,
            'wednesday_clock_out_time' => 'nullable|date_format:H:i:s|after:wednesday_clock_in_time',
            'thursday_clock_in_time' => self::NULLABLE_TIME_FORMAT,
            'thursday_clock_out_time' => 'nullable|date_format:H:i:s|after:thursday_clock_in_time',
            'friday_clock_in_time' => self::NULLABLE_TIME_FORMAT,
            'friday_clock_out_time' => 'nullable|date_format:H:i:s|after:friday_clock_in_time',
            'saturday_clock_in_time' => self::NULLABLE_TIME_FORMAT,
            'saturday_clock_out_time' => 'nullable|date_format:H:i:s|after:saturday_clock_in_time',
            'sunday_clock_in_time' => self::NULLABLE_TIME_FORMAT,
            'sunday_clock_out_time' => 'nullable|date_format:H:i:s|after:sunday_clock_in_time'
        ];
    }
}
