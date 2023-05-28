<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class EmployeeRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'first_name' => self::REQUIRED_STRING,
            'last_name' => self::REQUIRED_STRING,
            'department' => self::REQUIRED_STRING,
            'job_title' => self::REQUIRED_STRING,
            'date_of_hire' => self::REQUIRED_DATE,
            'date_of_birth' => self::REQUIRED_DATE,
            'employment_status' => self::REQUIRED_STRING,
            'contact_number' => self::REQUIRED_STRING,
            'address_line' => self::REQUIRED_STRING,
            'sss_number' => [
                'required',
                'string',
                'max:255',
                Rule::unique('employees', 'sss_number')->ignore($this->employee),
            ],
            'pagibig_number' => [
                'required',
                'string',
                'max:255',
                Rule::unique('employees', 'pagibig_number')->ignore($this->employee),
            ],
            'philhealth_number' => [
                'required',
                'string',
                'max:255',
                Rule::unique('employees', 'philhealth_number')->ignore($this->employee),
            ],
            'tax_identification_number' => [
                'required',
                'string',
                'max:255',
                Rule::unique('employees', 'tax_identification_number')->ignore($this->employee),
            ],
            'bank_name' => self::NULLABLE_STRING,
            'bank_account_name' => self::NULLABLE_STRING,
            'bank_account_number' => self::NULLABLE_STRING,
            'mobile_number' => [
                Rule::requiredIf($this->has('role') && $this->role == 'business-admin'),
                'regex:/^(09|\+639)\d{9}$/',
                'unique:users,mobile_number'
            ],
            'email' => 'email|unique:users,email',
            'role' => 'string|max:255'
        ];
    }
}
