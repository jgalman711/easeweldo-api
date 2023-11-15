<?php

namespace App\Http\Controllers;

use App\Http\Resources\DashboardResource;
use App\Models\Company;
use App\Services\PeriodService;
use Illuminate\Http\JsonResponse;

class DashboardController extends Controller
{
    protected $periodService;

    public function __construct(PeriodService $periodService)
    {
        $this->periodService = $periodService;
    }

    public function index(Company $company): JsonResponse
    {
        $period = $this->periodService->getLatestPeriod($company);
        $data = [
            'period' => $period
        ];
        return $this->sendResponse(new DashboardResource($data), 'Dashboard data retrieved successfully.');
    }
}
