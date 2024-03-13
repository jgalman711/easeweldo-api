<?php

namespace App\Services\Payroll;

use App\Models\Holiday;
use App\Models\Leave;
use App\Models\Payroll;
use Barryvdh\DomPDF\Facade\Pdf;

class UpdatePayrollService
{
    protected $payroll;
    protected $employee;
    protected $employeeHourlyRate;

    public function update(Payroll $payroll, array $input): Payroll
    {
        $this->init($payroll, $input);
        $this->calculateEarnings($input);
        $this->calculateOvertimeEarnings($input);
        $this->calculateHolidaysEarnings($input);
        $this->calculateLeaves($input);
        $this->calculateTaxesAndContributions($input);
        $this->calculateAttendanceDeductions($input);
        $payroll->save();
        return $payroll;
    }

    public function download(Payroll $payroll): string
    {
        $pdf = Pdf::loadView('pdf.payslip', [
            'payroll' => $payroll,
            'period' => $payroll->period,
            'employee' => $payroll->employee,
            'company' => optional($payroll->employee)->company
        ]);
        return base64_encode($pdf->output());
    }

    protected function init(Payroll $payroll, array $input)
    {
        $this->payroll = $payroll;
        $this->employee = $payroll->employee;
        $salaryComputation = $this->employee->salaryComputation;
        $this->employeeHourlyRate = $salaryComputation->hourly_rate;
        $this->payroll->basic_salary = $input['basicSalary'];
        $this->payroll->pay_date = $input['payDate'];
        $this->payroll->status = $input['status'];
        if (isset($input['description'])) {
            $payroll->description = $input['description'];
        }
    }

    private function calculateEarnings($input)
    {
        if (isset($input['otherEarnings'])) {
            if (isset($input['otherEarnings']['taxableEarnings'])) {
                $this->payroll->taxable_earnings = $input['otherEarnings']['taxableEarnings'];
            }
            if (isset($input['otherEarnings']['nonTaxableEarnings'])) {
                $this->payroll->non_taxable_earnings = $input['otherEarnings']['nonTaxableEarnings'];
            }
        }
    }

    private function calculateTaxesAndContributions(array $input): void
    {
        if (isset($input['taxesAndContributions'])) {
            if (isset($input['taxesAndContributions']['sssContributions'])) {
                $this->payroll->sss_contributions = $input['taxesAndContributions']['sssContributions'];
            }
            if (isset($input['taxesAndContributions']['philhealthContributions'])) {
                $this->payroll->philhealth_contributions = $input['taxesAndContributions']['philhealthContributions'];
            }
            if (isset($input['taxesAndContributions']['pagibigContributions'])) {
                $this->payroll->pagibig_contributions = $input['taxesAndContributions']['pagibigContributions'];
            }
            if (isset($input['taxesAndContributions']['withheldTax'])) {
                $this->payroll->withheld_tax = $input['taxesAndContributions']['withheldTax'];
            }
        }
    }

    private function calculateAttendanceDeductions(array $input): void
    {
        $keys = ['late', 'absent', 'undertime'];
        $attendanceDeductions = [];
        foreach ($keys as $key) {
            if (isset($input['deductions']) && isset($input['deductions'][$key])) {
                $deductions = $input['deductions'][$key];
                foreach ($deductions as $deduction) {
                    $attendanceDeductions[$key][] = [
                        'date' => $deduction['date'],
                        'rate' => $deduction['rate'],
                        'hours' => $deduction['hours'],
                        'amount' => $deduction['rate'] * $deduction['hours'] * $this->employeeHourlyRate * -1
                    ];
                }
            }
        }
        $this->payroll->attendance_earnings = array_merge($this->payroll->attendance_earnings, $attendanceDeductions);
    }

    private function calculateOvertimeEarnings(array $input): void
    {
        $attendanceEarnings = null;
        if (isset($input['regularEarnings']) && isset($input['regularEarnings']['overtime'])) {
            $overtimes = $input['regularEarnings']['overtime'];
            foreach ($overtimes as $overtime) {
                $attendanceEarnings['overtime'][] = [
                    'date' => $overtime['date'],
                    'rate' => $overtime['rate'],
                    'hours' => $overtime['hours'],
                    'amount' => $overtime['rate'] * $overtime['hours'] * $this->employeeHourlyRate
                ];
            }
        }
        $this->payroll->attendance_earnings = $attendanceEarnings;
    }

    private function calculateLeaves(array $input): array
    {
        $leaveEarnings = null;
        if (isset($input['regularEarnings']) && isset($input['regularEarnings']['sickLeave'])) {
            $leaves = $input['regularEarnings']['sickLeave'];
            foreach ($leaves as $leave) {
                $leaveEarnings[Leave::TYPE_SICK_LEAVE][] = [
                    'date' => $leave['date'],
                    'rate' => $leave['rate'],
                    'hours' => $leave['hours'],
                    'amount' => $leave['rate'] * $leave['hours'] * $this->employeeHourlyRate
                ];
            }
        }

        if (isset($input['regularEarnings']) && isset($input['regularEarnings']['vacationLeave'])) {
            $leaves = $input['regularEarnings']['vacationLeave'];
            foreach ($leaves as $leave) {
                $leaveEarnings[Leave::TYPE_VACATION_LEAVE][] = [
                    'date' => $leave['date'],
                    'rate' => $leave['rate'],
                    'hours' => $leave['hours'],
                    'amount' => $leave['rate'] * $leave['hours'] * $this->employeeHourlyRate
                ];
            }
        }
        return $this->payroll->leaves = $leaveEarnings;
    }

    private function calculateHolidaysEarnings(array $input): void
    {
        $holidayEarnings = null;
        $holidayWorkedEarnings = null;
        foreach (Holiday::HOLIDAY_TYPES as $type) {
            $holidayKey = "{$type}Holiday";
            if (isset($input['regularEarnings']) && isset($input['regularEarnings'][$holidayKey])) {
                $holidays = $input['regularEarnings'][$holidayKey];
                $holidayEarnings[$type] = $this->parseHoliday($holidays);
            }

            $holidayKey = "{$type}HolidayWorked";
            if (isset($input['regularEarnings']) && isset($input['regularEarnings'][$holidayKey])) {
                $holidays = $input['regularEarnings'][$holidayKey];
                $holidayWorkedEarnings[$type] = $this->parseHoliday($holidays);
            }
        }
        $this->payroll->holidays = $holidayEarnings;
        $this->payroll->holidays_worked = $holidayWorkedEarnings;
    }

    private function parseHoliday($holidays)
    {
        $holidayEarnings = null;
        foreach ($holidays as $holiday) {
            $holidayEarnings[] = [
                'date' => $holiday['date'],
                'rate' => $holiday['rate'],
                'hours' => $holiday['hours'],
                'amount' => $holiday['rate'] * $holiday['hours'] * $this->employeeHourlyRate
            ];
        }
        return $holidayEarnings;
    }
}
