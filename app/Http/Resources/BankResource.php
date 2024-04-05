<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class BankResource extends BaseResource
{
    public function toArray(Request $request): array
    {
        return [
            'name' => $this->name,
            'branch' => $this->branch,
            'account_name' => $this->account_name,
            'account_number' => $this->account_number,
            'email' => $this->email,
            'contact_name' => $this->contact_name,
            'contact_number' => $this->contact_number,
        ];
    }
}
