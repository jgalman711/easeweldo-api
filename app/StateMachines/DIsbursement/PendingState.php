<?php

namespace App\StateMachines\Disbursement;

use App\Enumerators\DisbursementEnumerator;
use App\Mail\PayEmployees;
use Barryvdh\DomPDF\Facade\Pdf;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class PendingState extends BaseState
{
    public function pay(): void
    {
        $this->disbursement->load('payrolls.employee', 'company.setting', 'company.banks');
        foreach ($this->disbursement->payrolls as $payroll) {
            try {
                $payroll->state()->pay();
            } catch (Exception $e) {
                Log::error($e->getMessage());
            }
        }
        $this->disbursement->update(['status' => DisbursementEnumerator::STATUS_COMPLETED]);

        $company = $this->disbursement->company;
        $bank = $company->banks->first();
        if ($bank->email && $company->setting->auto_send_email_to_bank) {
            Mail::to($bank->email)->send(new PayEmployees($company, $bank, $this->disbursement));
        }
    }

    public function cancel(): void
    {
        foreach ($this->disbursement->payrolls as $payroll) {
            try {
                $payroll->state()->cancel();
            } catch (Exception $e) {
                Log::error($e->getMessage());
            }
        }
        $this->disbursement->update(['status' => DisbursementEnumerator::STATUS_CANCELLED]);
    }
}
