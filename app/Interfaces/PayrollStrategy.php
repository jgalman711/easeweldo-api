<?php

namespace App\Interfaces;

use App\Models\Company;
use Illuminate\Support\Collection;

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

    public function getEmployees(Company $company, array $data);
}
