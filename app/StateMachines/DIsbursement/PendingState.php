<?php

namespace App\StateMachines\Disbursement;

use App\Enumerators\DisbursementEnumerator;
use App\Mail\PayEmployees;
use Exception;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;

class PendingState extends BaseState
{
    public function pay(): void
    {
        foreach ($this->disbursement->payrolls as $payroll) {
            try {
                $payroll->state()->pay();
            } catch (Exception $e) {
                Log::error($e->getMessage());
            }
        }
        $this->disbursement->update(['status' => DisbursementEnumerator::STATUS_COMPLETED]);

        $company = $this->disbursement->company()->with('setting', 'banks')->first();
            $settings = $company->setting;
            $bank = $company->banks->first();
        if ($bank->email && $settings->auto_send_email_to_bank) {
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
