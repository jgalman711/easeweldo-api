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

        if ($salary >= PagibigModel::MAX_SALARY) {
            $this->employeeShare = PagibigModel::MAX_SALARY * $pagibig->employee_contribution_rate;
            $this->employerShare = PagibigModel::MAX_SALARY * $pagibig->employer_contribution_rate;
        } else {
            $this->employeeShare = $salary * $pagibig->employee_contribution_rate;
            $this->employerShare = $salary * $pagibig->employer_contribution_rate;
        }
        return $this->employeeShare;
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
