<?php

namespace App\Strategies\Payroll;

use App\Enumerators\PayrollEnumerator;
use App\Interfaces\PayrollStrategy;
use App\Models\Company;
use App\Models\Employee;
use App\Models\Payroll;
use Exception;
use Illuminate\Support\Facades\DB;

class NthMonthPayrollStrategy implements PayrollStrategy
{
    public function generate($company, $payrollData): array
    {
        $nthMonthPayrolls = [];
        $payrollData['type'] = PayrollEnumerator::TYPE_NTH_MONTH_PAY;
        $employees = $this->getEmployees($company, $payrollData);
        $periods = $company->periodsForYear(date('Y'));
        foreach ($employees as $employee) {
            try {
                DB::beginTransaction();
                $payrolls = $employee->payrolls()
                    ->whereIn('period_id', $periods->pluck('id'))
                    ->get();
                $annualNetIncome = $payrolls->sum('net_taxable_income');
                $payrollData['basic_salary'] = $annualNetIncome / 12;
                $payrollData['employee_id'] = $employee->id;
                $nthMonthPayroll = Payroll::create($payrollData);
                $nthMonthPayrolls[] = $nthMonthPayroll;
                DB::commit();
            } catch (Exception) {
                DB::rollBack();
            }
        }
        return $nthMonthPayrolls;
    }

    public function getEmployees(Company $company, array $input)
    {
        $employees = $company->employees()->where('status', Employee::ACTIVE)
            ->when($input['employee_id'] != 'all', function ($query) use ($input) {
                $query->where('id', $input['employee_id']);
            })->get();

        if ($employees->isEmpty()) {
            throw new Exception('Employee not found.');
        }
        return $employees;
    }
}
