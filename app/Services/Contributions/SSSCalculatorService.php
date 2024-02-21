<?php

namespace App\Services\Contributions;

use App\Models\SSS;

class SSSCalculatorService
{
    protected $employeeShare;
    protected $employerShare;

    public function compute(float $salary): float
    {
        $sss = SSS::where([
            ['min_compensation', '<=', $salary],
            ['max_compensation', '>=', $salary]
        ])->first();
        if ($sss) {
            $this->employeeShare = $sss->employee_contribution;
            $this->employerShare = $sss->employer_contribution;
            return $sss->employee_contribution;
        }
        return 0;
    }

    public function getEmployerShare(): float
    {
        return $this->employerShare;
    }

    public function getEmployeeShare(): float
    {
        return $this->employeeShare;
    }
}
