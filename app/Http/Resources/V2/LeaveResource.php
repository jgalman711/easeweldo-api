<?php

namespace App\Http\Resources\V2;

use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Http\Resources\Json\JsonResource;

class LeaveResource extends JsonResource
{
    public function toArray(Request $request): array
    {
        return [
            'type' => 'leave',
            'id' => $this->id,
            'attributes' => [
                'title' => $this->title,
                $this->mergeWhen(
                    $request->routeIs('leaves.show'), [
                    'description' => $this->description,
                    'remarks' => $this->remarks,
                    'approvedBy' => $this->approved_by,
                    'approvedDate' => $this->approved_date,
                    'submittedDate' => $this->submitted_date
                ]),
                'hours' => $this->hours,
                'date' => [$this->date, Carbon::parse($this->date)->diffForHumans()],
                'type' => ucwords(str_replace("_", " ", $this->type)),
                'approver' => $this->when(
                    $request->routeIs('leaves.*'),
                    optional(optional($this->employee)->supervisor)->full_name,
                ),
                'status' => $this->status
            ],
            'relationships' => [
                'employee' => [
                    'data' => [
                        'type' => 'employee',
                        'id' => $this->employee_id,
                    ],
                    'links' => [
                        ['self' => route('employees.show', [$this->company->slug, $this->employee_id])]
                    ]
                ]
            ],
            'links' => [
                ['self' => route('leaves.show', [$this->company->slug, $this->employee_id, $this->id])]
            ]
        ];
    }
}
