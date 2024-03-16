<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CompanyResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        $uploadsConfig = config('app.uploads');
        $companyUploadPath = $uploadsConfig['url'] . "/" . $uploadsConfig['company_path'];
        return [
            "id" => $this->id,
            "name" => $this->name,
            "legal_name" => $this->legal_name,
            "contact_name" => $this->contact_name,
            "slug" => $this->slug,
            "status" => $this->status,
            "email_address" => $this->email_address,
            "details" => $this->details,
            "logo" => $companyUploadPath . "/" . $this->logo,
            "address_line" => $this->address_line,
            "barangay_town_city_province" => $this->barangay_town_city_province,
            "mobile_number" => $this->mobile_number,
            "landline_number" => $this->landline_number,
            "bank_name" => $this->bank_name,
            "bank_account_name" => $this->bank_account_name,
            "bank_account_number" => $this->bank_account_number,
            "tin" => $this->tin,
            "sss_number" => $this->sss_number,
            "philhealth_number" => $this->philhealth_number,
            "pagibig_number" => $this->pagibig_number,
            "is_ot_auto_approve" => optional($this->setting)->is_ot_auto_approve,
            "overtime_rate" => optional($this->setting)->overtime_rate,
            'auto_send_email_to_bank' => optional($this->setting)->auto_send_email_to_bank,
            'auto_pay_disbursement' => optional($this->setting)->auto_pay_disbursement,
            'clock_action_required' => optional($this->setting)->clock_action_required,
            'disbursement_method' => optional($this->setting)->disbursement_method,
            'leaves_convertible' => optional($this->setting)->leaves_convertible
        ];
    }
}
