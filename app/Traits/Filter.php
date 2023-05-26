<?php

namespace App\Traits;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

trait Filter
{
    protected $searchables = [];

    protected function applyFilters(Request $request, $query): LengthAwarePaginator
    {
        if ($request->has('search')) {
            $search = $request->input('search');
            foreach ($this->searchables as $searchable) {
                $query->orWhere($searchable, 'like', '%' . $search . '%');
            }
        }
        $sortColumn = $request->input('sort');
        $sortDirection = 'asc';
        if (strpos($sortColumn, '-') === 0) {
            $sortDirection = 'desc';
            $sortColumn = ltrim($sortColumn, '-');
        }
        $query->orderBy($sortColumn, $sortDirection);

        $perPage = $request->input('per_page', 10);
        return $query->paginate($perPage);
    }
}