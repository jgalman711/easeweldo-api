<?php

namespace App\Services\Contributions;

class SSS extends ContributionService
{
    protected $minAmount;
    protected $maxAmount;
    protected $employeeShare = 60;
    protected $employerShare = 40;

    public function compute(float $salary): float
    {
        return 1350.00;
    }
}
