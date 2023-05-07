<?php

namespace App\Services\Contributions;

use App\Models\PhilHealth as PhilHealthModel;

class PhilHealth extends ContributionService
{
    public function compute(float $salary): float
    {
        $philhealth = PhilHealthModel::where('status', PhilHealthModel::ACTIVE)->first();

        $contribution = $salary * $philhealth->contribution_rate;
        if ($contribution < $philhealth->min_contribution) {
            $contribution = $philhealth->min_contribution;
        } elseif ($contribution > $philhealth->max_contribution) {
            $contribution = $philhealth->max_contribution;
        }
        return $contribution;
    }
}
