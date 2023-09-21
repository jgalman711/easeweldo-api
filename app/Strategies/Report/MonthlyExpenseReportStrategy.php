<?php

namespace App\Strategies\Report;

use App\Http\Resources\PayrollExpenseReportResource;
use App\Interfaces\ReportStrategy;
use App\Models\Company;

class MonthlyExpenseReportStrategy implements ReportStrategy
{
    public function generate(Company $company, array $data = []): PayrollExpenseReportResource
    {
        $month = $data['month'] ?? now()->month;
        $payrolls = $company->payrolls()->whereMonth('payrolls.created_at', $month)->get();
        
        $monthlyTotals = [
            'hours_worked' => [],
            'expected_hours_worked' => [],
            'sss_contributions' => [],
            'philhealth_contributions' => [],
            'pagibig_contributions' => [],
            'withheld_tax' => [],
            'net_income' => [],
        ];
        
        $payrollsByMonth = $payrolls->groupBy(function ($payroll) {
            return $payroll->created_at->format('F');
        });
        
        foreach ($monthlyTotals as $category => &$totals) {
            $totals = $payrollsByMonth->map(function ($monthlyPayrolls) use ($category) {
                return $monthlyPayrolls->sum($category);
            });
        }
        return new PayrollExpenseReportResource($monthlyTotals);
    }
}
