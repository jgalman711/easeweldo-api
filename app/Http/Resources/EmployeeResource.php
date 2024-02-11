<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $latestSchedule = $this->employeeSchedules()->latest('start_date')->first();
        return [
            "id" => $this->id,
            "company" => $this->company->name,
            "company_slug" => $this->company->slug,
            "department" => ucwords($this->department),
            "job_title" => ucwords($this->job_title),
            "employment_status" => ucwords($this->employment_status),
            "date_of_hire" => $this->date_of_hire,
            "date_of_termination" => $this->date_of_termination,
            "date_of_birth" => $this->date_of_birth,
            "address_line" => ucwords($this->address_line),
            "barangay_town_city_province" => ucwords($this->barangay_town_city_province),
            "user_id" => $this->user_id,
            "company_id" => $this->company_id,
            "company_employee_id" => $this->company_employee_id,
            "status" => $this->status,
            "first_name" => $this->first_name,
            "last_name" => $this->last_name,
            "full_name" => $this->full_name,
            "username" => optional($this->user)->username,
            "email_address" =>  optional($this->user)->email_address,
            "profile_picture" => $this->profile_picture,
            "email" => optional($this->user)->email,
            "employment_type" => ucwords($this->employment_type),
            "employment_status" => ucwords($this->employment_status),
            "mobile_number" => $this->mobile_number,
            "sss_number" => $this->sss_number,
            "pagibig_number" => $this->pagibig_number,
            "philhealth_number" => $this->philhealth_number,
            "tax_identification_number" => $this->tax_identification_number,
            "bank_name" => $this->bank_name,
            "bank_account_name" => $this->bank_account_name,
            "bank_account_number" => $this->bank_account_number,
            "user" => $this->user ? [
                "email" => $this->user->email,
                "email_address" => $this->user->email_address,
                "username" => $this->user->username,
                "first_name" => $this->user->first_name,
                "last_name" => $this->user->last_name,
                "status" => $this->user->status,
                "temporary_password" => $this->user->temporary_password
            ] : null,
            "work_schedule" => [
                "start_date" => optional($latestSchedule)->start_date,
                "name" => optional(optional($latestSchedule)->workSchedule)->name
            ]
        ];
    }
}
