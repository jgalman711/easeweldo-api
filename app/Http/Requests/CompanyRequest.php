<?php

namespace App\Http\Requests;

class CompanyRequest extends BaseRequest
{
    private const NULLABLE_STRING_MAX_30 = 'nullable|string|max:30';

    public function rules(): array
    {
        return [
            'name' => 'required|unique:companies,name,NULL,id,deleted_at,NULL',
            'legal_name' => self::NULLABLE_STRING,
            'logo' => 'required|image|mimes:jpeg,png,jpg,gif,svg|max:2048',
            'address_line' => self::NULLABLE_STRING,
            'barangay_town_city_province' => self::NULLABLE_STRING,
            'contact_name' => self::NULLABLE_STRING,
            'email_address' => 'nullable|email|max:255',
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
