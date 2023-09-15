<?php

namespace App\Strategies\Payroll;

use App\Enumerators\PayrollEnumerator;
use App\Interfaces\PayrollStrategy;
use App\Models\Company;
use App\Models\Employee;
use App\Models\Payroll;
use Exception;
use Illuminate\Support\Facades\DB;

class FinalPayrollStrategy implements PayrollStrategy
{
    public function generate($employees, $payrollData): array
    {
        $finalPayrolls = [];
        $errors = [];
        $payrollData['type'] = PayrollEnumerator::TYPE_NTH_MONTH_PAY;
        foreach ($employees as $employee) {
            try {
                DB::beginTransaction();
                if ($employee->date_of_termination == null) {
                    $errors[] = [
                        'employee_id' => $employee->id,
                        'employee_full_name' => $employee->fullName,
                        'error' => "Unable to calculate the final pay for {$employee->fullName}. Please set the date of termination in the employee module"
                    ];
                    continue;
                }
                $latestPayroll = $this->getLatestPayroll($employee);

                dd($latestPayroll);
                // If not paid, calculate the worked here. If not subscribed to time and attendance must input
                // the hours worked manually.
                // Calculate the 13th month pay
                // Calculate the remaining leaves if reimbursible

                DB::commit();
            } catch (Exception $e) {
                $errors[] = [
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
            ->when($input['employee_id'] != 'all', function ($query) use ($input) {
                $query->where('id', $input['employee_id']);
            })->get();

        if ($employees->isEmpty()) {
            throw new Exception('Employee not found.');
        }
        return $employees;
    }

    protected function getLatestPayroll(Employee $employee): Payroll
    {
        return $employee->payrolls()
            ->where('status', PayrollEnumerator::STATUS_PAID)
            ->where('type', PayrollEnumerator::TYPE_REGULAR)
            ->whereNotNull('period_id')
            ->latest()
            ->first();
    }
}
