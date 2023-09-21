<?php

namespace App\Http\Controllers;

use App\Factories\ReportStrategyFactory;
use App\Http\Requests\ReportRequest;
use App\Models\Company;
use Exception;
use Illuminate\Http\JsonResponse;

class ReportController extends Controller
{
    protected $reportService;

    public function show(ReportRequest $request, Company $company, string $type): JsonResponse
    {
        try {
            $input = $request->validated();
            $reportStrategy = ReportStrategyFactory::createStrategy($type);
            $report = $reportStrategy->generate($company, $input);
            return $this->sendResponse($report, 'Report data retrieved successfully.');
        } catch (Exception $e) {
            return $this->sendError($e->getMessage());
        }
    }
}
