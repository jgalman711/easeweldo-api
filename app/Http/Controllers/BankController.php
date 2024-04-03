<?php

namespace App\Http\Controllers;

use App\Http\Requests\BankRequest;
use App\Http\Resources\BankResource;
use App\Models\Bank;
use App\Models\Company;

class BankController extends Controller
{
    public function index(Company $company)
    {
        return $this->sendResponse(
            new BankResource($company->banks()->first()),
            'Company bank retrieved successfully.'
        );
    }

    public function store(BankRequest $bankRequest, Company $company)
    {
        $input = $bankRequest->validated();
        $input['company_id'] = $company->id;
        $bank = Bank::updateOrCreate(['company_id' => $company->id], $input);
        return $this->sendResponse(new BankResource($bank), 'Company bank updated successfully.');
    }
}
