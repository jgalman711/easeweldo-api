<?php

namespace App\Services\Contributions;

use App\Models\Tax;

class TaxCalculatorService
{
    public function compute(float $taxableAmount, string $type): float
    {
        $tax = Tax::where([
            'type' => $type,
            ['min_compensation', '<=', $taxableAmount],
            ['max_compensation', '>=', $taxableAmount],
        ])->first();

        if ($tax) {
            $overCompensation = $taxableAmount - $tax->min_compensation;

            return $overCompensation * $tax->over_compensation_level_rate + $tax->base_tax;
        }

        return 0;
    }
}
