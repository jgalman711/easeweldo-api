<?php

namespace App\Http\Controllers\Period;

use App\Http\Controllers\Controller;
use App\Models\Company;
use App\Models\Period;
use App\Services\Payroll\GeneratePayrollService;
use Exception;
use Illuminate\Http\JsonResponse;

class GeneratePayrollsController extends Controller
{
    protected $generatePayrollService;

    public function __construct(GeneratePayrollService $generatePayrollService)
    {
        $this->generatePayrollService = $generatePayrollService;
    }

    public function __invoke(Company $company, Period $period): JsonResponse
    {
        if ($period->status == Period::STATUS_UNINITIALIZED) {
            $period->status = Period::STATUS_PENDING;
            $payrolls = [];
            foreach ($company->employees as $employee) {
                try {
                    $payrolls[] = $this->generatePayrollService->generate($company, $period, $employee);
                } catch (Exception $e) {
                    $payrolls[] = $e->getMessage();
                    $period->status = Period::STATUS_FAILED;
                }
            }
            $period->save();
            return $this->sendResponse($payrolls, 'Payrolls generated successfully.');
        } else {
            return $this->sendError('Period already generated payrolls.');
        }
    }
}
