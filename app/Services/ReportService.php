<?php

namespace App\Services;

use App\Models\Company;
use Carbon\Carbon;

class ReportService
{
    public function getPayrollReportsByDate(Company $company, array $data): array
    {
        $payrolls = $company->payrolls()->where(function ($query) use ($data) {
            if (isset($data['from_date'])) {
                $query->whereDate('pay_date', '>=', $data['from_date']);
            }
            if (isset($data['to_date'])) {
                $query->whereDate('pay_date', '<=', $data['to_date']);
            }
            if (isset($data['employee_id'])) {
                $query->where('employee_id', $data['employee_id']);
            }
        })->get();

        $payrollTotals = [
            'hours_worked' => [],
            'expected_hours_worked' => [],
            'sss_contributions' => [],
            'philhealth_contributions' => [],
            'pagibig_contributions' => [],
            'withheld_tax' => [],
            'net_income' => [],
        ];

        $payrollsByMonth = $payrolls->groupBy(function ($payroll) {
            return Carbon::parse($payroll->pay_date)->format('Y F');
        });

        foreach ($payrollTotals as $category => &$totals) {
            $totals = $payrollsByMonth->map(function ($monthlyPayrolls) use ($category) {
                return $monthlyPayrolls->sum($category);
            });
        }
        return $payrollTotals;
    }
}
