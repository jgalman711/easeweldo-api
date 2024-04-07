<?php

namespace App\Http\Requests;

class UpdateRoleRequest extends BaseRequest
{
    public function rules(): array
    {
        $allowedRoles = [
            'approver',
            'business-admin'
        ];
        return [
            'employee_id' => 'required',
            'role_name' => 'required|in:' . implode(",", $allowedRoles)
        ];
    }
}
