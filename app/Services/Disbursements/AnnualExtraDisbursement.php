<?php

namespace App\Services\Disbursements;

use App\Models\Employee;
use App\Models\Payroll;
use App\Models\Period;
use Carbon\Carbon;

class AnnualExtraDisbursement extends BaseDisbursement
{
    public function create(): Period
    {
        $now = Carbon::now();
        $startOfYear = $now->copy()->startOfYear()->format('Y-m-d');
        $endOfYear = $now->copy()->endOfYear()->format('Y-m-d');
        $input = [
            ...$this->input,
            'start_date' => $startOfYear,
            'end_date' => $endOfYear
        ];
        return Period::create($input);
    }

    public function generatePayroll(Period $period, Employee $employee): Payroll
    {
        dd($period);
    }
}
