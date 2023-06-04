<?php

namespace App\Http\Requests;

class SettingRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'period_cycle' => 'required|in:weekly,monthly,semi-monthly',
            'salary_day' => 'required|array',
            'salary_day.*' => 'integer',
            'grace_period' => 'required|integer',
            'minimum_overtime' => 'required|numeric',
        ];
    }
}
