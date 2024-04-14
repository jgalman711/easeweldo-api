<?php

namespace App\Http\Requests;

class ApproverRequest extends BaseRequest
{
    public function rules(): array
    {
        return [
            'user_id' => 'required|exists:users,id',
            'role_name' => 'nullable|exists:roles,name'
        ];
    }
}
