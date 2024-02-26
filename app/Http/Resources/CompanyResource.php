<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class CompanyResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            "id" => $this->id,
            "name" => $this->name,
            "legal_name" => $this->legal_name,
            "contact_name" => $this->contact_name,
            "slug" => $this->slug,
            "status" => $this->status,
            "email_address" => $this->email_address,
        ];
    }
}
