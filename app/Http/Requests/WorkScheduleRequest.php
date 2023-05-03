<?php

namespace App\Http\Requests;

class WorkScheduleRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'name' => self::REQUIRED_STRING,
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
