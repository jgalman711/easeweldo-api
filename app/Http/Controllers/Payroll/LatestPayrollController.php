<?php

namespace App\Http\Controllers\Payroll;

use App\Enumerators\PayrollEnumerator;
use App\Http\Controllers\Controller;
use App\Http\Resources\PayrollResource;
use App\Models\Company;
use App\Services\PeriodService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LatestPayrollController extends Controller
{
    protected $periodService;

    public function __construct(PeriodService $periodService)
    {
        $this->periodService = $periodService;
    }

    public function show(Request $request, Company $company): JsonResponse
    {
        $periodsBuilder = $this->periodService->getBuilderPeriodsByType($company, $request->all());
        $latestPeriod = $periodsBuilder->first();
        $payrollsQuery = $company->payrolls()
            ->where('type', PayrollEnumerator::TYPE_REGULAR)
            ->where('period_id', $latestPeriod->id)
            ->with('employee');
        $payrolls = $this->applyFilters($request, $payrollsQuery, [
            'employee.first_name',
            'employee.last_name'
        ]);
        return $this->sendResponse(PayrollResource::collection($payrolls), 'Payrolls retrieved successfully.');
    }
}
