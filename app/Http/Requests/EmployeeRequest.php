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
            'contact_number' => self::REQUIRED_STRING,
            'address' => self::REQUIRED_STRING,
            'sss_number' => 'required|string|max:255|unique:employees,sss_number',
            'pagibig_number' => 'required|string|max:255|unique:employees,pagibig_number',
            'philhealth_number' => 'required|string|max:255|unique:employees,philhealth_number',
            'tax_identification_number' => 'required|string|max:255|unique:employees,tax_identification_number',
            'bank_account_number' => self::REQUIRED_STRING,
            'sick_leaves' => self::REQUIRED_NUMERIC,
            'vacation_leaves' => self::REQUIRED_NUMERIC,
            'mobile_number' => [
                Rule::requiredIf($this->has('role') && $this->role == 'business-admin'),
                'numeric',
                'unique:users,mobile_number'
            ]
        ];
    }
}
