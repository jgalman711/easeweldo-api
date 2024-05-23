<?php

namespace App\Http\Filters\V2;

use Illuminate\Contracts\Database\Query\Builder;

class LeaveFilter extends Filter
{
    protected $sortable = [
        'date',
        'status',
        'type'
    ];

    public function status(string $status): Builder
    {
        return $this->builder->whereIn('status', explode(',', $status));
    }

    public function type(string $type): Builder
    {
        return $this->builder->whereIn('type', explode(',', $type));
    }

    public function date(string $date): Builder
    {
        $dates = explode(',', $date);

        if (count($dates) > 1) {
            return $this->builder->whereBetween('date', $dates);
        }

        return $this->builder->whereDate('date', $dates);
    }
}
