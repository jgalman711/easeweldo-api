<?php

namespace App\Http\Requests;

use App\Models\Employee;
use Illuminate\Validation\Rule;

class EmployeeRequest extends BaseRequest
{
    public function rules(): array
    {
        $employee = Employee::find($this->employee);
        return [
            'user_id' => [
                'nullable',
                'integer',
                Rule::exists('users', 'id')->whereNull('deleted_at'),
            ],
            'first_name' => self::REQUIRED_STRING,
            'last_name' => self::REQUIRED_STRING,
            'employee_number' => self::NULLABLE_STRING,
            'department' => self::REQUIRED_STRING,
            'job_title' => self::REQUIRED_STRING,
            'date_of_hire' => self::REQUIRED_DATE,
            'date_of_birth' => self::REQUIRED_DATE,
            'status' => 'nullable|string|in:' . implode(',', Employee::STATUS),
            'employment_status' => 'nullable|string|in:' . implode(',', Employee::EMPLOYMENT_STATUS),
            'employment_type' => 'nullable|string|in:' . implode(',', Employee::EMPLOYMENT_TYPE),
            'working_days_per_week' => [
                'nullable',
                'integer',
                'unsigned',
                function ($attribute, $value, $fail) {
                    if ($this->input('employment_type') == Employee::FULL_TIME && empty($value)) {
                        $fail('The working days per week field is required for full-time employees.');
                    }
                },
            ],
            'working_hours_per_day' => [
                'nullable',
                'integer',
                'unsigned',
                function ($attribute, $value, $fail) {
                    if ($this->input('employment_type') == Employee::FULL_TIME && empty($value)) {
                        $fail('The working hours per day field is required for full-time employees.');
                    }
                },
            ],
            'email_address' => [
                'nullable',
                'email',
                'sometimes',
                Rule::unique('users', 'email_address')
                    ->whereNull('deleted_at')
                    ->ignore(optional($employee)->user),
            ],
            'mobile_number' => [
                'nullable',
                'sometimes',
                Rule::unique('employees', 'mobile_number')
                    ->whereNull('deleted_at')
                    ->ignore($this->employee),
                self::PH_MOBILE_NUMBER
            ],
            'address_line' => self::REQUIRED_STRING,
            'barangay_town_city_province' => self::REQUIRED_STRING,
            'date_of_hire' => self::NULLABLE_DATE,
            'date_of_termination' => self::NULLABLE_DATE_AFTER_TODAY,
            'sss_number' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('employees', 'sss_number')
                    ->whereNull('deleted_at')
                    ->ignore($this->employee),
            ],
            'pagibig_number' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('employees', 'pagibig_number')
                    ->whereNull('deleted_at')
                    ->ignore($this->employee),
            ],
            'philhealth_number' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('employees', 'philhealth_number')
                    ->whereNull('deleted_at')
                    ->ignore($this->employee),
            ],
            'tax_identification_number' => [
                'nullable',
                'string',
                'max:255',
                Rule::unique('employees', 'tax_identification_number')
                    ->whereNull('deleted_at')
                    ->ignore($this->employee),
            ],
            'bank_name' => self::NULLABLE_STRING,
            'bank_account_name' => self::NULLABLE_STRING,
            'bank_account_number' => self::NULLABLE_STRING,
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048'
        ];
    }
}
