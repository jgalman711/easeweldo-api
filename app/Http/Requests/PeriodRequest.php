<?php

namespace App\Http\Requests;

use App\Models\Period;
use Illuminate\Validation\Rule;

class PeriodRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'name' => 'string|max:255',
            'start_date' => 'required|date',
            'end_date' => 'required|date|after:start_date',
            'status' => Rule::in(Period::getStatusOptions())
        ];
    }
}
