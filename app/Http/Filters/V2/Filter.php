<?php

namespace App\Http\Filters\V2;

use Illuminate\Contracts\Database\Query\Builder;
use Illuminate\Http\Request;

abstract class Filter
{
    protected $builder;

    protected $request;

    public function __construct(Request $request)
    {
        $this->request = $request;
    }

    public function apply(Builder $builder): Builder
    {
        $this->builder = $builder;
        foreach ($this->request->all() as $key => $value) {
            if (method_exists($this, $key)) {
                $this->$key($value);
            }
        }
        return $builder;
    }

    public function filter(array $filter): Builder
    {
        foreach ($filter as $key => $value) {
            if (method_exists($this, $key)) {
                $this->$key($value);
            }
        }
        return $this->builder;
    }

    public function createdAt(string $createdAt): Builder
    {
        $dates = explode(',', $createdAt);

        if (count($dates) > 1) {
            return $this->builder->whereBetween('created_at', $dates);
        }

        return $this->builder->whereDate('created_at', $dates);
    }

    public function updatedAt(string $updatedAt): Builder
    {
        $dates = explode(',', $updatedAt);

        if (count($dates) > 1) {
            return $this->builder->whereBetween('updated_at', $dates);
        }

        return $this->builder->whereDate('updated_at', $dates);
    }
}
