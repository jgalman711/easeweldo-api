<?php

namespace App\Interfaces;

use App\Models\Company;

interface PayrollStrategy
{
    /**
     * Generate payroll for an employee.
     *
     * @param Employee $employee | Company $company
     * @param Period $period | array $data
     *
     * @return Payroll | Collection.
     */
    public function generate($employeeOrCompany, $periodOrData);

    public function update(Company $company, int $payrollId, array $data);

    public function getEmployees(Company $company, array $data);
}
