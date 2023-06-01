<?php

namespace App\Http\Requests;

class TimeRecordRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'clock_in' => 'nullable|date',
            'clock_out' => 'nullable|date|after:clock_in',
            'expected_clock_in' => 'required|date',
            'expected_clock_out' => 'required|date|after:expected_clock_in',
            'remarks' => 'nullable|string|max:255',
        ];
    }
}
