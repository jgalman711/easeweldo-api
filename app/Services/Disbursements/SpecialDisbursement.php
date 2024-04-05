<?php

namespace App\Services\Disbursements;

use App\Enumerators\PayrollEnumerator;
use App\Models\Employee;
use App\Models\Payroll;
use App\Models\Period;

class SpecialDisbursement extends BaseDisbursement
{
    public function create()
    {
        return Period::create($this->input);
    }

    public function generatePayroll(Period $disbursement, Employee $employee): Payroll
    {
        return Payroll::create([
            ...$this->input,
            'employee_id' => $employee->id,
            'period_id' => $disbursement->id,
            'status' => PayrollEnumerator::STATUS_TO_PAY,
            'basic_salary' => $this->input['pay_amount'],
            'pay_date' => $this->input['salary_date'],
        ]);
    }
}
