<?php

namespace App\Strategies\Report;

use App\Interfaces\ReportStrategy;
use App\Models\Company;
use Carbon\Carbon;

class MonthlyReportStrategy implements ReportStrategy
{
    public function generate(Company $company, array $data)
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
        })
        ->whereNotNull('pay_date')
        ->orderBy('pay_date', 'asc')
        ->get();

        $payrollsByMonth = $payrolls->groupBy(function ($payroll) {
            return Carbon::parse($payroll->pay_date)->format('Y F');
        });

        $report = [];
        foreach ($payrollsByMonth as $month => $payrolls) {
            $report[$month] = [
                'total_contributions' => $payrolls->sum('total_contributions'),
                'withheld_tax' => $payrolls->sum('withheld_tax'),
                'net_income' => $payrolls->sum('net_income'),
            ];
        }
        return $report;
    }
}
