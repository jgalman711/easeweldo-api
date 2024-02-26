<?php

namespace App\Services;

use App\Enumerators\DisbursementEnumerator;
use App\Enumerators\PayrollEnumerator;
use App\Models\Company;
use App\Models\Payroll;
use App\Models\Period;
use App\Repositories\DisbursementRepository;
use App\Services\Payroll\GenerateExtraPayrollService as GeneratePayrollService;
use Carbon\Carbon;
use Exception;

class DisbursementService
{
    protected $disbursementRepository;
    protected $generatePayrollService;

    public function __construct(
        DisbursementRepository $disbursementRepository,
        GeneratePayrollService $generatePayrollService
    ) {
        $this->disbursementRepository = $disbursementRepository;
        $this->generatePayrollService = $generatePayrollService;
    }

    public function create(Company $company, array $input)
    {
        $employees = $company->employees()->whereIn('id', $input['employee_id'] ?? [])->get();
        $employees = $employees->isEmpty() ? $company->employees : $employees;
        $input['company_id'] = $company->id;
        $input['status'] = DisbursementEnumerator::STATUS_UNINITIALIZED;
        if ($input['type'] == DisbursementEnumerator::TYPE_FINAL) {
            $latestPaidDisbursement = $this->disbursementRepository
                ->getLatestDisbursement(
                    DisbursementEnumerator::TYPE_REGULAR,
                    DisbursementEnumerator::STATUS_COMPLETED
                );
            if ($latestPaidDisbursement) {
                $endDate = Carbon::parse($latestPaidDisbursement->end_date);
                $startDate = $endDate->addDay();
                $input['start_date'] = $startDate->toDateString();
            } else {
                throw new Exception('No paid regular disbursement.');
            }
        }

        $disbursement = Period::create($input);
        $payrolls = [];
        foreach ($employees as $employee) {
            try {
                if (in_array($input['type'], [
                    DisbursementEnumerator::TYPE_FINAL,
                    DisbursementEnumerator::TYPE_NTH_MONTH_PAY
                ])) {
                    $payrolls[] = $this->generatePayrollService->generate($company, $disbursement, $employee);
                } else {
                    $payrolls[] = Payroll::create([
                        'employee_id' => $employee->id,
                        'period_id' => $disbursement->id,
                        'type' => $disbursement->type,
                        'status' => PayrollEnumerator::STATUS_TO_PAY,
                        'description' => $input['description'],
                        'pay_date' => $input['salary_date'],
                        'basic_salary' => $input['pay_amount'],
                    ]);
                }
            } catch (Exception $e) {
                $payrolls[] = $e->getMessage();
                $disbursement->status = Period::STATUS_FAILED;
            }
        }
        return $payrolls;
    }
}
