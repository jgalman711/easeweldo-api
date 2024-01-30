<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class TimeCorrectionResource extends BaseResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'employee_id' => $this->employee_id,
            'company_id' => $this->company_id,
            'date' => $this->date,
            'clock_in' => $this->clock_in,
            'clock_out' => $this->clock_out,
            'title' => $this->title,
            'remarks' => $this->remarks,
            'status' => $this->status
        ];
    }
}
