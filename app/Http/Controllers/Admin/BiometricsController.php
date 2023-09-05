<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminBiometricsRequest;
use App\Http\Resources\BaseResource;
use App\Models\Biometrics;
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
        $this->setCacheIdentifier('biometrics');
    }

    public function index(Request $request): JsonResponse
    {
        $biometrics = $this->applyFilters($request, Biometrics::with(['company:id,name,slug,status']), [
            'name',
            'ip_address',
            'port',
            'provider',
            'model',
            'product_number',
            'status',
            'company.name'
        ]);
        return $this->sendResponse(BaseResource::collection($biometrics), 'Biometrics data retrieved successfully.');
    }

    public function show(int $biometricsId): JsonResponse
    {
        $biometrics = Biometrics::find($biometricsId);
        return $this->sendResponse(new BaseResource($biometrics), 'Biometrics data retrieved successfully.');
    }

    public function store(AdminBiometricsRequest $request): JsonResponse
    {
        $biometrics = Biometrics::create([
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

    public function update(AdminBiometricsRequest $request, int $biometricsId): JsonResponse
    {
        try {
            $biometrics = Biometrics::find($biometricsId);
            $biometrics->update($request->validated());
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

    public function destroy(int $biometricsId): JsonResponse
    {
        $biometrics = Biometrics::find($biometricsId);
        $biometrics->delete();
        return $this->sendResponse(new BaseResource($biometrics), 'Biometrics data deleted successfully.');
    }
}
