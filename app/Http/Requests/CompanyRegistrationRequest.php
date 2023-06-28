<?php

namespace App\Http\Requests;

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
            'email_address' => [
                'required',
                'string',
                'max:255',
                Rule::unique('users', 'email_address')->where(function ($query) {
                    $query->whereNull('deleted_at');
                }),
            ],
            'password' => 'required|confirmed|min:6'
        ];
    }
}
