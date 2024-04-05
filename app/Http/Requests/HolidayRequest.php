<?php

namespace App\Http\Requests;

class HolidayRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string',
            'date' => 'required|date',
            'type' => 'required|in:special,regular',
        ];
    }
}
