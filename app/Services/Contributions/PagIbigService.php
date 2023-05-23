<?php

namespace App\Services\Contributions;

use App\Models\Pagibig;

class PagIbigService extends ContributionService
{
    protected $employeeShare;
    protected $employerShare;

    public function compute(float $salary): float
    {
        $pagibig = Pagibig::where([
            ['min_compensation', '<=', $salary],
            ['max_compensation', '>=', $salary],
            ['status', Pagibig::ACTIVE]
        ])->first();

        if ($pagibig) {
            if ($salary >= Pagibig::MAX_SALARY) {
                $this->employeeShare = Pagibig::MAX_SALARY * $pagibig->employee_contribution_rate;
                $this->employerShare = Pagibig::MAX_SALARY * $pagibig->employer_contribution_rate;
            } else {
                $this->employeeShare = $salary * $pagibig->employee_contribution_rate;
                $this->employerShare = $salary * $pagibig->employer_contribution_rate;
            }
            return $this->employeeShare;
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
