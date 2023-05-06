<?php

namespace App\Http\Controllers;

use App\Http\Requests\PeriodRequest;
use App\Http\Resources\BaseResource;
use App\Models\Company;
use App\Models\Period;
use App\Services\PeriodService;
use Illuminate\Http\JsonResponse;

class PeriodsController extends Controller
{
    protected $periodService;

    public function __construct(PeriodService $periodService)
    {
        $this->periodService = $periodService;
    }
    /**
     * IMPORTANT: Period refers to the cut-off period of payroll.
     *
     * Example:
     * The employees' salaries are disbursed on every 15th and 30th of the month.
     * The period must 15-day duration before the 15th. so the duration would be 26th of the previous month up to 10th.
     * This must be explicitly put in the Periods Page so the user can put the correct period.
     *
     */
    public function index(Company $company): JsonResponse
    {
        $periods = $company->periods;
        return $this->sendResponse(BaseResource::collection($periods), 'Payroll periods retrieved successfully.');
    }

    public function store(PeriodRequest $request, Company $company): JsonResponse
    {
        $input = $request->validated();
        $period = $this->periodService->initializeFromSalaryDate($input, $company);
        return $this->sendResponse(new BaseResource($period), 'Payroll period created successfully.');
    }

    public function show(Company $company, int $periodId): JsonResponse
    {
        $period = $company->getPeriodById($periodId);
        return $this->sendResponse(new BaseResource($period), 'Payroll period retrieved successfully.');
    }

    public function update(PeriodRequest $request, Company $company, int $periodId): JsonResponse
    {
        $period = $company->getPeriodById($periodId);
        $input = $request->validated();
        if ($period->status != Period::STATUS_PENDING) {
            return $this->sendError('This period cannot be edited as it has already been ' . $period->status . '.');
        }
        $companyPreviousPeriod = $company->periods()->latest()->first();
        if ($companyPreviousPeriod && $input['start_date'] <= $companyPreviousPeriod->end_date) {
            return $this->sendError('This period overlaps the previous period. Please adjust');
        }
        $period->update($input);
        return $this->sendResponse(new BaseResource($period), 'Payroll period updated successfully.');
    }

    public function destroy(Company $company, int $periodId): JsonResponse
    {
        $period = $company->getPeriodById($periodId);
        if ($period->status != Period::STATUS_PENDING) {
            return $this->sendError('This period cannot be edited as it has already been ' . $period->status . '.');
        }
        $period->delete();
        return $this->sendResponse(new BaseResource($period), 'Payroll period deleted successfully.');
    }
}
