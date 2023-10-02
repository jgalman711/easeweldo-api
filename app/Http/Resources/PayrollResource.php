<?php

namespace App\Http\Resources;

use DateTime;
use Illuminate\Http\Request;

class PayrollResource extends BaseResource
{
    private const SIXTY_MINUTES = 60;

    public function toArray(Request $request): array
    {
        if (isset($this->period) && $this->period->start_date) {
            $start_date = is_string($this->period->start_date)
                ? new DateTime($this->period->start_date)
                : $this->period->start_date;
            $end_date = is_string($this->period->end_date)
                ? new DateTime($this->period->end_date)
                : $this->period->end_date;
            $data['period_duration'] = $start_date->format('F d') . ' - ' . $end_date->format('F d, Y');
        }
        

        $data = parent::toArray($request);
        $indeces = ['overtime', 'late', 'absent', 'undertime'];
        foreach ($indeces as $index) {
            $hoursKey = $index . '_hours';
            $minutesKey = $index . '_minutes';
            $data[$hoursKey] = isset($data[$minutesKey]) && $data[$minutesKey] ? $data[$minutesKey] / self::SIXTY_MINUTES : null;
        }
        return $data;
    }
}
