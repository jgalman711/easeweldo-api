<?php

namespace App\Services\Payroll;

use App\Models\Payroll;
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
}
