<?php

namespace App\Http\Controllers;

use App\Http\Requests\PeriodRequest;
use App\Http\Resources\BaseResource;
use App\Models\Company;
use App\Models\Period;
use Illuminate\Http\JsonResponse;

class PeriodsController extends Controller
{
    public function index(Company $company): JsonResponse
    {
        $periods = $company->periods;
        return $this->sendResponse(BaseResource::collection($periods), 'Payroll periods retrieved successfully.');
    }

    public function store(PeriodRequest $request, Company $company): JsonResponse
    {
        $input = $request->validated();
        $input['company_id'] = $company->id;
        $period = Period::create($input);
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
