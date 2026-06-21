<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;

/**
 * Reusable search service — eliminates the duplicated getMatches logic
 * across all controllers.
 */
class SearchService
{
    /**
     * Generic search on a model for the authenticated user.
     *
     * @param class-string<Model> $modelClass
     * @param string $query The search term.
     * @param array<string> $searchableColumns Columns to search on.
     * @param int $perPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function searchForUser(
        string $modelClass,
        string $query,
        array $searchableColumns,
        int $perPage = 200
    ) {
        return $modelClass::where('user_id', auth()->id())
            ->where(function (Builder $q) use ($query, $searchableColumns) {
                foreach ($searchableColumns as $column) {
                    $q->orWhere($column, 'like', "%{$query}%");
                }
            })
            ->paginate($perPage);
    }
}
