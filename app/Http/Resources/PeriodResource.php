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
            "duration" => $this->formatDate($this->start_date) . " to " . $this->formatDate($this->end_date),
            "pay_day" => $this->formatDate($this->salary_date),
            "salary_date" => $this->salary_date,
            "status" => $this->status,
            "next_period" => $this->next_period,
            "previous_period" => $this->previous_period,
            'employees_count' => $this->employees_count,
            'employees_net_pay' => number_format($this->employees_net_pay, 2),
            'withheld_taxes' => number_format($this->withheld_taxes, 2),
            'total_contributions' => number_format($this->total_contributions, 2),
            'payroll_cost' => number_format($this->payroll_cost, 2),
            'base_pay' => number_format($this->payrolls->sum('base_pay'), 2),
            'leaves_pay' => $this->payrolls->sum('leaves_pay'),
            'holiday_pay' => $this->payrolls->sum('holiday_pay'),
            'overtime_pay' => $this->payrolls->sum('overtime_pay'),
            'payrolls' => $this->payrolls()->with('employee')->get(),
        ];
    }
}
