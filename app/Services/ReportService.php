<?php

namespace App\Services;

use App\Enumerators\ReportType;
use App\Helpers\PayrollSummaryReport;
use App\Models\Company;
use Exception;
use Illuminate\Http\Request;

class ReportService
{
    protected $reportAttributes;


    public function getReport(string $type, Request $request, Company $company): array
    {
        $periods = $company->periods()->byRange([
                'dateFrom' => $request->date_from,
                'dateTo' => $request->date_to
            ])
            ->with('payrolls')
            ->get();

        if ($type == ReportType::PAYROLL_SUMMARY) {
            $report = new PayrollSummaryReport($periods);
        } elseif ($type == ReportType::EMPLOYEE_PAYROLL_DETAILS) {
            if (!$request->has('employee_id')) {
                throw new Exception('Employee ID is required.');
            }
            $employee = $company->getEmployeeById($request->employee_id);
            $report = new PayrollSummaryReport($periods, $employee);
        } else {
            throw new Exception('Invalid report type ' . $type);
        }

        return $report->get();
    }
}
