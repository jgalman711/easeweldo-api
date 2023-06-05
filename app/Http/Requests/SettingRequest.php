<?php

namespace App\Http\Requests;

use App\Models\Period;

class SettingRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'period_cycle' => 'required|in:weekly,semi-monthly,monthly',
            'salary_day' => [
                'required',
                'array',
                function ($attribute, $value, $fail) {
                    $periodCycle = $this->input('period_cycle');
                    if ($periodCycle == Period::TYPE_MONTHLY && count($value) !== 1) {
                        $fail("The $attribute must contain 1 day for monthly period cycle.");
                    }
                    if ($periodCycle == Period::TYPE_SEMI_MONTHLY && count($value) !== 2) {
                        $fail("The $attribute must contain 2 days for semi-monthly period cycle.");
                    }
                    if ($periodCycle == Period::TYPE_SEMI_MONTHLY) {
                        sort($value);
                        $day1 = \Carbon\Carbon::createFromFormat('d', $value[0]);
                        $day2 = \Carbon\Carbon::createFromFormat('d', $value[1]);

                        if ($day1->diffInDays($day2) < 15) {
                            $fail("The salary days for semi-monthly period cycle must be at least 15 days apart.");
                        }
                    } elseif ($periodCycle == Period::TYPE_WEEKLY) {
                        $allowedDays = ['monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday', 'sunday'];
                        foreach ($value as $day) {
                            if (!in_array(strtolower($day), $allowedDays)) {
                                $fail("Invalid day '$day' found in $attribute.");
                                break;
                            }
                        }
                    }
                }
            ],
            'grace_period' => 'required|integer|min:0',
            'minimum_overtime' => 'required|integer|min:0'
        ];
    }
}
