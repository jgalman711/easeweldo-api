<?php

namespace App\Traits;

use Illuminate\Http\Request;
use Illuminate\Pagination\LengthAwarePaginator;

trait Filter
{
    public const SORT_ASC = 'asc';
    public const SORT_DESC = 'desc';

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
            $sortDirection = self::SORT_ASC;
            if (strpos($sortColumn, '-') === 0) {
                $sortDirection = self::SORT_DESC;
                $sortColumn = ltrim($sortColumn, '-');
            }
            $query->orderBy($sortColumn, $sortDirection);
        } else {
            $query->orderBy('created_at', self::SORT_DESC);
        }
        $perPage = $request->input('per_page', 10);
        return $query->paginate($perPage);
    }
}