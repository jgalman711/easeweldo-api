<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class PeriodResource extends BaseResource
{
    public function toArray(Request $request): array
    {
        return [
            ...parent::toArray($request),
            'employees_count' => $this->employees_count,
            'employees_net_pay' => $this->employees_net_pay,
            'withheld_taxes' => $this->withheld_taxes,
            'total_contributions' => $this->total_contributions,
            'payroll_cost' => $this->payroll_cost,
        ];
    }
}
