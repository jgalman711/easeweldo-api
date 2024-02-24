<?php

namespace App\Services\Payroll;

use App\Enumerators\PayrollEnumerator;
use App\Http\Requests\Payroll\UpdateRegularPayrollRequest;
use App\Models\Payroll;
use App\Models\PayrollAttendance;
use Barryvdh\DomPDF\Facade\Pdf;

class PayrollService
{
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

    public function update(Payroll $payroll, UpdateRegularPayrollRequest $request)
    {
        $employee = $payroll->employee;
        $salaryComputation = $employee->salaryComputation;
        $attendanceEarnings = [];
        if ($request->has('absents')) {
            foreach ($request->absents as $absent) {
                $amount = $absent['hours'] * $absent['rate'] * $salaryComputation->hourly_rate;
                $attendanceEarnings[] = [
                    'date' => $absent['date'],
                    'type' => PayrollEnumerator::ABSENT,
                    'hours' => $absent['hours'],
                    'rate' => $absent['rate'],
                    'amount' => $amount
                ];
            }
        }
        dd($attendanceEarnings);
    }
}
