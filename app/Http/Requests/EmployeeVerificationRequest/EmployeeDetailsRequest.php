<?php

namespace App\Http\Requests\EmployeeVerificationRequest;

use App\Http\Requests\BaseRequest;
use App\Models\Employee;

class EmployeeDetailsRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'job_title' => self::REQUIRED_STRING,
            'department' => self::REQUIRED_STRING,
            'date_of_hire' => self::REQUIRED_DATE,
            'employment_type' => 'required|string|in:'.implode(',', Employee::EMPLOYMENT_TYPE),
            'employment_status' => 'required|string|in:'.implode(',', Employee::EMPLOYMENT_STATUS),
            'working_days_per_week' => [
                'nullable',
                'integer',
                'min:1',
                function ($attribute, $value, $fail) {
                    if ($this->input('employment_type') == Employee::FULL_TIME && empty($value)) {
                        $fail('The working days per week field is required for full-time employees.');
                    }
                },
            ],
            'working_hours_per_day' => [
                'nullable',
                'integer',
                'min:1',
                function ($attribute, $value, $fail) {
                    if ($this->input('employment_type') == Employee::FULL_TIME && empty($value)) {
                        $fail('The working hours per day field is required for full-time employees.');
                    }
                },
            ],
            'role' => 'nullable|exists:roles,name'
        ];
    }

    public function messages()
    {
        return [
            'required' => 'This field is required.',
            'date' => 'This date is invalid.',
        ];
    }
}
