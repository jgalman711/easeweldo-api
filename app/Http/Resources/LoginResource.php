<?php

namespace App\Http\Resources;

use App\Models\Company;
use Illuminate\Http\Request;

class LoginResource extends BaseResource
{
    public function toArray(Request $request): array
    {
        $companies = $this->hasRole('super-admin') ? Company::with('setting')->get() : $this->companies;
        return [
            'token' => $this->token,
            'username' => $this->username,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'status' => $this->status,
            'email' => $this->email_address,
            'email_address' => $this->email_address,
            'email_verified_at' => $this->email_verified_at,
            'companies' => CompanyResource::collection($companies),
            'employee' => new EmployeeResource($this->employee),
            'role' => $this->roles->first()->name,
        ];
    }
}
