<?php

namespace App\Http\Resources;

use Carbon\Carbon;
use Illuminate\Http\Request;

class LeaveResource extends BaseResource
{
    public function toArray(Request $request): array
    {
        if ($request->format == 'tabular') {
            return $this->formatTabular();
        } else {
            return $this->formatDefault();
        }
    }

    private function formatTabular()
    {
        $date = Carbon::parse($this->date);
        return [
            'id' => $this->id,
            'type' => ucwords(str_replace("_", " ", $this->type)),
            'name' => [
                optional($this->employee)->full_name,
                ucwords(optional($this->employee)->job_title)
            ],
            'description' => $this->description,
            'hours' => $this->hours,
            'date' => [
                $date->format('d F Y'),
                $date->diffForHumans()
            ],
            'status' => $this->status,
        ];
    }

    private function formatDefault()
    {
        $date = Carbon::parse($this->date);
        return [
            'id' => $this->id,
            'type' => $this->type,
            'title' => $this->title,
            'description' => $this->description,
            'hours' => $this->hours,
            'date' => $this->date,
            'month' => $date->format('F'),
            'day' => $date->format('d'),
            'year' => $date->year,
            'formatted_date' => $date->format('d F Y'),
            'diff_for_humans' => $date->diffForHumans(),
            'submitted_date' => $this->submitted_date,
            'remarks' => $this->remarks,
            'status' => $this->status,
        ];
    }
}
