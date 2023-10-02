<?php

namespace App\Http\Resources;

use DateTime;
use Illuminate\Http\Request;

class PayrollResource extends BaseResource
{
    public function toArray(Request $request): array
    {
        $data = parent::toArray($request);
        if (isset($this->period) && $this->period) {
            $start_date = is_string($this->period->start_date)
                ? new DateTime($this->period->start_date)
                : $this->period->start_date;
            $end_date = is_string($this->period->end_date)
                ? new DateTime($this->period->end_date)
                : $this->period->end_date;
            $data['period_duration'] = $start_date->format('F d') . ' - ' . $end_date->format('F d, Y');
        }
        return $data;
    }
}
