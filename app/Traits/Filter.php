<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

trait Filter
{
    protected function applyFilters(Request $request, $query, ?array $searchableColumns = []): LengthAwarePaginator
    {
        if ($request->has('search')) {
            $search = $request->input('search');
            foreach ($searchableColumns as $searchableColumn) {
                $query->orWhere($searchableColumn, 'like', '%' . $search . '%');
            }
        }
        if ($request->has('sort')) {
            $sortColumn = $request->input('sort');
            $sortDirection = 'asc';
            if (strpos($sortColumn, '-') === 0) {
                $sortDirection = 'desc';
                $sortColumn = ltrim($sortColumn, '-');
            }
            $query->orderBy($sortColumn, $sortDirection);
        } else {
            $query->orderBy('created_at', 'asc');
        }
        $perPage = $request->input('per_page', 10);
        return $query->paginate($perPage);
    }
}