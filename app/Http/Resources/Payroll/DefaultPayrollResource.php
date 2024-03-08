<?php

namespace App\Http\Resources\Payroll;

use App\Http\Resources\BaseResource;
use Illuminate\Http\Request;

class DefaultPayrollResource extends BaseResource
{
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "employee_full_name" => optional($this->employee)->full_name,
            "period_id" => $this->period_id,
            "type" => $this->type,
            "status" => $this->status,
            "description" => $this->description,
            "pay_date" => $this->pay_date ? $this->formatDate($this->pay_date) : null,
            "basic_salary" => number_format($this->basic_salary, 2),
            "attendance_earnings" => $this->attendance_earnings,
            "leaves" => $this->leaves,
            "taxable_earnings" => $this->taxable_earnings,
            "non_taxable_earnings" => $this->non_taxable_earnings,
            "holidays" => $this->holidays,
            "holidays_worked" => $this->holidays,
            "sss_contributions" => $this->sss_contributions,
            "philhealth_contributions" => $this->philhealth_contributions,
            "pagibig_contributions" => $this->pagibig_contributions,
            "withheld_tax" => $this->withheld_tax,
            "remarks" => $this->remarks,
            "total_non_taxable_earnings" => number_format($this->total_non_taxable_earnings, 2),
            "total_taxable_earnings" => number_format($this->total_taxable_earnings, 2),
            "total_contributions" => number_format($this->total_contributions, 2),
            "total_deductions" => number_format($this->total_deductions, 2),
            "total_deductions_contributions" => number_format($this->total_deductions + $this->total_contributions, 2),
            "gross_income" => number_format($this->gross_income, 2),
            "taxable_income" => number_format($this->taxable_income, 2),
            "net_taxable_income" => number_format($this->net_taxable_income, 2),
            "net_income" => number_format($this->net_income, 2),
            "period_duration" => $this->formatCompactDate(
                optional($this->period)->start_date) . " - " . $this->formatDate(optional($this->period)->end_date
            )
        ];
    }
}
