<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class LoginResource extends BaseResource
{
    public function toArray(Request $request): array
    {
        return [
            "token" => $this->token,
            "username" => $this->username,
            "first_name" => $this->first_name,
            "last_name" => $this->last_name,
            "status" => $this->status,
            "email" => $this->email_address,
            "email_address" => $this->email_address,
            "email_verified_at" => $this->email_verified_at,
            "companies" => CompanyResource::collection($this->companies),
            "employee" => new EmployeeResource($this->employee),
            "roles" => $this->roles,
        ];
    }
}
