<?php

namespace App\Strategies\Report;

use App\Http\Resources\PayrollExpenseReportResource;
use App\Interfaces\ReportStrategy;
use App\Models\Company;
use Exception;

class EmployeePayrollReportStrategy implements ReportStrategy
{
    public function generate(Company $company, array $data = []): PayrollExpenseReportResource
    {
        $employee = $company->employees()->find($data['employee_id']);
        throw_unless($employee, new Exception("Employee not found."));
        
        $fromDate = $data['from_date'] ?? null;
        $toDate = $data['to_date'] ?? null;
        
        $payrolls = $employee->payrolls()
            ->when($fromDate, function ($query) use ($fromDate) {
                return $query->whereDate('created_at', '>=', $fromDate);
            })
            ->when($toDate, function ($query) use ($toDate) {
                return $query->whereDate('created_at', '<=', $toDate);
            })->get();

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
