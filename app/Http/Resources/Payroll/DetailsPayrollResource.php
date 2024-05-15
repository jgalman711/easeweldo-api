<?php

namespace App\Http\Resources\Payroll;

use App\Enumerators\PayrollEnumerator;
use App\Helpers\NumberHelper;
use App\Http\Resources\BaseResource;
use App\Http\Resources\EmployeeResource;
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
            'id' => $this->id,
            'payroll_number' => $this->payroll_number,
            'status' => ucwords(str_replace('-', ' ', $this->status)),
            'employee_id' => $this->employee_id,
            'employee' => new EmployeeResource($this->employee),
            'pay_date' => $this->pay_date,
            'formatted_pay_date' => $this->formatDate($this->pay_date),
            'gross_income' => NumberHelper::format($this->gross_income),
            'net_income' => NumberHelper::format($this->net_income),
            'type' => $type,
            'period' => optional($this->period)->start_date.' to '.optional($this->period)->end_date,
            'total_contributions' => NumberHelper::format($this->total_contributions),
            'total_deductions' => NumberHelper::format($this->total_deductions),
            'overall_deductions' => NumberHelper::format($this->total_contributions + $this->total_deductions),
            'remarks' => $this->remarks,
            'earnings' => $this->formatEarnings(),
            'deductions' => $this->formatDeductions(),
            'summary' => $this->formatSummary(),
            'error' => $this->error,
            'download' => url("/api/payrolls/{$this->id}/download"),
        ];
    }

    private function formatEarnings(): array
    {
        $earnings = [
            [
                'label' => 'Regular Pay',
                'amount' => NumberHelper::format($this->basic_salary),
            ],
        ];

        if (isset($this->attendance_earnings['overtime'])) {
            foreach ($this->attendance_earnings['overtime'] as $overtime) {
                array_push($earnings, [
                    'label' => 'Overtime ('.$overtime['date'].')',
                    'rate' => $overtime['rate'],
                    'hours' => $overtime['hours'],
                    'amount' => NumberHelper::format($overtime['amount']),
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
                        'amount' => NumberHelper::format($deduction['amount'] * -1),
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
                        'rate' => $leave['rate'] ?? NumberHelper::format(1),
                        'hours' => $leave['hours'],
                        'amount' => NumberHelper::format($leave['amount'] ?? $leave['pay'] ?? 0),
                    ]);
                }
            }
        }

        if ($this->taxable_earnings && ! empty($this->taxable_earnings)) {
            foreach ($this->taxable_earnings as $taxableEarnings) {
                array_push($earnings, [
                    'label' => ucwords($taxableEarnings['name']),
                    'amount' => NumberHelper::format($taxableEarnings['amount']),
                ]);
            }
        }

        if ($this->non_taxable_earnings && ! empty($this->non_taxable_earnings)) {
            foreach ($this->non_taxable_earnings as $nonTaxableEarnings) {
                $label = ucwords($nonTaxableEarnings['name']).' (Non-taxable)';
                array_push($earnings, [
                    'label' => $label,
                    'amount' => NumberHelper::format($nonTaxableEarnings['amount']),
                ]);
            }
        }

        array_push($earnings, [
            'label' => 'Gross Income',
            'amount' => NumberHelper::format($this->gross_income),
        ]);

        return $earnings;
    }

    private function formatDeductions(): array
    {
        $deductions = [
            [
                'label' => 'SSS',
                'amount' => NumberHelper::format($this->sss_contributions * -1, 'negate'),
            ],
            [
                'label' => 'PhilHealth',
                'amount' => NumberHelper::format($this->philhealth_contributions * -1),
            ],
            [
                'label' => 'PagIbig',
                'amount' => NumberHelper::format($this->pagibig_contributions * -1),
            ],
            [
                'label' => 'Withheld Tax',
                'amount' => NumberHelper::format($this->withheld_tax * -1),
            ],
        ];

        if ($this->other_deductions && ! empty($this->other_deductions)) {
            foreach ($this->other_deductions as $otherDeductions) {
                $label = ucwords($otherDeductions['name']);
                array_push($deductions, [
                    'label' => $label,
                    'amount' => NumberHelper::format($otherDeductions['amount'] * -1),
                ]);
            }
        }

        array_push($deductions,  [
            'label' => 'Total Deductions',
            'amount' => NumberHelper::format($this->total_contributions + $this->total_deductions)
        ]);
        return $deductions;
    }

    private function formatSummary(): array
    {
        return [
            [
                'label' => 'Gross Income',
                'amount' => NumberHelper::format($this->gross_income),
            ],
            [
                'label' => 'Total Contributions',
                'amount' => NumberHelper::format($this->total_contributions),
            ],
            [
                'label' => 'Total Deductions',
                'amount' => NumberHelper::format($this->total_deductions),
            ],
            [
                'label' => 'Net Income',
                'amount' => NumberHelper::format($this->net_income),
            ],
        ];
    }
}
