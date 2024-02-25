<?php

namespace App\Http\Controllers;

use App\Http\Requests\DisbursementRequest;
use App\Models\Company;
use App\Services\DisbursementService;
use Exception;
use Illuminate\Http\Request;

class DisbursementController extends Controller
{
    protected $disbursementService;

    public function __construct(DisbursementService $disbursementService)
    {
        $this->disbursementService = $disbursementService;
    }

    public function store(DisbursementRequest $request, Company $company)
    {
        $input = $request->validated();

        if ($request->has('employee_id')) {
            throw_unless(
                $this->isCompanyEmployees($company, $request->employee_id),
                new Exception("Employee IDs must belong to the company.")
            );
        }
        $disbursement = $this->disbursementService->create($company, $input);
        return $this->sendResponse($disbursement, 'Payrolls generated successfully.');
    }
}
