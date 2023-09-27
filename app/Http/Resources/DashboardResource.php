<?php

namespace App\Http\Resources;

use App\Models\TimeRecord;
use App\Models\WorkSchedule;
use Carbon\Carbon;
use Illuminate\Http\Request;

class DashboardResource extends BaseResource
{
    public function toArray(Request $request): array
    {
        $timeRecord = $this['timeRecord'];
        $workSchedule = $this['workSchedule'];
        return [
            'work_today' => $this->parseWorkToday($timeRecord),
            'schedule' => $this->parseWorkSchedule($workSchedule)
        ];
    }

    private function parseWorkToday(TimeRecord $timeRecord): array
    {
        return [
            'day' => Carbon::now()->format('l'),
            'date' =>Carbon::now()->format('d'),
            'clock_in' => optional($timeRecord)->clock_in,
            'clock_out' => optional($timeRecord)->clock_out,
            'expected_clock_in' => Carbon::parse(optional($timeRecord)->expected_clock_in)->format('h:i A'),
            'expected_clock_out' => Carbon::parse(optional($timeRecord)->expected_clock_out)->format('h:i A')
        ];
    }

    private function parseWorkSchedule(WorkSchedule $workSchedule): array
    {
        $result = [];
        $daysOfWeek = [
            'sunday', 'monday', 'tuesday', 'wednesday', 'thursday', 'friday', 'saturday',
        ];
        $currentDay = date('l');
        foreach ($daysOfWeek as $day) {
            $clockInKey = "{$day}_clock_in_time";
            $clockOutKey = "{$day}_clock_out_time";
            $clockIn = $workSchedule->$clockInKey;
            $clockOut = $workSchedule->$clockOutKey;
            $result[] = [
                'day' => strtoupper(substr($day, 0, 3)),
                'clock_in' => $clockIn ? Carbon::createFromFormat('H:i:s', $clockIn)->format('H:i') : null,
                'clock_out' => $clockOut ? Carbon::createFromFormat('H:i:s', $clockOut)->format('H:i') : null,
                'is_today' => (strtolower($day) === strtolower($currentDay)),
            ];
        }
        return $result;
    }
}
