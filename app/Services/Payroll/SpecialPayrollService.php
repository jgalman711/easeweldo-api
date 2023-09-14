<?php

namespace App\Services\Payroll;

use App\Enumerators\PayrollEnumerator;
use App\Models\Employee;
use App\Models\Payroll;

class SpecialPayrollService
{
    public function generate(Employee $employee, array $payrollData): Payroll
    {
        $payrollData['basic_salary'] = $payrollData['basic_salary'] ?? $payrollData['pay_amount'] ?? 0;
        $payrollData['type'] = PayrollEnumerator::TYPE_SPECIAL;
        return $employee->payrolls()->create($payrollData);
    }
}
