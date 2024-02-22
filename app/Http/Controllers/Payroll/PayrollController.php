<?php

namespace App\Http\Controllers\Payroll;

use App\Enumerators\ErrorMessagesEnumerator;
use App\Enumerators\PayrollEnumerator;
use App\Factories\PayrollStrategyFactory;
use App\Http\Controllers\Controller;
use App\Http\Requests\Payroll\PayrollRequest;
use App\Http\Resources\PayrollResource;
use App\Models\Company;
use App\Models\Payroll;
use App\Traits\PayrollFilter;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;

class PayrollController extends Controller
{
    use PayrollFilter;

    protected $payrollStrategy;

    public function __construct()
    {
        $this->setCacheIdentifier('payrolls');
    }

    public function index(Request $request, Company $company): JsonResponse
    {
        $payrolls = $company->payrolls()->with('employee');
        $payrolls = $this->applyFilters($request, $payrolls, [
            'employee.first_name',
            'employee.last_name'
        ]);
        return $this->sendResponse(PayrollResource::collection($payrolls), 'Payrolls retrieved successfully.');
    }

    public function show(Company $company, Payroll $payroll): JsonResponse
    {
        $payrollWithEmployee = $this->remember($company, function () use ($payroll, $company) {
            if (!$company->payrolls->contains($payroll)) {
                return $this->sendError('Payroll not found.');
            }
            return $payroll->load('employee');
        }, $payroll);
        return $this->sendResponse(new PayrollResource($payrollWithEmployee), 'Payroll retrieved successfully.');
    }
}
