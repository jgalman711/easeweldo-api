<?php

namespace App\Services;

class TaxService
{
    protected $baseTax = 2500;
    protected $compensationLevel = 33333;
    protected $taxRate = 0.25;

    public function compute(float $taxableAmount): float
    {
        return ($taxableAmount - $this->compensationLevel) * $this->taxRate;
    }

    public function getBaseTax(): float
    {
        return $this->baseTax;
    }

    public function getCompensationLevel(): float
    {
        return $this->compensationLevel;
    }

    public function getTaxRate(): float
    {
        return $this->taxRate;
    }
}

