<?php

namespace App\Traits;

use App\Models\Company;

trait CompanyEmployee
{
    public function isCompanyEmployees(Company $company, array $employeeIds = []): bool
    {
        $companyEmployeeIds = $company->employees()->pluck('id')->toArray();
        foreach ($employeeIds as $employeeId) {
            if (! in_array($employeeId, $companyEmployeeIds)) {
                return false;
            }
        }

        return true;
    }
}
