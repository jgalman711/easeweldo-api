<?php

namespace App\Services;

use App\Models\Company;
use App\Models\Employee;
use Illuminate\Support\Facades\DB;

class EmployeeService
{
    public function create(Company $company, array $data): Employee
    {
        try {
            DB::beginTransaction();
            $data['company_id'] = $company->id;
            $data['company_employee_id'] = $this->generateCompanyEmployeeId($company);
            $data['status'] = $company->isInSettlementPeriod() ? Employee::PENDING : Employee::ACTIVE;
            $employee = Employee::create($data);
            DB::commit();
        } catch (\Exception $e) {
            DB::rollback();
            throw $e;
        }
        return $employee;
    }

    private function generateCompanyEmployeeId(Company $company): int
    {
        $latestEmployee = $company->employees()->orderByDesc('id')->first();
        return $latestEmployee ? $latestEmployee->company_employee_id + 1 : 1;
    }
}
