<?php

namespace App\Services\Payroll;

use App\Models\Holiday;
use App\Models\Leave;
use App\Models\Payroll;
use Barryvdh\DomPDF\Facade\Pdf;

class PayrollService
{
    public function update(Payroll $payroll, array $input): Payroll
    {
        $employee = $payroll->employee;
        $salaryComputation = $employee->salaryComputation;
        $employeeHourlyRate = $salaryComputation->hourly_rate;
        $payroll->basic_salary = $input['basicSalary'];
        $payroll->pay_date = $input['payDate'];
        $payroll->status = $input['status'];
        $payroll->description = $input['description'] ?? null;
        list($holidayEarnings, $holidayWorkedEarnings) = $this->getHolidaysEarnings($input, $employeeHourlyRate);
        $attendanceEarnings = $this->getOvertimeEarnings($input, $employeeHourlyRate);
        $payroll->leaves = $this->getLeaves($input, $employeeHourlyRate);
        $payroll->holidays = $holidayEarnings;
        $payroll->holidays_worked = $holidayWorkedEarnings;
        $payroll->taxable_earnings = $input['otherEarnings']['taxableEarnings'];
        $payroll->non_taxable_earnings = $input['otherEarnings']['nonTaxableEarnings'];
        $deduction = $this->getAttendanceDeductions($input, $employeeHourlyRate);
        $payroll->attendance_earnings = array_merge($attendanceEarnings, $deduction);
        $this->getTaxesAndContributions($payroll, $input);
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

    private function getTaxesAndContributions(Payroll &$payroll, array $input)
    {
        if (isset($input['taxesAndContributions']) && isset($input['taxesAndContributions']['sssContributions'])) {
            $payroll->sss_contributions = $input['taxesAndContributions']['sssContributions'];
        }
        if (isset($input['taxesAndContributions']) && isset($input['taxesAndContributions']['philhealthContributions'])) {
            $payroll->philhealth_contributions = $input['taxesAndContributions']['philhealthContributions'];
            
        }
        if (isset($input['taxesAndContributions']) && isset($input['taxesAndContributions']['pagibigContributions'])) {
            $payroll->pagibig_contributions = $input['taxesAndContributions']['pagibigContributions'];
        }
        if (isset($input['taxesAndContributions']) && isset($input['taxesAndContributions']['withheldTax'])) {
            $payroll->withheld_tax = $input['taxesAndContributions']['withheldTax'];
        }
    }

    private function getAttendanceDeductions(array $input, $employeeHourlyRate): ?array
    {
        $keys = ['late', 'absent', 'undertime'];
        $attendanceDeductions = null;
        foreach ($keys as $key) {
            if (isset($input['deductions']) && isset($input['deductions'][$key])) {
                $deductions = $input['deductions'][$key];
                foreach ($deductions as $deduction) {
                    $attendanceDeductions[$key][] = [
                        'date' => $deduction['date'],
                        'rate' => $deduction['rate'],
                        'hours' => $deduction['hours'],
                        'pay' => $deduction['rate'] * $deduction['hours'] * $employeeHourlyRate * -1
                    ];
                }
            }
        }
        return $attendanceDeductions;
    }

    private function getOvertimeEarnings(array $input, $employeeHourlyRate): array
    {
        $attendanceEarnings = null;
        if (isset($input['regularEarnings']) && isset($input['regularEarnings']['overtime'])) {
            $overtimes = $input['regularEarnings']['overtime'];
            foreach ($overtimes as $overtime) {
                $attendanceEarnings['overtime'][] = [
                    'date' => $overtime['date'],
                    'rate' => $overtime['rate'],
                    'hours' => $overtime['hours'],
                    'pay' => $overtime['rate'] * $overtime['hours'] * $employeeHourlyRate
                ];
            }
        }
        return $attendanceEarnings;
    }

    private function getLeaves(array $input, $employeeHourlyRate): array
    {
        $leaveEarnings = null;
        if (isset($input['regularEarnings']) && isset($input['regularEarnings']['sickLeave'])) {
            $leaves = $input['regularEarnings']['sickLeave'];
            foreach ($leaves as $leave) {
                $leaveEarnings[Leave::TYPE_SICK_LEAVE][] = [
                    'date' => $leave['date'],
                    'rate' => $leave['rate'],
                    'hours' => $leave['hours'],
                    'pay' => $leave['rate'] * $leave['hours'] * $employeeHourlyRate
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
                    'pay' => $leave['rate'] * $leave['hours'] * $employeeHourlyRate
                ];
            }
        }
        return $leaveEarnings;
    }

    private function getHolidaysEarnings(array $input, $employeeHourlyRate): array
    {
        $holidayEarnings = null;
        $holidayWorkedEarnings = null;
        foreach (Holiday::HOLIDAY_TYPES as $type) {
            $holidayKey = "{$type}Holiday";
            if (isset($input['regularEarnings']) && isset($input['regularEarnings'][$holidayKey])) {
                $holidays = $input['regularEarnings'][$holidayKey];
                $holidayEarnings[$type] = $this->parseHoliday($holidays, $employeeHourlyRate);
            }

            $holidayKey = "{$type}HolidayWorked";
            if (isset($input['regularEarnings']) && isset($input['regularEarnings'][$holidayKey])) {
                $holidays = $input['regularEarnings'][$holidayKey];
                $holidayWorkedEarnings[$type][] = $this->parseHoliday($holidays, $employeeHourlyRate);
            }
        }
        return [$holidayEarnings, $holidayWorkedEarnings];
    }

    private function parseHoliday($holidays, $employeeHourlyRate)
    {
        $holidayEarnings = null;
        foreach ($holidays as $holiday) {
            $holidayEarnings[] = [
                'date' => $holiday['date'],
                'rate' => $holiday['rate'],
                'hours' => $holiday['hours'],
                'pay' => $holiday['rate'] * $holiday['hours'] * $employeeHourlyRate
            ];
        }
        return $holidayEarnings;
    }
}
