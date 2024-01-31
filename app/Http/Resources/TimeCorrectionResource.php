<?php

namespace App\Http\Resources;

use DateTime;
use Illuminate\Http\Request;

class TimeCorrectionResource extends BaseResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            'description' => $this->description,
            'employee_id' => $this->employee_id,
            'company_id' => $this->company_id,
            'date' => $this->date,
            'clock_in' => $this->timeFormat($this->clock_in),
            'clock_out' => $this->timeFormat($this->clock_out),
            'remarks' => $this->remarks,
            'status' => $this->status
        ];
    }

    private function timeFormat(?string $dateTime = null): ?string
    {
        if ($dateTime) {
            $datetime = new DateTime($dateTime);
            return $datetime->format('H:i:s');
        }
        return $dateTime;
    }
}
