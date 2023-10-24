<?php

namespace App\Http\Controllers\Payroll;

use App\Enumerators\PayrollEnumerator;
use App\Http\Controllers\Controller;
use App\Http\Resources\PayrollResource;
use App\Models\Company;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class LatestPayrollController extends Controller
{
    public function show(Request $request, Company $company): JsonResponse
    {
        $latestPeriod = $company->periods()->latest()->first();
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
