<?php

namespace App\Strategies\Payroll;

use App\Enumerators\PayrollEnumerator;
use App\Interfaces\PayrollStrategy;
use App\Models\Company;
use App\Models\Employee;
use App\Models\Payroll;
use Exception;
use Illuminate\Support\Facades\DB;

class SpecialPayrollStrategy implements PayrollStrategy
{
    public function generate($employees, $data): array
    {
        $payrolls = [];
        foreach ($employees as $employee) {
            try {
                DB::beginTransaction();
                $data['basic_salary'] = $data['pay_amount'] ?? 0;
                $data['type'] = PayrollEnumerator::TYPE_SPECIAL;
                $data['status'] = PayrollEnumerator::STATUS_TO_PAY;
                $payrolls[] = $employee->payrolls()->create($data);
                DB::commit();
            } catch (Exception) {
                DB::rollBack();
            }
        }
        return $payrolls;
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

    public function update(Company $company, int $payrollId, array $data): Payroll
    {
        $payroll = $company->payrolls()
            ->where('type', PayrollEnumerator::TYPE_SPECIAL)
            ->findOrFail($payrollId);

        throw_if($payroll->status == PayrollEnumerator::STATUS_PAID, new Exception('Payroll already paid.'));
        $data['basic_salary'] = $data['pay_amount'];
        $payroll->update($data);
        return $payroll;
    }
}
