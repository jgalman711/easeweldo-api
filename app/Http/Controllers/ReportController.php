<?php

namespace App\Http\Controllers;

use App\Enumerators\ReportType;
use App\Http\Requests\ReportRequest;
use App\Http\Resources\BaseResource;
use App\Models\Company;
use App\Services\ReportService;
use Exception;
use Illuminate\Http\JsonResponse;

class ReportController extends Controller
{
    protected $reportService;

    public function __construct(ReportService $reportService)
    {
        $this->reportService = $reportService;
    }

    public function show(ReportRequest $request, Company $company, string $type): JsonResponse
    {
        try {
            $report = $this->reportService->getReport($type, $request, $company);
            return $this->sendResponse(new BaseResource($report), 'Report data retrieved successfully.');
        } catch (Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }
}
