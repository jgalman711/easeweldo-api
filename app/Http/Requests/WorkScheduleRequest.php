<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class WorkScheduleRequest extends BaseRequest
{
    public function rules(): array
    {
        $rules = [];
        $companyId = $this->company->id;
        $daysOfWeek = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
        foreach ($daysOfWeek as $day) {
            $rules["{$day}_clock_in_time"] = self::NULLABLE_TIME_FORMAT;
            $rules["{$day}_clock_out_time"] = self::NULLABLE_TIME_FORMAT;
        }

        return [
            ...$rules,
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('work_schedules', 'name')
                    ->where(function ($query) use ($companyId) {
                        $query->where('company_id', $companyId)
                            ->whereNull('deleted_at');
                    })
                    ->ignore($this->work_schedule->id ?? null),
            ],
        ];
    }
}
