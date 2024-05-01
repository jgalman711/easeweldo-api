<?php

namespace App\Http\Resources\Payroll;

use App\Helpers\NumberHelper;
use App\Http\Resources\BaseResource;
use Exception;
use Illuminate\Http\Request;

class DefaultPayrollResource extends BaseResource
{
    public function toArray(Request $request): array
    {
        try {
            return [
                'id' => $this->id,
                'employee_full_name' => optional($this->employee)->full_name,
                'period_id' => $this->period_id,
                'type' => $this->type,
                'status' => $this->status,
                'description' => $this->description,
                'pay_date' => $this->pay_date ? $this->formatDate($this->pay_date) : null,
                'basic_salary' => NumberHelper::format($this->basic_salary),
                'attendance_earnings' => $this->attendance_earnings,
                'leaves' => $this->leaves,
                'taxable_earnings' => $this->taxable_earnings,
                'non_taxable_earnings' => $this->non_taxable_earnings,
                'holidays' => $this->holidays,
                'holidays_worked' => $this->holidays,
                'sss_contributions' => $this->sss_contributions,
                'philhealth_contributions' => $this->philhealth_contributions,
                'pagibig_contributions' => $this->pagibig_contributions,
                'withheld_tax' => $this->withheld_tax,
                'remarks' => $this->remarks,
                'total_attendance_earnings' => $this->total_attendance_earnings,
                'total_attendance_deductions' => $this->total_attendance_deductions,
                'total_non_taxable_earnings' => NumberHelper::format($this->total_non_taxable_earnings),
                'total_other_deductions' => NumberHelper::format($this->total_other_deductions),
                'total_taxable_earnings' => NumberHelper::format($this->total_taxable_earnings),
                'total_contributions' => NumberHelper::format($this->total_contributions),
                'total_deductions_contributions' => NumberHelper::format($this->total_other_deductions + $this->total_contributions),
                'gross_income' => NumberHelper::format($this->gross_income),
                'taxable_income' => NumberHelper::format($this->taxable_income),
                'net_income' => NumberHelper::format($this->net_income),
                'period_duration' => $this->formatCompactDate(
                    optional($this->period)->start_date).' - '.$this->formatDate(optional($this->period)->end_date
                    ),
            ];
        } catch (Exception) {
            return ['error' => $this->resource];
        }
    }
}
