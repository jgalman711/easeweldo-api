<?php

namespace App\Http\Requests;

use Illuminate\Validation\Rule;

class CompanyRequest extends BaseRequest
{
    private const NULLABLE_STRING_MAX_30 = 'nullable|string|max:30';

    public function rules(): array
    {
        return [
            'name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('companies', 'name')->where(function ($query) {
                    $query->whereNull('deleted_at');
                })->ignore($this->company),
            ],
            'legal_name' => self::NULLABLE_STRING,
            'logo' => 'nullable|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'address_line' => self::NULLABLE_STRING,
            'barangay_town_city_province' => self::NULLABLE_STRING,
            'contact_name' => self::NULLABLE_STRING,
            'email_address' => [
                'nullable',
                'email',
                'sometimes',
                Rule::unique('companies', 'email_address')
                    ->whereNull('deleted_at')
                    ->ignore($this->company),
            ],
            'mobile_number' => [
                'nullable',
                self::PH_MOBILE_NUMBER
            ],
            'landline_number' => self::NULLABLE_STRING,
            'bank_name' => self::NULLABLE_STRING,
            'bank_account_name' => self::NULLABLE_STRING,
            'bank_account_number' => self::NULLABLE_STRING_MAX_30,
            'tin' => self::NULLABLE_STRING_MAX_30,
            'sss_number' => self::NULLABLE_STRING_MAX_30,
            'philhealth_number' => self::NULLABLE_STRING_MAX_30,
            'pagibig_number' => self::NULLABLE_STRING_MAX_30,
        ];
    }
}
