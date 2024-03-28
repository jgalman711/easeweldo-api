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
    protected const TWELVE_MONTHS = 12;

    public function create(): Period
    {
        $now = Carbon::now();
        $startOfYear = $now->copy()->startOfYear()->format('Y-m-d');
        $endOfYear = $now->copy()->endOfYear()->format('Y-m-d');
        $input = [
            ...$this->input,
            'start_date' => $startOfYear,
            'end_date' => $endOfYear,
        ];

        return Period::create($input);
    }

    public function generatePayroll(Period $disbursement, Employee $employee): Payroll
    {
        $payrollRepository = app()->make(PayrollRepository::class);
        $payrolls = $payrollRepository->getEmployeePayrollsByDateRange($employee->id, [
            $disbursement->start_date,
            $disbursement->end_date,
        ]);

        $totalBasicSalary = 0;
        $totalTaxableEarnings = 0;
        $totalNonTaxableEarnings = 0;
        foreach ($payrolls as $payroll) {
            $totalBasicSalary += $payroll->net_income;
            $totalTaxableEarnings += $this->getEarningsTotal($payroll->taxable_earnings ?? []);
            $totalNonTaxableEarnings += $this->getEarningsTotal($payroll->non_taxable_earnings ?? []);
        }

        return Payroll::create([
            ...$this->input,
            'employee_id' => $employee->id,
            'period_id' => $disbursement->id,
            'status' => PayrollEnumerator::STATUS_TO_PAY,
            'basic_salary' => $totalBasicSalary / self::TWELVE_MONTHS,
            'taxable_earnings' => [
                [
                    'name' => 'Prorated Taxable Earnings',
                    'amount' => round($totalTaxableEarnings / self::TWELVE_MONTHS, 2),
                ],
            ],
            'non_taxable_earnings' => [
                [
                    'name' => 'Prorated Non-taxable Earnings',
                    'amount' => round($totalNonTaxableEarnings / self::TWELVE_MONTHS, 2),
                ],
            ],
            'pay_date' => $this->input['salary_date'],
        ]);
    }

    private function getEarningsTotal(array $earnings): float
    {
        $totalEarnings = 0;
        foreach ($earnings as $earning) {
            $totalEarnings += $earning['amount'] ?? 0;
        }

        return $totalEarnings;
    }
}
