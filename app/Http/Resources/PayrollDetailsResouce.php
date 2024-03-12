<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class PayrollDetailsResouce extends BaseResource
{
    /**
     * TODO: remove this. Already moved to PayrollResource.php
     */
    public function toArray(Request $request): array
    {
        return [
            'status' => ucwords(str_replace("-", " ", $this->status)),
            'pay_date' => $this->pay_date,
            'net_income' => number_format($this->net_income, 2),
            'type' => optional($this->period)->type,
            'period' => optional($this->period)->start_date . " to " . optional($this->period)->end_date,
            'total_deductions' => number_format($this->total_contributions, 2),
            'earnings' => $this->formatEarnings(),
            'deductions' => $this->formatDeductions(),
            'other_deductions' => $this->formatOtherDeductions(),
            'summary' => $this->formatSummary()
        ];
    }

    private function formatEarnings(): array
    {
        $earnings = [
            [
                'label' => 'Regular Pay',
                'rate' => 1.0,
                'hours' => $this->hours_worked,
                'amount' => number_format($this->basic_salary, 2)
            ]
        ];
        if ($this->overtime_minutes > 0) {
            array_push($earnings, [
                'label' => 'Overtime',
                'rate' => 1.3,
                'hours' => $this->overtime_minutes / 60,
                'amount' => number_format($this->overtime_pay, 2)
            ]);
        }

        if ($this->regular_holiday_hours > 0) {
            array_push($earnings, [
                'label' => 'Regular Holiday',
                'rate' => 1.0,
                'hours' => $this->regular_holiday_hours,
                'amount' => number_format($this->regular_holiday_hours_pay, 2)
            ]);
        }

        if ($this->regular_holiday_hours_worked > 0) {
            array_push($earnings, [
                'label' => 'Regular Holiday Worked',
                'rate' => 1.0,
                'hours' => $this->regular_holiday_hours_worked,
                'amount' => number_format($this->regular_holiday_hours_worked_pay, 2)
            ]);
        }

        if ($this->special_holiday_hours > 0) {
            array_push($earnings, [
                'label' => 'Special Holiday',
                'rate' => 1.0,
                'hours' => $this->special_holiday_hours,
                'amount' => number_format($this->special_holiday_hours_pay, 2)
            ]);
        }

        if ($this->special_holiday_hours_worked > 0) {
            array_push($earnings, [
                'label' => 'Special Holiday Worked',
                'rate' => 1.0,
                'hours' => $this->special_holiday_hours_worked,
                'amount' => number_format($this->special_holiday_hours_worked_pay, 2)
            ]);
        }

        if (!empty($this->leaves)) {
            foreach ($this->leaves as $leave) {
                array_push($earnings, [
                    'label' => ucfirst($leave['type']) . " Leave ({$leave['date']})",
                    'rate' => 1.0,
                    'hours' => $leave['hours'],
                    'amount' => number_format($leave['amount'], 2)
                ]);
            }
        }

        if ($this->taxable_earnings && !empty($this->taxable_earnings)) {
            foreach ($this->taxable_earnings as $taxableEarnings) {
                array_push($earnings, [
                    'label' => ucwords($taxableEarnings['name']),
                    'amount' => number_format($taxableEarnings['amount'], 2)
                ]);
            }
        }

        if ($this->absent_minutes > 0 && $this->absent_deductions > 0) {
            array_push($earnings, [
                'label' => 'Absent Deductions',
                'rate' => 1.0,
                'hours' => $this->absent_minutes / 60,
                'amount' => "-" . number_format($this->absent_deductions, 2)
            ]);
        }

        if ($this->late_minutes > 0 && $this->late_deductions > 0) {
            array_push($earnings, [
                'label' => 'Late Deductions',
                'rate' => 1.0,
                'hours' => $this->late_minutes / 60,
                'amount' => "-" . number_format($this->late_deductions, 2)
            ]);
        }

        if ($this->non_taxable_earnings && !empty($this->non_taxable_earnings)) {
            foreach ($this->non_taxable_earnings as $nonTaxableEarnings) {
                array_push($earnings, [
                    'label' => ucwords($nonTaxableEarnings['name']) . " (Non-taxable {$nonTaxableEarnings['type']})",
                    'amount' => number_format($nonTaxableEarnings['amount'], 2)
                ]);
            }
        }

        array_push($earnings, [
            'label' => 'Gross Income',
            'amount' => number_format($this->gross_income, 2)
        ]);

        return $earnings;
    }

    private function formatDeductions(): array
    {
        return [
            [
                'label' => 'SSS Contributions',
                'amount' => $this->sss_contributions
            ],
            [
                'label' => 'PhilHealth Contributions',
                'amount' => $this->philhealth_contributions
            ],
            [
                'label' => 'PagIbig Contributions',
                'amount' => $this->pagibig_contributions
            ],
            [
                'label' => 'Withheld Tax',
                'amount' => $this->withheld_tax
            ]
        ];
    }

    private function formatOtherDeductions(): array
    {
        $otherDeductions = [];
        if ($this->other_deductions && !empty($this->other_deductions)) {
            foreach ($this->other_deductions as $deduction) {
                array_push($otherDeductions, [
                    'label' => ucwords($deduction['name']),
                    'amount' => number_format($deduction['deduction'], 2)
                ]);
            }
        }
        return $otherDeductions;
    }

    private function formatSummary(): array
    {
        return [
            [
                'label' => "Gross Income",
                'amount' => number_format($this->gross_income, 2)
            ],
            [
                'label' => "Total Contributions",
                'amount' => number_format($this->total_contributions, 2)
            ],
            [
                'label' => "Other Deductions",
                'amount' => number_format($this->total_other_deductions, 2)
            ],
            [
                'label' => "Net Income",
                'amount' => number_format($this->net_income, 2)
            ],
        ];
    }
}
