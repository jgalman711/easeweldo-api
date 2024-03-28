<?php

namespace App\Services;

use App\Models\Tax;

class TaxService
{
    protected $baseTax;

    protected $compensationLevel;

    protected $taxRate;

    public function compute(float $taxableAmount, string $type): float
    {
        $tax = Tax::where([
            'type' => $type,
            ['min_compensation', '<=', $taxableAmount],
            ['max_compensation', '>=', $taxableAmount],
        ])->first();

        if ($tax) {
            $this->baseTax = $tax->base_tax;
            $this->compensationLevel = $tax->min_compensation;
            $this->taxRate = $tax->over_compensation_level_rate;
            $overCompensation = $taxableAmount - $tax->min_compensation;

            return $overCompensation * $tax->over_compensation_level_rate + $tax->base_tax;
        }

        return 0;
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
