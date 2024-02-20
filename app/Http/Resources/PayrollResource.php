<?php

namespace App\Http\Resources;

use DateTime;
use Illuminate\Http\Request;

class PayrollResource extends BaseResource
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
            "hours_worked" => $this->hours_worked,
            "expected_hours_worked" => $this->expected_hours_worked,
            "overtime_minutes" => $this->overtime_minutes,
            "overtime_pay" => $this->overtime_pay,
            "late_minutes" => $this->late_minutes,
            "late_deductions" => $this->late_deductions,
            "absent_minutes" => $this->absent_minutes,
            "absent_deductions" => $this->absent_deductions,
            "undertime_minutes" => $this->undertime_minutes,
            "undertime_deductions" => $this->undertime_deductions,
            "leaves" => $this->leaves,
            "leaves_pay" => $this->leaves_pay,
            "taxable_earnings" => $this->taxable_earnings,
            "non_taxable_earnings" => $this->non_taxable_earnings,
            "sss_contributions" => $this->sss_contributions,
            "philhealth_contributions" => $this->philhealth_contributions,
            "pagibig_contributions" => $this->pagibig_contributions,
            "withheld_tax" => $this->withheld_tax,
            "remarks" => $this->remarks,
            "base_pay" => number_format($this->base_pay, 2),
            "absent_hours" => $this->absent_hours,
            "late_hours" => $this->late_hours,
            "overtime_hours" => $this->overtime_hours,
            "undertime_hours" => $this->undertime_hours,
            "regular_holiday_hours_worked" => $this->regular_holiday_hours_worked,
            "regular_holiday_hours_worked_pay" => $this->regular_holiday_hours_worked_pay,
            "regular_holiday_hours" => $this->regular_holiday_hours,
            "regular_holiday_hours_pay" => $this->regular_holiday_hours_pay,
            "special_holiday_hours_worked" => $this->special_holiday_hours_worked,
            "special_holiday_hours_worked_pay" => $this->special_holiday_hours_worked_pay,
            "special_holiday_hours" => $this->special_holiday_hours,
            "special_holiday_hours_pay" => $this->special_holiday_hours_pay,
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
