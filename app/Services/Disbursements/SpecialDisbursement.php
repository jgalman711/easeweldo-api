<?php

namespace App\Services\Disbursements;

use App\Models\Employee;
use App\Models\Payroll;
use App\Models\Period;

class SpecialDisbursement extends BaseDisbursement
{
    public function create(): Period
    {
        return Period::create($this->input);
    }

    public function generatePayroll(Period $period, Employee $employee): Payroll
    {
        // $payroll = Payroll::create([
            
        // ]);
    }
}
