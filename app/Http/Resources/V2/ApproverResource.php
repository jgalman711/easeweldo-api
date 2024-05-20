<?php

namespace App\Http\Resources\V2;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class ApproverResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'employee_id' => $this->id,
            'name' => $this->user->full_name,
            'request_type' => $this->pivot->request_type,
            'order' => $this->pivot->order
        ];
    }
}
