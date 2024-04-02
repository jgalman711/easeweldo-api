<?php

namespace App\Http\Resources\Payroll;

use App\Enumerators\PayrollEnumerator;
use App\Http\Resources\BaseResource;
use App\Models\Holiday;
use Illuminate\Http\Request;

class DetailsPayrollResource extends BaseResource
{
    public function toArray(Request $request): array
    {
        if (optional($this->period)->type == PayrollEnumerator::TYPE_NTH_MONTH_PAY) {
            $type = 'Annual Extra';
        } else {
            $type = ucfirst(optional($this->period)->type);
        }

        return [
            'status' => ucwords(str_replace('-', ' ', $this->status)),
            'employee_id' => $this->employee_id,
            'pay_date' => $this->pay_date,
            'net_income' => number_format($this->net_income, 2),
            'type' => $type,
            'period' => optional($this->period)->start_date.' to '.optional($this->period)->end_date,
            'total_contributions' => number_format($this->total_contributions, 2),
            'total_deductions' => number_format($this->total_deductions, 2),
            'earnings' => $this->formatEarnings(),
            'deductions' => $this->formatDeductions(),
            'summary' => $this->formatSummary(),
        ];
    }

    private function formatEarnings(): array
    {
        $earnings = [
            [
                'label' => 'Regular Pay',
                'amount' => number_format($this->basic_salary, 2),
            ],
        ];

        if (isset($this->attendance_earnings['overtime'])) {
            foreach ($this->attendance_earnings['overtime'] as $overtime) {
                array_push($earnings, [
                    'label' => 'Overtime ('.$overtime['date'].')',
                    'rate' => $overtime['rate'],
                    'hours' => $overtime['hours'],
                    'amount' => number_format($overtime['amount'], 2),
                ]);
            }
        }

        $attendanceDeductionTypes = [
            'absent' => 'Absent',
            'late' => 'Late',
            'undertime', 'Undertime',
        ];
        foreach ($attendanceDeductionTypes as $type => $label) {
            if (isset($this->attendance_earnings[$type])) {
                foreach ($this->attendance_earnings[$type] as $deduction) {
                    array_push($earnings, [
                        'label' => "$label (".$deduction['date'].')',
                        'rate' => $deduction['rate'],
                        'hours' => $deduction['hours'],
                        'amount' => number_format($deduction['amount'] * -1, 2),
                    ]);
                }
            }
        }

        foreach (Holiday::HOLIDAY_TYPES as $type) {
            if (isset($this->holidays[$type])) {
                $label = ucfirst($type).' Holiday';
                foreach ($this->holidays[$type] as $holiday) {
                    $formattedLabel = $label . "(" . $holiday['date'] ?? '' . ")";
                    array_push($earnings, [
                        'label' => $formattedLabel,
                        'rate' => $holiday['rate'],
                        'hours' => $holiday['hours'],
                        'amount' => $holiday['amount'] ?? $holiday['pay'] ?? 0,
                    ]);
                }
            }
        }

        foreach (Holiday::HOLIDAY_TYPES as $type) {
            if (isset($this->holidays_worked[$type])) {
                $label = ucfirst($type).' Holiday Worked';
                foreach ($this->holidays_worked[$type] as $holiday) {
                    array_push($earnings, [
                        'label' => "$label (".$holiday['date'].')',
                        'rate' => $holiday['rate'],
                        'hours' => $holiday['hours'],
                        'amount' => $holiday['amount'] ?? $holiday['pay'] ?? 0,
                    ]);
                }
            }
        }

        if (! empty($this->leaves)) {
            foreach ($this->leaves as $type => $typeLeaves) {
                foreach ($typeLeaves as $leave) {
                    array_push($earnings, [
                        'label' => ucwords(str_replace('_', ' ', $type))." ({$leave['date']})",
                        'rate' => $leave['rate'],
                        'hours' => $leave['hours'],
                        'amount' => number_format($leave['amount'] ?? $leave['pay'] ?? 0, 2),
                    ]);
                }
            }
        }

        if ($this->taxable_earnings && ! empty($this->taxable_earnings)) {
            foreach ($this->taxable_earnings as $taxableEarnings) {
                array_push($earnings, [
                    'label' => ucwords($taxableEarnings['name']),
                    'amount' => number_format($taxableEarnings['amount'], 2),
                ]);
            }
        }

        if ($this->non_taxable_earnings && ! empty($this->non_taxable_earnings)) {
            foreach ($this->non_taxable_earnings as $nonTaxableEarnings) {
                $label = ucwords($nonTaxableEarnings['name']).' (Non-taxable)';
                array_push($earnings, [
                    'label' => $label,
                    'amount' => number_format($nonTaxableEarnings['amount'], 2),
                ]);
            }
        }

        if ($this->other_deductions && ! empty($this->other_deductions)) {
            foreach ($this->other_deductions as $otherDeductions) {
                $label = ucwords($otherDeductions['name']);
                array_push($earnings, [
                    'label' => $label,
                    'amount' => number_format($otherDeductions['amount'] * -1, 2),
                ]);
            }
        }

        array_push($earnings, [
            'label' => 'Gross Income',
            'amount' => number_format($this->gross_income, 2),
        ]);

        return $earnings;
    }

    private function formatDeductions(): array
    {
        return [
            [
                'label' => 'SSS Contributions',
                'amount' => $this->sss_contributions,
            ],
            [
                'label' => 'PhilHealth Contributions',
                'amount' => $this->philhealth_contributions,
            ],
            [
                'label' => 'PagIbig Contributions',
                'amount' => $this->pagibig_contributions,
            ],
            [
                'label' => 'Withheld Tax',
                'amount' => $this->withheld_tax,
            ],
        ];
    }

    private function formatSummary(): array
    {
        return [
            [
                'label' => 'Gross Income',
                'amount' => number_format($this->gross_income, 2),
            ],
            [
                'label' => 'Total Contributions',
                'amount' => number_format($this->total_contributions, 2),
            ],
            [
                'label' => 'Total Deductions',
                'amount' => number_format($this->total_deductions, 2),
            ],
            [
                'label' => 'Net Income',
                'amount' => number_format($this->net_income, 2),
            ],
        ];
    }
}
