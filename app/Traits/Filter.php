<?php

namespace App\Traits;

use Exception;
use Illuminate\Http\Request;

trait Filter
{
    /*
     * @return LengthAwarePaginator or Collection
     */
    protected function applyFilters(Request $request, $query, ?array $searchableColumns = [])
    {
        if ($request->has('search')) {
            $search = $request->input('search');
            $query->where(function ($searchQuery) use ($searchableColumns, $search) {
                foreach ($searchableColumns as $searchableColumn) {
                    $columnRelationship = explode('.', $searchableColumn);
                    if (count($columnRelationship) == 2) {
                        $searchQuery->orWhereHas(
                            $columnRelationship[0],
                            function ($queryRelationship) use ($columnRelationship, $search) {
                                $queryRelationship->orWhere($columnRelationship[1], $search);
                            }
                        );
                    } elseif (count($columnRelationship) == 1) {
                        $searchQuery->orWhere($searchableColumn, 'like', '%' . $search . '%');
                    } else {
                        throw new Exception('Unsupported search query');
                    }
                }
            });
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
}
