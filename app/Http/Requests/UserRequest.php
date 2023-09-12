<?php

namespace App\Http\Requests;

use App\Models\User;
use Illuminate\Validation\Rule;

class UserRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'first_name' => 'required',
            'last_name' => 'required',
            'email_address' => [
                'nullable',
                'email',
                'sometimes',
                Rule::unique('users', 'email_address')
                    ->whereNull('deleted_at')
                    ->ignore($this->user),
            ],
            'status' => 'nullable|string|in:' . implode(',', User::STATUSES),
            'company_id' => [
                'required',
                'array',
                Rule::exists('companies', 'id')->whereNull('deleted_at'),
            ],
        ];
    }
}
