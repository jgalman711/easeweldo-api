<?php

namespace App\Http\Requests;

class TimeCorrectionRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'date' => self::REQUIRED_DATE,
            'clock_in' => 'nullable|date',
            'clock_out' => 'nullable|date|after:clock_in',
            'title' => self::REQUIRED_STRING,
            'description' => self::NULLABLE_STRING,
            'remarks' => self::NULLABLE_STRING,
        ];
    }
}
