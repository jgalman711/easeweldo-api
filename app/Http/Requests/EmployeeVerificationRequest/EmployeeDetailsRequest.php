<?php

namespace App\Http\Requests\EmployeeVerificationRequest;

use App\Http\Requests\BaseRequest;
use Illuminate\Validation\Rule;

class EmployeeDetailsRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'first_name' => self::REQUIRED_STRING,
            'last_name' => self::REQUIRED_STRING,
            'email_address' => [
                'nullable',
                'email',
                'sometimes',
                Rule::unique('users', 'email_address')
                    ->whereNull('deleted_at')
                    ->ignore(optional($this->employee)->user),
            ],
            'mobile_number' => [
                'nullable',
                'sometimes',
                Rule::unique('employees', 'mobile_number')
                    ->whereNull('deleted_at')
                    ->ignore($this->employee),
                self::PH_MOBILE_NUMBER
            ],
            'date_of_birth' => self::REQUIRED_DATE,
            'address_line' => self::REQUIRED_STRING,
            'barangay_town_city_province' => self::REQUIRED_STRING,
            'profile_picture' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
        ];
    }
}
