<?php

namespace App\Http\Filters\V2;

use Illuminate\Contracts\Database\Query\Builder;

class LeaveFilter extends Filter
{
    public function status(string $value): Builder
    {
        return $this->builder->whereIn('status', explode(',', $value));
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
