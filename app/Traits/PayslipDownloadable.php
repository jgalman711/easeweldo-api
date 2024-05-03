<?php

namespace App\Traits;

use App\Models\Payroll;
use Barryvdh\DomPDF\Facade\Pdf;

trait PayslipDownloadable
{
    public function download(): string
    {
        $pdf = Pdf::loadView('pdf.payslip', [
            'payroll' => $this,
            'period' => $this->period,
            'employee' => $this->employee,
            'company' => optional($this->employee)->company,
        ]);
        return $pdf->stream();
    }
}
