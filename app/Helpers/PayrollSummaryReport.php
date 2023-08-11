<?php

namespace App\Helpers;

use Illuminate\Support\Collection;

class PayrollSummaryReport
{
    protected $periods;

    protected $attributes  = [
        'overtime_minutes' => 0,
        'overtime_pay' => 0,
        'late_minutes' => 0,
        'late_deductions' => 0,
        'absent_minutes' => 0,
        'absent_deductions' => 0,
        'undertime_minutes' => 0,
        'undertime_deductions' => 0,
        'leaves_pay' => 0,
        'total_allowances' => 0,
        'total_commissions' => 0,
        'total_other_compensations' => 0,
        'total_non_taxable_earnings' => 0,
        'regular_holiday_hours_worked' => 0,
        'regular_holiday_hours_worked_pay' => 0,
        'regular_holiday_hours' => 0,
        'regular_holiday_hours_pay' => 0,
        'special_holiday_hours_worked' => 0,
        'special_holiday_hours_worked_pay' => 0,
        'special_holiday_hours' => 0,
        'special_holiday_hours_pay' => 0,
        'sss_contributions' => 0,
        'philhealth_contributions' => 0,
        'pagibig_contributions' => 0,
        'total_contributions' => 0,
        'gross_income' => 0,
        'taxable_income' => 0,
        'withheld_tax' => 0,
        'net_income' => 0
    ];

    public function __construct(Collection $periods)
    {
        $this->periods = $periods;
        
    }

    public function get()
    {
        foreach ($this->periods as $period) {
            foreach ($period->payrolls as $payroll) {
                foreach ($this->attributes as $reportKey => $reportAttribute) {
                    $this->attributes[$reportKey] += $payroll->$reportKey;
                }
            }
        }
        return [
            'number_of_periods' => $this->periods->count(),
            ...$this->attributes
        ];
    }
}
