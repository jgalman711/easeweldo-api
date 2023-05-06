<?php

namespace App\Services\Contributions;

abstract class ContributionService
{
    protected $minAmount;
    protected $maxAmount;
    protected $employeeShare;
    protected $employerShare;

    abstract public function compute(float $salary): float;
}
