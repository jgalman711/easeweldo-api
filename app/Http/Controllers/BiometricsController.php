<?php

namespace App\Http\Controllers;

use App\Http\Requests\BiometricsRequest;
use App\Http\Resources\BaseResource;
use App\Models\Biometrics;
use App\Models\Company;
use App\Services\BiometricsService;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BiometricsController extends Controller
{
    protected $biometricsService;

    public function __construct(BiometricsService $biometricsService)
    {
        $this->biometricsService = $biometricsService;
        $this->setCacheIdentifier('employees');
    }

    public function index(Request $request, Company $company): JsonResponse
    {
        $biometrics = $this->applyFilters($request, $company->biometrics());

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
        $biometrics = Biometrics::updateOrCreate([
            'company_id' => $company->id,
            'ip_address' => $request->ip_address,
            'port' => $request->port,
        ], [
            'provider' => $request->provider,
            'model' => $request->model,
            'product_number' => $request->product_number,
            'status' => Biometrics::STATUS_INACTIVE,
        ]);
        try {
            $this->biometricsService->initialize($biometrics);
            $biometrics->status = Biometrics::STATUS_ACTIVE;
            $biometrics->save();
            $message = 'Biometrics data saved successfully.';
        } catch (Exception) {
            $message = 'Biometrics data saved successfully but was not able to connect to the device.';
        }

        return $this->sendResponse(new BaseResource($biometrics), $message);
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
