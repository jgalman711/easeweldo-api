<?php

namespace App\Http\Requests;

use App\Models\Period;
use Carbon\Carbon;
use Illuminate\Validation\Rule;

class PeriodRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'type' => Rule::in(Period::TYPES),
            'salary_date' => [
                'required',
                'date',
                'after:' . Carbon::now()->format('Y-m-d')],
        ];
    }
}
