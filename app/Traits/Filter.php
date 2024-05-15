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
                } elseif ($key == 'role') {
                    $roles = explode(',', $value);
                    $filterQuery->whereHas('user.roles', function ($query) use ($roles) {
                        $query->where(function ($rolesQueryFilter) use ($roles) {
                            foreach ($roles as $role) {
                                $rolesQueryFilter->orWhere('name', 'like', "%{$role}%");
                            }
                        });
                    });
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
                if ($this->isWithRelationshipSearch($columnRelationship)) {
                    $searchQuery->orWhereHas($columnRelationship[0],
                        function ($queryRelationship) use ($columnRelationship, $search) {
                            $this->searchByColumn($queryRelationship, $columnRelationship[1], $search);
                        }
                    );
                } else {
                    $searchQuery->orWhere(function ($singleQuery) use ($searchableColumn, $search) {
                        $this->searchByColumn($singleQuery, $searchableColumn, $search);
                    });
                }
            }
        };
    }

    private function searchByColumn(Builder &$queryBuilder, string $column, string $key): void
    {
        if ($column == 'full_name') {
            $queryBuilder->whereRaw("CONCAT(first_name, ' ', last_name) LIKE ?", ["%$key%"]);
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
