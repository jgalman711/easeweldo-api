<?php

namespace App\Http\Requests;

class TimeCorrectionRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'date' => self::REQUIRED_DATE,
            'clock_in' => 'nullable|time',
            'clock_out' => 'nullable|time|after:clock_in',
            'remarks' => self::REQUIRED_STRING,
            'status' => self::NULLABLE_STRING
        ];
    }
}
