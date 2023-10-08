<?php

namespace App\Http\Controllers\Period;

use App\Http\Controllers\Controller;
use App\Http\Requests\PeriodRequest;
use App\Http\Resources\BaseResource;
use App\Http\Resources\PeriodResource;
use App\Models\Company;
use App\Models\Period;
use App\Services\PeriodService;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PeriodsController extends Controller
{
    protected $periodService;

    public function __construct(PeriodService $periodService)
    {
        $this->periodService = $periodService;
    }

    public function index(Request $request, Company $company): JsonResponse
    {
        $periods = $this->applyFilters($request, $company->periods());
        return $this->sendResponse(
            new BaseResource($periods),
            'Payroll periods retrieved successfully.'
        );
    }

    public function show(Company $company, int $companyPeriodId): JsonResponse
    {
        $period = Period::where([
            ['company_id', $company->id],
            ['company_period_id', $companyPeriodId]
        ])->firstOrFail();
        return $this->sendResponse(new PeriodResource($period), 'Payroll period retrieved successfully.');
    }

    public function update(PeriodRequest $request, Company $company, int $companyPeriodId): JsonResponse
    {
        $period = Period::where([
            ['company_id', $company->id],
            ['company_period_id', $companyPeriodId]
        ])->firstOrFail();
        $input = $request->validated();
        if ($period->status != Period::STATUS_PENDING) {
            return $this->sendError('This period cannot be edited as it is already ' . $period->status);
        }
        $companyPreviousPeriod = $company->periods()->latest()->first();
        if ($companyPreviousPeriod && $input['start_date'] <= $companyPreviousPeriod->end_date) {
            return $this->sendError('This period overlaps the previous period. Please adjust');
        }
        $period->update($input);
        return $this->sendResponse(new PeriodResource($period), 'Payroll period updated successfully.');
    }

    public function destroy(Company $company, int $periodId): JsonResponse
    {
        $period = $company->getPeriodById($periodId);
        if ($period->status != Period::STATUS_PENDING) {
            return $this->sendError('This period cannot be edited as it is already ' . $period->status);
        }
        $period->delete();
        return $this->sendResponse(new PeriodResource($period), 'Payroll period deleted successfully.');
    }
}
