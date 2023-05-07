<?php

namespace App\Services\Contributions;

use App\Models\Pagibig as PagibigModel;
use Illuminate\Support\Facades\Cache;

class PagIbig extends ContributionService
{
    protected $employeeShare;
    protected $employerShare;

    public function compute(float $salary): float
    {
        $pagibig = Cache::remember('pagibig', 3660, function () use ($salary) {
            return PagibigModel::where([
                ['min_compensation', '<=', $salary],
                ['max_compensation', '>=', $salary],
                ['status', PagibigModel::ACTIVE]
            ])->first();
        });

        $this->employeeShare = $pagibig->employee_contribution;
        $this->employerShare = $pagibig->employer_contribution;

        return $salary >= PagibigModel::MAX_SALARY
            ? PagibigModel::MAX_SALARY * $pagibig->employee_contribution_rate
            : $salary * $pagibig->employee_contribution_rate;
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
