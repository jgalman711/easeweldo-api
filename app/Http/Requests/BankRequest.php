<?php

namespace App\Http\Requests;

class BankRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'name' => self::REQUIRED_STRING,
            'branch' => self::REQUIRED_STRING,
            'account_name' => self::REQUIRED_STRING,
            'account_number' => self::REQUIRED_STRING,
            'email' => 'nullable|email',
            'contact_name' => self::NULLABLE_STRING,
            'contact_number' => self::NULLABLE_STRING,
        ];
    }
}
