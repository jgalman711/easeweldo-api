<?php

namespace App\Http\Controllers;

use App\Http\Requests\BiometricsRequest;
use App\Http\Resources\BaseResource;
use App\Models\Biometrics;
use App\Models\Company;
use App\Services\BiometricsService;
use Exception;
use Illuminate\Http\JsonResponse;

class BiometricsController extends Controller
{
    protected $biometricsService;

    public function __construct(BiometricsService $biometricsService)
    {
        $this->biometricsService = $biometricsService;
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
        try {
            $this->biometricsService->initialize($biometrics);
            $biometrics->status = Biometrics::STATUS_ACTIVE;
            return $this->sendResponse(new BaseResource($biometrics), 'Biometrics data saved successfully.');
        } catch (Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }

    public function update(BiometricsRequest $request, Company $company, int $biometricsId): JsonResponse
    {
        try {
            $input = $request->validated();
            $biometrics = $company->biometrics()->find($biometricsId);
            $biometrics->update($input);
            $this->forget($company, $biometricsId);
            if ($biometrics->status == Biometrics::STATUS_ACTIVE) {
                $this->biometricsService->initialize($biometrics);
            }
            return $this->sendResponse(new BaseResource($biometrics), 'Biometrics data updated successfully.');
        } catch (Exception $e) {
            $biometrics->status = Biometrics::STATUS_INACTIVE;
            $biometrics->save();
            return $this->sendError($e->getMessage());
        }
    }

    public function destroy(Company $company, int $biometricsId): JsonResponse
    {
        $biometrics = $company->biometrics()->find($biometricsId);
        $biometrics->delete();
        $this->forget($company, $biometricsId);
        return $this->sendResponse(new BaseResource($biometrics), 'Biometrics data deleted successfully.');
    }
}
