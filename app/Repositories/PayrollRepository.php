<?php

namespace App\Repositories;

use App\Enumerators\PayrollEnumerator;
use App\Models\Payroll;
use Illuminate\Database\Eloquent\Collection;

class PayrollRepository
{
    public function getEmployeePayrollsByDateRange(
        int $employeeId,
        array $range,
        string $type = PayrollEnumerator::TYPE_REGULAR,
        string $status = PayrollEnumerator::STATUS_PAID
    ): Collection {
        return Payroll::selectRaw('YEAR(pay_date) as year, MONTH(pay_date) as month, SUM(basic_salary) as total_basic_salary')
            ->where('employee_id', $employeeId)
            ->where('type', $type)
            ->where('status', $status)
            ->whereBetween('pay_date', [$range['start_date'], $range['end_date']])
            ->groupBy('year', 'month')
            ->get();
    }
}
