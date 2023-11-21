<?php

namespace App\Http\Controllers;

use App\Factories\ReportStrategyFactory;
use App\Http\Requests\ReportRequest;
use App\Models\Company;
use Illuminate\Http\JsonResponse;

class ReportController extends Controller
{
    protected $reportStrategyFactory;

    public function __construct(ReportStrategyFactory $reportStrategyFactory)
    {
        $this->reportStrategyFactory = $reportStrategyFactory;
    }

    public function show(ReportRequest $reportRequest, Company $company, string $type): JsonResponse
    {
        $filter = $reportRequest->validated();
        $reportStrategy = $this->reportStrategyFactory->createStrategy($type);
        $response['data'] = $reportStrategy->generate($company, $filter);
        return $this->sendResponse($response, 'Reports successfully retrieved.');
    }
}
