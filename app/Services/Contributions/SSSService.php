<?php

namespace App\Services\Contributions;

use App\Models\SSS;
use Illuminate\Support\Facades\Cache;

class SSSService extends ContributionService
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
