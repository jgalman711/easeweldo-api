<?php

namespace App\Http\Controllers;

use App\Http\Requests\SettingRequest;
use App\Http\Resources\BaseResource;
use App\Models\Company;
use Illuminate\Http\JsonResponse;

class SettingController extends Controller
{
    public function index(Company $company): JsonResponse
    {
        $settings = $company->setting;
        return $this->sendResponse(new BaseResource($settings), 'Company settings retrieved successfully.');
    }

    public function update(SettingRequest $request, Company $company): JsonResponse
    {
        $input = $request->validated();
        $settings = $company->setting;
        $settings->update($input);
        return $this->sendResponse(
            new BaseResource($settings),
            'Company settings updated successfully. Changes will be applied next period.'
        );
    }
}
