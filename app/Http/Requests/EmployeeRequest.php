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
