<?php

namespace App\Services\Payroll;

use App\Http\Requests\Payroll\UpdatePayrollRequest;
use App\Models\Payroll;
use Barryvdh\DomPDF\Facade\Pdf;

class PayrollService
{
    protected $updatePayrollService;

    public function __construct(UpdatePayrollService $updatePayrollService)
    {
        $this->updatePayrollService = $updatePayrollService;
    }

    public function update(Payroll $payroll, UpdatePayrollRequest $request): Payroll
    {
        return $this->updatePayrollService->update($payroll, $request);
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
