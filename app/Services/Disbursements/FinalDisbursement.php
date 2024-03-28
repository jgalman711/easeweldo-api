<?php

namespace App\Services\Disbursements;

use App\Enumerators\PayrollEnumerator;
use App\Models\Employee;
use App\Models\Payroll;
use App\Models\Period;
use App\Repositories\DisbursementRepository;
use App\Services\Payroll\GeneratePayrollService;
use Carbon\Carbon;

class FinalDisbursement extends BaseDisbursement
{
    public function create(): Period
    {
        $disbursementRepository = app()->make(DisbursementRepository::class);
        $disbursement = $disbursementRepository->getLatestDisbursement(
            PayrollEnumerator::TYPE_REGULAR,
            PayrollEnumerator::STATUS_PAID
        );
        $startDate = $disbursement ? Carbon::parse($disbursement->end_date)->addDay() : null;
        $this->input = [
            ...$this->input,
            'start_date' => $startDate,
            'end_date' => $this->input['salary_date'],
        ];

        return Period::create($this->input);
    }

    public function generatePayroll(Period $disbursement, Employee $employee): Payroll
    {
        $payrollService = app()->make(GeneratePayrollService::class);

        return $payrollService->generate($employee->company, $disbursement, $employee);
    }
}
