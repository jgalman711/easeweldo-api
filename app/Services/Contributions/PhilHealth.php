<?php

namespace App\Services\Contributions;

class PhilHealth extends ContributionService
{
    protected $minAmount = 100;
    protected $maxAmount = 5000;
    protected $employeeShare = 0;
    protected $employerShare = 100;

    public function compute(float  $salary): float
    {
        return 1237.50;
    }
}
