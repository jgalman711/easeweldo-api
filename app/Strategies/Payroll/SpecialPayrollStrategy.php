<?php

namespace App\Strategies\Payroll;

use App\Enumerators\PayrollEnumerator;
use App\Interfaces\PayrollStrategy;
use App\Models\Company;
use App\Models\Employee;
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
            ->when($input['employee_id'] != 'all', function ($query) use ($input) {
                $query->where('id', $input['employee_id']);
            })->get();

        if ($employees->isEmpty()) {
            throw new Exception('Employee not found.');
        }
        return $employees;
    }
}
