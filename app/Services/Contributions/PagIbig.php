<?php

namespace App\Services\Contributions;

class PagIbig extends ContributionService
{
    protected $minAmount = 100;
    protected $maxAmount = 5000;
    protected $employeeShare = 100;
    protected $employerShare = 0;

    public function compute(float $salary): float
    {
        return 100.00;
    }
}
