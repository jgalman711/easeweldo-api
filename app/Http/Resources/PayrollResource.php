<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class PayrollResource extends BaseResource
{
    private const SIXTY_MINUTES = 60;

    public function toArray(Request $request): array
    {
        $data = parent::toArray($request);
        $indeces = ['overtime', 'late', 'absent', 'undertime'];
        foreach ($indeces as $index) {
            $hoursKey = $index . '_hours';
            $minutesKey = $index . '_minutes';
            $data[$hoursKey] = $data[$minutesKey] ? $data[$minutesKey] * self::SIXTY_MINUTES : null;
        }
        return $data;
    }
}
