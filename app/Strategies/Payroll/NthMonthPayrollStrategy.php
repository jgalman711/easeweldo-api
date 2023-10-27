<?php

namespace App\Strategies\Payroll;

use App\Enumerators\PayrollEnumerator;
use App\Interfaces\PayrollStrategy;
use App\Models\Company;
use App\Models\Employee;
use App\Models\Payroll;
use App\Models\Period;
use Carbon\Carbon;
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
        $now = Carbon::now();
        foreach ($employees as $employee) {
            try {
                DB::beginTransaction();
                $period = Period::firstOrCreate([
                    'company_id' => $company->id,
                    'description' => $payrollData['description'],
                    'start_date' =>  $now->copy()->startOfYear()->format('Y-m-d'),
                    'end_date' => $now->copy()->endOfYear()->format('Y-m-d'),
                    'type' => Period::TYPE_NTH_MONTH_PAY
                ], [
                    'status' => Period::STATUS_PENDING,
                    'salary_date' => Carbon::parse($payrollData['pay_date'])->format('Y-m-d')
                ]);

                $payrolls = $employee->payrolls()
                    ->whereIn('period_id', $periods->pluck('id'))
                    ->get();
                $annualNetIncome = $payrolls->sum('net_taxable_income');
                $payrollData['basic_salary'] = $annualNetIncome / 12;
                $payrollData['employee_id'] = $employee->id;
                $payrollData['period_id'] = $period->id;
                $nthMonthPayroll = Payroll::create($payrollData);
                $nthMonthPayrolls[] = $nthMonthPayroll;
                DB::commit();
            } catch (Exception $e) {
                DB::rollBack();
                throw $e;
            }
        }
        return $nthMonthPayrolls;
    }

    public function getEmployees(Company $company, array $input)
    {
        $employees = $company->employees()->where('status', Employee::ACTIVE)
            ->when($input['employee_id'][0] != 'all', function ($query) use ($input) {
                $query->whereIn('id', $input['company_employee_id']);
            })->get();

        if ($employees->isEmpty()) {
            throw new Exception('Employee not found.');
        }
        return $employees;
    }
}
