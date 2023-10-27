<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class PeriodResource extends BaseResource
{
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "company_id" => $this->company_id,
            "company_period_id" => $this->company_period_id,
            "description" => $this->description,
            "type" => $this->type,
            "start_date" => $this->start_date,
            "end_date" => $this->end_date,
            "salary_date" => $this->salary_date,
            "status" => $this->status,
            "next_period" => $this->next_period,
            "previous_period" => $this->previous_period,
            'employees_count' => $this->employees_count,
            'employees_net_pay' => $this->employees_net_pay,
            'withheld_taxes' => $this->withheld_taxes,
            'total_contributions' => $this->total_contributions,
            'payroll_cost' => $this->payroll_cost,
            'payrolls' => $this->payrolls()->with('employee')->get(),
        ];
    }
}
