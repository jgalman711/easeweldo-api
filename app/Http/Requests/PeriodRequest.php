<?php

namespace App\Http\Requests;

use App\Models\Period;
use Illuminate\Validation\Rule;

class PeriodRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'type' => Rule::in(Period::TYPES),
            'salary_date' => self::REQUIRED_DATE
        ];
    }
}
