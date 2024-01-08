<?php

namespace App\Services\Payroll;

use App\Models\Employee;
use App\Models\Payroll;
use App\Models\Period;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Response;

class PayrollService
{
    protected $regularPayrollService;

    public function __construct(RegularPayrollService $regularPayrollService)
    {
        $this->regularPayrollService = $regularPayrollService;
    }

    public function generate(Period $period, Employee $employee, array $additional = []): Payroll
    {
        return $this->regularPayrollService->generate($period, $employee, $additional);
    }

    public function update(Payroll $payroll, array $data): Payroll
    {
        return $this->regularPayrollService->update($payroll, $data);
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
}
