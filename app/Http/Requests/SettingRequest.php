<?php

namespace App\Http\Requests;

use App\Models\Period;
use Closure;

class SettingRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'period_cycle' => 'required|in:weekly,semi-monthly,monthly',
            'salary_day' => [
                'required',
                function ($attribute, $value, $fail) {
                    $periodCycle = $this->input('period_cycle');

                    if ($periodCycle === Period::SUBTYPE_SEMI_MONTHLY) {
                        $this->validateSemiMonthly($value, $fail);
                    } elseif ($periodCycle === Period::SUBTYPE_MONTHLY) {
                        $this->validateMonthly($value, $fail);
                    } elseif ($periodCycle === Period::SUBTYPE_WEEKLY) {
                        $this->validateWeekly($value, $fail);
                    }
                }
            ],
            'disbursement_method' => self::NULLABLE_STRING,
            'grace_period' => 'nullable|integer|min:0',
            'minimum_overtime' => 'nullable|integer|min:0',
            'overtime_rate' => 'nullable|min:0'
        ];
    }

    private function validateSemiMonthly($value, $fail)
    {
        if (count($value) !== 2) {
            $fail("The salary day must contain 2 days for semi-monthly period cycle.");
        }
        sort($value);
        if ((int)$value[1] - (int)$value[0] < 15) {
            $fail("The salary days for semi-monthly period cycle must be at least 15 days apart.");
        }
    }

    private function validateMonthly($value, $fail)
    {
        if (!is_numeric($value)) {
            $fail("The salary day is invalid.");
        }
    }

    private function validateWeekly(string $day, Closure $fail): void
    {
        if (!in_array(strtolower($day), Period::ALLOWED_DAYS)) {
            $fail("Invalid day '$day' found in salary day.");
        }
    }
}
