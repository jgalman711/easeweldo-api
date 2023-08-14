<?php

namespace App\Http\Controllers;

use App\Http\Requests\BiometricsRequest;
use App\Http\Resources\BaseResource;
use App\Models\Biometrics;
use App\Models\Company;
use Illuminate\Http\JsonResponse;

class BiometricsController extends Controller
{
    public function __construct()
    {
        $this->setCacheIdentifier('employees');
    }

    public function index(Company $company): JsonResponse
    {
        $biometrics = $this->remember($company, function () use ($company) {
            return $company->biometrics;
        });
        return $this->sendResponse(BaseResource::collection($biometrics), 'Biometrics data retrieved successfully.');
    }

    public function show(Company $company, int $biometricsId): JsonResponse
    {
        $biometrics = $this->remember($company, function () use ($company, $biometricsId) {
            return $company->biometrics()->find($biometricsId);
        }, $biometricsId);
        return $this->sendResponse(new BaseResource($biometrics), 'Biometrics data retrieved successfully.');
    }

    public function store(BiometricsRequest $request, Company $company): JsonResponse
    {
        $biometrics = Biometrics::create([
            'company_id' => $company->id,
            'status' => Biometrics::STATUS_INACTIVE,
            ...$request->validated(),
        ]);
        return $this->sendResponse(new BaseResource($biometrics), 'Biometrics data saved successfully.');
    }

    public function update(BiometricsRequest $request, Company $company, int $biometricsId): JsonResponse
    {
        $input = $request->validated();
        $biometrics = $company->biometrics()->find($biometricsId);
        $biometrics->update($input);
        $this->forget($company, $biometricsId);
        return $this->sendResponse(new BaseResource($biometrics), 'Leave updated successfully.');
    }
}
