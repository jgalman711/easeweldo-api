<?php

namespace App\Http\Controllers\Report;

use App\Http\Controllers\Controller;
use App\Http\Requests\Report\ExpensesRequest;
use App\Http\Resources\PayrollExpenseReportResource;
use App\Models\Company;
use App\Services\ReportService;

class ExpensesReportController extends Controller
{
    protected $reportService;

    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    public function index(ExpensesRequest $expensesRequest, Company $company)
    {
        $input = $expensesRequest->validated();
        $report = $this->reportService->getPayrollReportsByDate($company, $input);
        return new PayrollExpenseReportResource($report);
    }
}
