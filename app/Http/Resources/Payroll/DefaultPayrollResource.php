<?php

namespace App\Http\Resources\Payroll;

use App\Http\Resources\BaseResource;
use Exception;
use Illuminate\Http\Request;

class DefaultPayrollResource extends BaseResource
{
    public function toArray(Request $request): array
    {
        try {
            return [
                "id" => $this->id,
                "employee_full_name" => optional($this->employee)->full_name,
                "period_id" => $this->period_id,
                "type" => $this->type,
                "status" => $this->status,
                "description" => $this->description,
                "pay_date" => $this->pay_date ? $this->formatDate($this->pay_date) : null,
                "basic_salary" => round($this->basic_salary, 2),
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
                "total_attendance_earnings" => $this->total_attendance_earnings,
                "total_attendance_deductions" => $this->total_attendance_deductions,
                "total_non_taxable_earnings" => round($this->total_non_taxable_earnings, 2),
                "total_other_deductions" => round($this->total_other_deductions, 2),
                "total_taxable_earnings" => round($this->total_taxable_earnings, 2),
                "total_contributions" => round($this->total_contributions, 2),
                "total_deductions_contributions" => round($this->total_other_deductions + $this->total_contributions, 2),
                "gross_income" => round($this->gross_income, 2),
                "taxable_income" => round($this->taxable_income, 2),
                "net_taxable_income" => round($this->net_taxable_income, 2),
                "net_income" => round($this->net_income, 2),
                "period_duration" => $this->formatCompactDate(
                    optional($this->period)->start_date) . " - " . $this->formatDate(optional($this->period)->end_date
                )
            ];
        } catch (Exception) {
            return ['error' => $this->resource];
        }
    }
}
