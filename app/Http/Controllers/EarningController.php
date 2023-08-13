<?php

namespace App\Http\Controllers;

use App\Http\Requests\EarningRequest;
use App\Http\Resources\BaseResource;
use App\Models\Company;
use App\Models\Earning;
use Illuminate\Http\Request;

class EarningController extends Controller
{
    public function __construct()
    {
        $this->setCacheIdentifier('earnings');
    }

    public function index(Request $request, Company $company)
    {
        $earnings = $this->remember($company, function () use ($company) {
            return $company->earnings;
        }, $request);
        return $this->sendResponse(new BaseResource($earnings), 'Earnings retrieved successfully.');
    }

    public function store(EarningRequest $request, Company $company)
    {
        $earning = Earning::updateOrCreate(
            ['company_id' => $company->id],
            [...$request->validated()]
        );
        $this->forget($company);
        return $this->sendResponse(new BaseResource($earning), 'Earning created successfully.');
    }
}
