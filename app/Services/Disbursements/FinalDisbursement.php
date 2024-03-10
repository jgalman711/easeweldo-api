<?php

namespace App\Services\Disbursements;

use App\Models\Employee;
use App\Models\Payroll;
use App\Models\Period;
use App\Services\Payroll\GeneratePayrollService;
use Carbon\Carbon;

class FinalDisbursement extends BaseDisbursement
{
    public function create(): Period
    {
        $now = Carbon::now();
        $startOfYear = $now->copy()->startOfYear()->format('Y-m-d');
        $endOfYear = $now->copy()->endOfYear()->format('Y-m-d');
        $this->input = [
            ...$this->input,
            'start_date' => $startOfYear,
            'end_date' => $endOfYear
        ];
        return Period::create($this->input);
    }

    public function generatePayroll(Period $disbursement, Employee $employee): Payroll
    {
        $payrollService = app()->make(GeneratePayrollService::class);
        return $payrollService->generate($employee->company, $disbursement, $employee);
    }
}
