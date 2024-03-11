<?php

namespace App\Services\Disbursements;

use App\Enumerators\PayrollEnumerator;
use App\Models\Employee;
use App\Models\Payroll;
use App\Models\Period;
use App\Repositories\PayrollRepository;
use Carbon\Carbon;

class AnnualExtraDisbursement extends BaseDisbursement
{
    public function create(): Period
    {
        $now = Carbon::now();
        $startOfYear = $now->copy()->startOfYear()->format('Y-m-d');
        $endOfYear = $now->copy()->endOfYear()->format('Y-m-d');
        $input = [
            ...$this->input,
            'start_date' => $startOfYear,
            'end_date' => $endOfYear
        ];
        return Period::create($input);
    }

    public function generatePayroll(Period $disbursement, Employee $employee): Payroll
    {
        $payrollRepository = app()->make(PayrollRepository::class);
        $payrolls = $payrollRepository->getEmployeePayrollsByDateRange($employee->id, [
            'start_date' => $disbursement->start_date,
            'end_date' => $disbursement->end_date
        ]);

        $totalAmount = $payrolls->sum('net_income');
        return Payroll::create([
            ...$this->input,
            'employee_id' => $employee->id,
            'period_id' => $disbursement->id,
            'status' => PayrollEnumerator::STATUS_TO_PAY,
            'basic_salary' => $totalAmount / 12,
            'pay_date' => $this->input['salary_date']
        ]);
    }
}
