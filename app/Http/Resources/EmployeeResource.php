<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "department" => $this->department,
            "job_title" => $this->job_title,
            "date_of_hire" => $this->date_of_hire,
            "date_of_birth" => $this->date_of_birth,
            "employment_status" => $this->employment_status,
            "address_line" => $this->address_line,
            "barangay_town_city_province" => $this->barangay_town_city_province,
            "bank_account_number" => $this->bank_account_number,
            "user_id" => $this->user_id,
            "company_id" => $this->company_id,
            "company_employee_id" => $this->company_employee_id,
            "status" => $this->status,
            "first_name" => $this->first_name,
            "last_name" => $this->last_name,
            "full_name" => $this->full_name,
            "user" => $this->user ? [
                "email" => $this->user->email,
                "email_address" => $this->user->email_address,
                "username" => $this->user->username,
                "first_name" => $this->user->first_name,
                "last_name" => $this->user->last_name,
                "status" => $this->user->status,
                "temporary_password" => $this->user->temporary_password
            ] : null
        ];
    }
}
