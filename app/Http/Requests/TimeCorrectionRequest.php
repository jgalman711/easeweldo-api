<?php

namespace App\Http\Requests;

class TimeCorrectionRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'date' => self::REQUIRED_DATE,
            'clock_in' => 'nullable|date_format:H:i',
            'clock_out' => 'nullable|date_format:H:i|after:clock_in',
            'remarks' => self::REQUIRED_STRING,
            'status' => self::NULLABLE_STRING
        ];
    }
}
