<?php

namespace App\Repositories;

use App\Models\Holiday;
use Illuminate\Support\Facades\Cache;

class HolidayRepository
{
    public function getHolidaysForPeriod($startDate, $endDate)
    {
        $key = 'holidays_' . $startDate . '_' . $endDate;
        return Cache::remember($key, $this->calculateCacheDuration(), function () use ($startDate, $endDate) {
            return Holiday::whereBetween('date', [$startDate, $endDate])->get();
        });
    }

    private function calculateCacheDuration()
    {
        return now()->addHours(6);
    }
}
