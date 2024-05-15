<?php

namespace App\Http\Resources\V2;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TimeCorrectionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'id' => $this->id,
            'title' => $this->title,
            $this->mergeWhen(
                $request->routeIs('employees.time-corrections.show'), [
                'description' => $this->description,
                'remarks' => $this->remarks
            ]),
            'date' => $this->date,
            'dateForHumans' => Carbon::parse($this->date)->diffForHumans(),
            'clockIn' =>  Carbon::parse($this->clock_in)->format('H:i:s'),
            'clockOut' =>  Carbon::parse($this->clock_out)->format('H:i:s'),
            'status' => $this->status
        ];
    }
}
