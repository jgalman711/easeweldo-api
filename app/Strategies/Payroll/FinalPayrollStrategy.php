<?php

namespace App\Strategies\Payroll;

use App\Enumerators\PayrollEnumerator;
use App\Interfaces\PayrollStrategy;
use App\Models\Company;
use App\Models\Employee;
use App\Models\Payroll;
use App\Models\Period;
use App\Services\Payroll\PayrollService;
use Carbon\Carbon;
use Exception;
use Illuminate\Support\Facades\DB;

class FinalPayrollStrategy implements PayrollStrategy
{
    protected $payrollService;

    public function __construct()
    {
        $this->payrollService = app()->make(PayrollService::class);
    }

    public function generate($employees, $payrollData): array
    {
        $finalPayrolls = [];
        $errors = [];
        $payrollData['type'] = PayrollEnumerator::TYPE_FINAL;
        foreach ($employees as $employee) {
            try {
                DB::beginTransaction();
                if ($employee->date_of_termination == null) {
                    $errors[] = [
                        'company_employee_id' => $employee->company_employee_id,
                        'employee_id' => $employee->id,
                        'employee_full_name' => $employee->fullName,
                        'error' => "Unable to calculate the final pay for {$employee->fullName}. Please set the date of termination in the employee module"
                    ];
                    continue;
                }
                $payrollData['employee_id'] = $employee->id;
                $payroll = $this->calculate($employee, $payrollData);

                $finalPayrolls[] = $payroll;
                DB::commit();
            } catch (Exception $e) {
                $errors[] = [
                    'company_employee_id' => $employee->company_employee_id,
                    'employee_id' => $employee->id,
                    'employee_full_name' => $employee->fullName,
                    'error' => $e->getMessage()
                ];
                DB::rollBack();
            }
        }
        return [$finalPayrolls, $errors];
    }

    public function getEmployees(Company $company, array $input)
    {
        $employees = $company->employees()->where('status', Employee::ACTIVE)
            ->when($input['employee_id'][0] != 'all', function ($query) use ($input) {
                $query->whereIn('id', $input['employee_id']);
            })->get();

        if ($employees->isEmpty()) {
            throw new Exception('Employee not found.');
        }
        return $employees;
    }

    protected function calculate(Employee $employee, array $payrollData): Payroll
    {
        $latestPayroll = $this->getLatestPayroll($employee);
        $endDate = $employee->date_of_termination;
        if ($latestPayroll) {
            $startDate = Carbon::parse($latestPayroll->period->end_date)->addDay()->format('Y-m-d');
        } else {
            $startDate = $employee->date_of_hire;
        }
        $period = Period::create([
            'company_id' => $payrollData['company']->id,
            'description' => 'Final Pay Period',
            'start_date' => $startDate,
            'end_date' => $endDate,
            'status' => Period::STATUS_PENDING,
            'salary_date' => Carbon::parse($endDate)->addMonth()->format('Y-m-d')
        ]);

        $periodsForYear = $payrollData['company']->periodsForYear(date('Y'));
        $payrolls = $employee->payrolls()
            ->whereIn('period_id', $periodsForYear->pluck('id'))
            ->get();
        $thirteenthMonthPay = $payrolls->sum('net_taxable_income') / 12;

        $payroll = $this->payrollService->generate($period, $employee, [
            "pay_date" => $payrollData['pay_date'] ?? $period->salary_date,
            "taxable_earnings" => [
                [
                    "pay" => $thirteenthMonthPay,
                    "name" => $payrollData['description'],
                    "type" => PayrollEnumerator::TYPE_FINAL
                ]
            ]
        ]);

        $this->unlink($payroll, $period);
        return $payroll;
    }

    protected function unlink(Payroll $payroll, Period $period): void
    {
        $payroll->period_id = null;
        $period->delete();
        $period->save();
        $payroll->save();
    }
    protected function getLatestPayroll(Employee $employee, string $status = PayrollEnumerator::STATUS_PAID): ?Payroll
    {
        return $employee->payrolls()
            ->where('type', PayrollEnumerator::TYPE_REGULAR)
            ->where('status', $status)
            ->whereNotNull('period_id')
            ->latest()
            ->first();
    }
}
