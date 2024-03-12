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
            "type" => ucfirst($this->type),
            "subtype" => ucfirst($this->subtype),
            "start_date" => $this->start_date,
            "end_date" => $this->end_date,
            "duration" => $this->getDuration(),
            "pay_day" => $this->formatDate($this->salary_date),
            "salary_date" => $this->salary_date,
            "status" => $this->status,
            'employees_count' => $this->employees_count,
            'employees_net_pay' => number_format($this->employees_net_pay, 2),
            'withheld_taxes' => number_format($this->withheld_taxes, 2),
            'total_contributions' => number_format($this->total_contributions, 2),
            'payroll_cost' => number_format($this->payroll_cost, 2),
            'base_pay' => number_format($this->payrolls->sum('basic_salary'), 2),
            'leaves_pay' => number_format($this->payrolls->sum('leaves_pay'), 2),
            'holiday_pay' => number_format($this->payrolls->sum('holiday_pay'), 2),
            'overtime_pay' => number_format($this->payrolls->sum('overtime_pay'), 2),
        ];
    }

    private function getDuration(): ?string
    {
        if ($this->start_date && $this->end_date) {
            return $this->formatCompactDate($this->start_date) . " - " . $this->formatDate($this->end_date);
        }
        return null;
    }
}
