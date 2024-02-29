<?php

namespace App\Services;

use App\Enumerators\DisbursementEnumerator;
use App\Models\Company;
use App\Models\Period;
use App\Services\Disbursements\DisbursementFactory;

class DisbursementService
{
    protected $disbursementFactory;

    public function __construct(DisbursementFactory $disbursementFactory)
    {
        $this->disbursementFactory = $disbursementFactory;
    }

    public function create(Company $company, array $input)
    {
        $employees = $company->employees()->whereIn('id', $input['employee_id'] ?? [])->get();
        $employees = $employees->isEmpty() ? $company->employees : $employees;
        $input['company_id'] = $company->id;
        $input['status'] = DisbursementEnumerator::STATUS_UNINITIALIZED;
        $disbursementGenerator = $this->disbursementFactory->initialize($input);
        $disbursement = $disbursementGenerator->create($input);
        foreach ($employees as $employee) {
            try {
                $payrolls[] = $disbursementGenerator->generatePayroll($disbursement, $employee);
            } catch (\Exception $e) {
                $payrolls[] = $e->getMessage();
                $disbursement->status = Period::STATUS_FAILED;
            }
        }
        return $payrolls;
    }
}
