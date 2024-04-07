<?php

namespace App\Traits;

use Closure;
use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

trait Filter
{
    /*
     * @return LengthAwarePaginator or Collection
     */
    protected function applyFilters(Request $request, $query, ?array $searchableColumns = [])
    {
        if ($request->has('filter')) {
            $query->where($this->filter($searchableColumns, $request));
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
        }
        if ($request->has('per_page')) {
            $perPage = $request->input('per_page', 10);

            return $query->paginate($perPage);
        }

        return $query->get();
    }

    protected function filter(array $searchableColumns, Request $request): Closure
    {
        return function ($filterQuery) use ($searchableColumns, $request) {
            foreach ($request->filter as $key => $value) {
                if ($key == 'from_date') {
                    $filterQuery->whereDate($key, '>=', $value);
                } elseif ($key == 'to_date') {
                    $filterQuery->whereDate($key, '<=', $value);
                } else {
                    $this->findInSearchableColumns($filterQuery, $searchableColumns, $value);
                }
            }
        };
    }

    protected function search(array $searchableColumns, Request $request): Closure
    {
        $search = $request->input('search');
        return function ($searchQuery) use ($searchableColumns, $search) {
            $this->findInSearchableColumns($searchQuery, $searchableColumns, $search);
        };
    }

    protected function findInSearchableColumns(&$query, $columns, $needle): void
    {
        foreach ($columns as $column) {
            $columnRelationship = explode('.', $column);
            if ($this->isWithRelationshipSearch($columnRelationship)) {
                $query->orWhereHas($columnRelationship[0],
                    function ($queryRelationship) use ($columnRelationship, $needle) {
                        $this->searchByColumn($queryRelationship, $columnRelationship[1], $needle);
                    }
                );
            } else {
                $query->orWhere(function ($singleQuery) use ($column, $needle) {
                    $this->searchByColumn($singleQuery, $column, $needle);
                });
            }
        }
    }

    private function searchByColumn(Builder &$queryBuilder, string $column, string $key): void
    {
        if ($column == 'full_name') {
            $queryBuilder->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%$key%"]);
        } elseif ($column == 'role') {
            $queryBuilder->whereHas('roles', function ($query) use ($key) {
                $query->where('name', 'like', "%{$key}%");
            });
        } else {
            $queryBuilder->where($column, 'like', "%{$key}%");
        }
    }

    private function isWithRelationshipSearch(array $columnRelationship): bool
    {
        throw_unless(
            in_array(count($columnRelationship), [1, 2]),
            new Exception('Unsupported search query')
        );

        return count($columnRelationship) == 2;
    }
}
