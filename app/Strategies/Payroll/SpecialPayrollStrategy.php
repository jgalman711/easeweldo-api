<?php

namespace App\Strategies\Payroll;

use App\Enumerators\PayrollEnumerator;
use App\Interfaces\PayrollStrategy;
use App\Models\Payroll;

class SpecialPayrollStrategy implements PayrollStrategy
{
    public function generate($employee, $data): Payroll
    {
        $data['basic_salary'] = $data['basic_salary'] ?? $data['pay_amount'] ?? 0;
        $data['type'] = PayrollEnumerator::TYPE_SPECIAL;
        return $employee->payrolls()->create($data);
    }
}
