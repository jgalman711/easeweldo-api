<?php

namespace App\Services\Contributions;

use App\Models\SSS as SSSModel;
use Illuminate\Support\Facades\Cache;

class SSS extends ContributionService
{
    protected $employeeShare;
    protected $employerShare;

    public function compute(float $salary): float
    {
        $sss = Cache::remember('taxes', 3660, function () use ($salary) {
            return SSSModel::where([
                ['min_compensation', '<=', $salary],
                ['max_compensation', '>=', $salary]
            ])->first();
        });
        $this->employeeShare = $sss->employee_contribution;
        $this->employerShare = $sss->employer_contribution;
        return $sss->employee_contribution;
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
