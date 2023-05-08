<?php

namespace App\Services\Contributions;

use App\Models\PhilHealth as PhilHealthModel;
use Illuminate\Support\Facades\Cache;

class PhilHealth extends ContributionService
{
    public function compute(float $salary): float
    {
        Cache::forget('philhealth');
        $philhealth = Cache::remember('philhealth', 3660, function () {
            return PhilHealthModel::where('status', PhilHealthModel::ACTIVE)->first();
        });

        $contribution = $salary * $philhealth->contribution_rate;
        if ($contribution < $philhealth->min_contribution) {
            $contribution = $philhealth->min_contribution;
        } elseif ($contribution > $philhealth->max_contribution) {
            $contribution = $philhealth->max_contribution;
        }
        return $contribution;
    }
}
