<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\AdminBiometricsRequest;
use App\Http\Resources\BaseResource;
use App\Models\Biometrics;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class BiometricsController extends Controller
{
    public function __construct()
    {
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
        return $this->sendResponse(new BaseResource($biometrics), 'Biometrics data saved successfully.');
    }

    public function update(AdminBiometricsRequest $request, int $biometricsId): JsonResponse
    {
        $biometrics = Biometrics::find($biometricsId);
        $biometrics->update($request->validated());
        return $this->sendResponse(new BaseResource($biometrics), 'Biometrics data updated successfully.');
    }

    public function destroy(int $biometricsId): JsonResponse
    {
        $biometrics = Biometrics::find($biometricsId);
        $biometrics->delete();
        return $this->sendResponse(new BaseResource($biometrics), 'Biometrics data deleted successfully.');
    }
}
