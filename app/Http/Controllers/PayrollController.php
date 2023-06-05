<?php

namespace App\Http\Controllers;

use App\Http\Requests\PayrollRequest;
use App\Http\Resources\BaseResource;
use App\Models\Company;
use App\Models\Payroll;
use App\Services\PayrollService;
use App\Traits\Filter;
use Illuminate\Http\JsonResponse;
use Illuminate\Http\Request;

class PayrollController extends Controller
{
    use Filter;

    protected $payrollService;

    public function __construct(PayrollService $payrollService)
    {
        $this->payrollService = $payrollService;
    }

    public function index(Request $request, Company $company): JsonResponse
    {
        $payrolls = $this->applyFilters($request, $company->payrolls()->with('employee'), [
            'status',
            'employee.first_name',
            'employee.last_name'
        ]);
        return $this->sendResponse($payrolls, 'Payrolls retrieved successfully.');
    }
    
    public function store(PayrollRequest $request, Company $company): JsonResponse
    {
        $input = $request->validated();
        $input['company_id'] = $company->id;
        $payroll = Payroll::create($input);
        return $this->sendResponse(new BaseResource($payroll), 'Payroll created successfully');
    }

    public function show(Company $company, int $payrollId): JsonResponse
    {
        $payroll = $company->payrolls->where('id', $payrollId)->first();
        return $this->sendResponse(new BaseResource($payroll), 'Payroll retrieved successfully');
    }

    public function update(PayrollRequest $request, Company $company, int $payrollId): JsonResponse
    {
        $input = $request->validated();
        $payroll = $company->payrolls->where('id', $payrollId)->first();
        if ($request->has('regenerate') && $request->regenerate === true) {
            $payroll->delete();
            $payroll = $this->payrollService->generate($payroll->period, $payroll->employee);
        } else {
            $payroll->update($input);
        }
        return $this->sendResponse(new BaseResource($payroll), 'Payroll updated successfully.');
    }

    public function destroy(Company $company, int $payrollId): JsonResponse
    {
        $payroll = $company->payrolls->where('id', $payrollId)->first();
        return $this->sendResponse(new BaseResource($payroll), 'Payroll deleted successfully');
    }
}
