<?php

namespace App\Services\Contributions;

use App\Models\PhilHealth;

class PhilHealthCalculatorService
{
    public function compute(float $salary): float
    {
        $philhealth = PhilHealth::where('status', PhilHealth::ACTIVE)->first();

        $contribution = $salary * $philhealth->contribution_rate;
        if ($salary <= 1) {
            $contribution = 0;
        } elseif ($contribution < $philhealth->min_contribution) {
            $contribution = $philhealth->min_contribution;
        } elseif ($contribution > $philhealth->max_contribution) {
            $contribution = $philhealth->max_contribution;
        }

        return $contribution;
    }
}
