<?php

namespace App\Http\Resources;

use App\Enumerators\DisbursementEnumerator;
use Illuminate\Http\Request;

class SettingsResource extends BaseResource
{
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "company_id" => $this->company_id,
            "period_cycle" => $this->period_cycle,
            "salary_day" => $this->salary_day,
            "grace_period" => $this->grace_period,
            "minimum_overtime" => $this->minimum_overtime,
            "is_ot_auto_approve" => $this->is_ot_auto_approve,
            "auto_send_email_to_bank" => $this->auto_send_email_to_bank,
            "auto_pay_disbursement" => $this->auto_pay_disbursement,
            "clock_action_required" => $this->clock_action_required,
            "disbursement_method" => $this->disbursement_method,
            "overtime_rate" => $this->overtime_rate,
            "leaves_convertible" => $this->leaves_convertible,
            "disbursementMethodLabel" => ucwords(str_replace("_", " ", $this->disbursement_method)),
            "payrollConfigLabel" => [
                "title" => ucfirst(str_replace("_", " ", $this->period_cycle)),
                "subtitle" => $this->getSubtitle()
            ]
        ];
    }

    private function getSubtitle(): string
    {
        if ($this->period_cycle == DisbursementEnumerator::SUBTYPE_WEEKLY) {
            $salaryDay = ucfirst($this->salary_day);
        } else {
            $salaryDay = $this->getOrdinal($this->salary_day[0]);
            if (isset($this->salary_day[1])) {
                $salaryDay .= " and " . $this->getOrdinal($this->salary_day[1]);
            }
        }
        return "Every $salaryDay";
    }

    private function getOrdinal(int $number): string {
        if (in_array((($number) % 100), [11, 12, 13])) {
            return $number.'th';
        }
        $lastDigit = $number % 10;

        switch ($lastDigit) {
            case 1:
                $ordinal = $number.'st';
                break;
            case 2:
                $ordinal = $number.'nd';
                break;
            case 3:
                $ordinal = $number.'rd';
                break;
            default:
                $ordinal = $number.'th';
                break;
        }
        return $ordinal;
    }
}
