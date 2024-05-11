<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class EmployeeResource extends BaseResource
{
    public function toArray(Request $request): array
    {
        $uploadsConfig = config('app.uploads');
        $employeeUploadPath = $uploadsConfig['url'].'/'.$uploadsConfig['employee_path'];
        $latestSchedule = $this->employeeSchedules->first();

        return [
            'id' => $this->id,
            'company' => $this->company->name,
            'company_slug' => $this->company->slug,
            'supervisor' => optional($this->supervisor)->full_name,
            'supervisor_user_id' => optional($this->supervisor)->id,
            'department' => ucwords($this->department),
            'job_title' => ucwords($this->job_title),
            'employment_status' => ucwords($this->employment_status),
            'date_of_hire' => [$this->date_of_hire, $this->formatForHumans($this->date_of_hire)],
            'date_of_termination' => $this->date_of_termination,
            'date_of_birth' => $this->date_of_birth,
            'address_line' => ucwords($this->address_line),
            'barangay_town_city_province' => ucwords($this->barangay_town_city_province),
            'user_id' => $this->user_id,
            'company_id' => $this->company_id,
            'company_employee_id' => $this->company_employee_id,
            'status' => $this->status,
            'first_name' => $this->first_name,
            'last_name' => $this->last_name,
            'full_name' => $this->full_name,
            'username' => optional($this->user)->username,
            'email_address' => optional($this->user)->email_address,
            'email' => optional($this->user)->email,
            'profile_picture' => $this->profile_picture ? $employeeUploadPath.'/'.$this->profile_picture : null,
            'employment_type' => ucwords($this->employment_type),
            'employment_status' => ucwords($this->employment_status),
            'mobile_number' => $this->mobile_number,
            'sss_number' => $this->sss_number,
            'pagibig_number' => $this->pagibig_number,
            'philhealth_number' => $this->philhealth_number,
            'tax_identification_number' => $this->tax_identification_number,
            'bank_name' => $this->bank_name,
            'bank_account_name' => $this->bank_account_name,
            'bank_account_number' => $this->bank_account_number,
            'role' => optional(optional(optional($this->user)->roles)->first())->name,
            'work_schedule_start_date' => optional($latestSchedule)->start_date,
            'work_schedule_name' => optional(optional($latestSchedule)->workSchedule)->name,
            'salary_package' => $this->getSalaryPackage(),
        ];
    }

    private function getSalaryPackage()
    {
        $salaryComputation = $this->salaryComputation;
        if ($salaryComputation && $salaryComputation->basic_salary) {
            return '₱'.number_format($salaryComputation->basic_salary, 2).' / month';
        } elseif ($salaryComputation && $salaryComputation->hourly_rate) {
            return '₱'.number_format($salaryComputation->hourly_rate, 2).' / hour';
        }

        return null;
    }
}
