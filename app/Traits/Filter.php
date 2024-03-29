<?php

namespace App\Traits;

use Closure;
use Exception;
use Illuminate\Http\Request;

trait Filter
{
    /*
     * @return LengthAwarePaginator or Collection
     */
    protected function applyFilters(Request $request, $query, ?array $searchableColumns = [])
    {
        if ($request->has('filter')) {
            $query->where($this->filter($request));
        }
        if ($request->has('search')) {
            $query->where($this->search($searchableColumns, $request));
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
        if ($request->has('per_page')) {
            $perPage = $request->input('per_page', 10);
            return $query->paginate($perPage);
        }
        return $query->get();
    }

    protected function filter(Request $request): Closure
    {
        return function ($filterQuery) use ($request) {
            foreach ($request->filter as $key => $value) {
                if ($key == 'from_date') {
                    $filterQuery->whereDate($key, '>=', $value);
                } elseif ($key == 'to_date') {
                    $filterQuery->whereDate($key, '<=', $value);
                } else {
                    $filterQuery->where($key, $value);
                }
            }
        };
    }

    protected function search(array $searchableColumns, Request $request): Closure
    {
        $search = $request->input('search');
        return function ($searchQuery) use ($searchableColumns, $search) {
            foreach ($searchableColumns as $searchableColumn) {
                $columnRelationship = explode('.', $searchableColumn);
                if (count($columnRelationship) == 2) {
                    $searchQuery->orWhereHas(
                        $columnRelationship[0],
                        function ($queryRelationship) use ($columnRelationship, $search) {
                            $queryRelationship->where($columnRelationship[1], 'like', '%' . $search . '%');
                        }
                    );
                } elseif (count($columnRelationship) == 1) {
                    $searchQuery->orWhere($searchableColumn, 'like', '%' . $search . '%');
                } else {
                    throw new Exception('Unsupported search query');
                }
            }
        };
    }
}
