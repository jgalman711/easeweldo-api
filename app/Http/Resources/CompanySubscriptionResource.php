<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CompanySubscriptionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'subscription' => $this->subscription->title,
            'status' => $this->status,
            'amount_per_employee' => $this->amount_per_employee,
            'employee_count' => $this->employee_count,
            'amount' => $this->amount,
            'amount_paid' => $this->amount_paid,
            'balance' => $this->balance,
            'overpaid_balance' => $this->overpaid_balance,
            'start_date' => $this->start_date,
            'end_date' => $this->end_date
        ];
    }
}
