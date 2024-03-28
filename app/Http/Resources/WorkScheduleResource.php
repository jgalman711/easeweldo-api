<?php

namespace App\Http\Resources;

use Illuminate\Http\Request;

class WorkScheduleResource extends BaseResource
{
    protected $days = [
        'sunday',
        'monday',
        'tuesday',
        'wednesday',
        'thursday',
        'friday',
        'saturday',
    ];

    public function toArray(Request $request): array
    {
        if ($request->format == 'tabular') {
            $schedule = $this->formatAdminView();
        } else {
            $schedule = $this->formatDefault();
        }

        return $schedule;
    }

    private function formatDefault()
    {
        $schedule = [
            'id' => $this->id,
            'name' => $this->name,
        ];
        foreach ($this->days as $day) {
            $schedule["{$day}_clock_in_time"] = $this->{$day.'_clock_in_time'};
            $schedule["{$day}_clock_out_time"] = $this->{$day.'_clock_out_time'};
        }

        return $schedule;
    }

    private function formatAdminView()
    {
        $schedule = [
            'id' => $this->id,
            'name' => $this->name,
        ];
        foreach ($this->days as $day) {
            if ($this->{$day.'_clock_in_time'}) {
                $clockIn = substr($this->{$day.'_clock_in_time'}, 0, -3);
                $clockOut = substr($this->{$day.'_clock_out_time'}, 0, -3);
                $schedule[$day] = "$clockIn - $clockOut";
            } else {
                $schedule[$day] = 'Rest Day';
            }
        }

        return $schedule;
    }
}
