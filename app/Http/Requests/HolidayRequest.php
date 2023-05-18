<?php

namespace App\Http\Requests;

use Illuminate\Foundation\Http\FormRequest;

class HolidayRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'name' => 'required|string',
            'date' => 'required|date',
            'type' => 'required|in:special,regular'
        ];
    }
}
