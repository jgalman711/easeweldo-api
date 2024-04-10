<?php

namespace App\Http\Controllers;

use App\Http\Requests\SettingRequest;
use App\Http\Resources\BaseResource;
use App\Http\Resources\SettingsResource;
use App\Models\Company;
use App\Models\Setting;
use App\Services\PeriodService;
use Illuminate\Http\JsonResponse;

class SettingController extends Controller
{
    protected $periodService;

    public function __construct(PeriodService $periodService)
    {
        $this->periodService = $periodService;
    }

    public function index(Company $company): JsonResponse
    {
        $settings = $company->setting;
        if ($settings) {
            return $this->sendResponse(
                new SettingsResource($company->setting),
                'Company settings retrieved successfully.'
            );
        } else {
            return $this->sendError("No company settings found");
        }
    }

    public function store(SettingRequest $request, Company $company): JsonResponse
    {
        $input = $request->validated();
        $settings = Setting::updateOrCreate(
            ['company_id' => $company->id],
            $input
        );

        if ($settings->salary_day && $settings->period_cycle) {
            $this->periodService->convertSalaryDayToDate($settings->salary_day, $settings->period_cycle);
        }

        return $this->sendResponse(new SettingsResource($settings), 'Company settings updated successfully.');
    }
}
