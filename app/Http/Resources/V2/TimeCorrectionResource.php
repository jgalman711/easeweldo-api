<?php

namespace App\Http\Resources\V2;

use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class TimeCorrectionResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'type' => 'time correction',
            'id' => $this->id,
            'attributes' => [
                'title' => $this->title,
                $this->mergeWhen(
                    $request->routeIs('employees.time-corrections.show'), [
                    'description' => $this->description,
                    'remarks' => $this->remarks
                ]),
                'date' => $this->date,
                'clockIn' => $this->clock_in,
                'clockOut' => $this->clock_out,
                'status' => $this->status
            ],
            'relationships' => $this->when($request->routeIs('time-corrections.*'),
                [
                    'employee' => [
                        'data' => [
                            'type' => 'employee',
                            'id' => $this->employee_id,
                        ],
                        'links' => [
                            ['self' => route('employees.show', [$this->company->slug, $this->employee_id])]
                        ]
                    ]
                ]
            ),
            'links' => [
                ['self' => route('employees.time-corrections.show', [$this->company->slug, $this->employee_id, $this->id])]
            ]
        ];
    }
}
