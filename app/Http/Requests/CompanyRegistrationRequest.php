<?php

namespace App\Http\Requests;

use App\Rules\Recaptcha;
use Illuminate\Validation\Rule;

class CompanyRegistrationRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'company_name' => [
                'required',
                'string',
                'max:255',
                Rule::unique('companies', 'name')->where(function ($query) {
                    $query->whereNull('deleted_at');
                }),
            ],
            'first_name' => self::REQUIRED_STRING,
            'last_name' => self::REQUIRED_STRING,
            'email_address' => [
                'required',
                'email',
                'max:255',
                Rule::unique('users', 'email_address')->where(function ($query) {
                    $query->whereNull('deleted_at');
                }),
            ],
            'password' => 'required|confirmed|min:6',
            'g-recaptcha-response' => ['required', new Recaptcha],
        ];
    }
}
