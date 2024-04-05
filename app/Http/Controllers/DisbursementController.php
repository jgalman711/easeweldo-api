<?php

namespace App\Http\Controllers;

use App\Http\Requests\DisbursementRequest;
use App\Http\Resources\Payroll\BasePayrollResource;
use App\Models\Company;
use App\Services\DisbursementService;
use Exception;
use Illuminate\Http\JsonResponse;

class DisbursementController extends Controller
{
    protected $disbursementService;

    public function __construct(DisbursementService $disbursementService)
    {
        $this->disbursementService = $disbursementService;
    }

    public function store(DisbursementRequest $request, Company $company): JsonResponse
    {
        $input = $request->validated();
        if ($request->has('employee_id')) {
            throw_unless(
                $this->isCompanyEmployees($company, $request->employee_id),
                new Exception('Employee IDs must belong to the company.')
            );
        }
        $disbursement = $this->disbursementService->create($company, $input);

        return $this->sendResponse(BasePayrollResource::collection($disbursement), 'Payrolls generated successfully.');
    }
}
