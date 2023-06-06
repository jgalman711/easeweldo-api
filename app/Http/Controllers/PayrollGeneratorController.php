<?php

namespace App\Http\Controllers;

use App\Models\Company;
use App\Models\Period;
use App\Services\PayrollService;
use Carbon\Carbon;
use Exception;
use Illuminate\Http\JsonResponse;
use Illuminate\Support\Facades\DB;

class PayrollGeneratorController extends Controller
{
    protected $payrollService;

    protected $successPayroll = [];

    protected $failedPayroll = [];

    public function __construct(PayrollService $payrollService)
    {
        $this->payrollService = $payrollService;
    }

    public function store(Company $company, int $periodId): JsonResponse
    {
        $period = $company->getPeriodById($periodId);
        $endDate = Carbon::parse($period->end_date);
        if ($endDate->isFuture()) {
            return $this->sendError("Unable to generate payroll. The period has not yet concluded.");
        }
        $period = $company->getPeriodById($period->id);
        $period->status = Period::STATUS_PROCESSING;
        $period->save();
        foreach ($company->employees as $employee) {
            try {
                DB::beginTransaction();
                $this->deleteExistingPayroll($employee->payrolls(), $periodId);
                $payroll = $this->payrollService->generate($period, $employee);
                array_push($this->successPayroll, $payroll);
                DB::commit();
            } catch (Exception $e) {
                array_push($this->failedPayroll, $e->getMessage());
                DB::rollBack();
            }
        }
        return $this->sendResponse([
            'success' => $this->successPayroll,
            'failed' => $this->failedPayroll
        ], 'Payrolls created successfully.');
    }

    private function deleteExistingPayroll($payrolls, int $periodId): void
    {
        $payroll = $payrolls->where('period_id', $periodId)->first();
        if ($payroll) {
            $payroll->delete();
        }
    }
}
