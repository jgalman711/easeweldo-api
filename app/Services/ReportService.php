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
        } else {
            throw new Exception('Invalid report type ' . $type);
        }

        return $report->get();
    }
}
