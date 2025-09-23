<?php

namespace App\Services;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Http\Request;

class FilterService
{
    /**
     * Apply generic filters to an Eloquent query based on request parameters.
     *
     * @param Builder $query
     * @param Request $request
     * @param array $filterableColumns
     * @return Builder
     */
    public function applyFilters(Builder $query, Request $request, array $filterableColumns = []): Builder
    {
        foreach ($filterableColumns as $column) {
            if ($request->filled($column)) {
                $query->where($column, $request->input($column));
            }
        }

        // Date range filtering
        if ($request->filled('start_date')) {
            $query->whereDate('start_date', '>=', $request->start_date);
        }
        if ($request->filled('end_date')) {
            $query->whereDate('end_date', '<=', $request->end_date);
        }

        // Status filter (optional)
        if ($request->filled('status')) {
            $query->where('status', $request->status);
        }

        // Text search
        if ($request->filled('search')) {
            $search = $request->search;
            $query->where(function ($q) use ($search) {
                $q->where('name', 'like', "%{$search}%")
                  ->orWhere('description', 'like', "%{$search}%");
            });
        }

        return $query;
    }

    /**
     * Apply tournament-specific filters.
     *
     * @param Builder $query
     * @param Request $request
     * @return Builder
     */
    public function applyTournamentFilters(Builder $query, Request $request): Builder
    {
        if ($request->filled('level')) {
            $query->where('level', $request->level);
        }

        if ($request->filled('season_year_id')) {
            $query->where('season_year_id', $request->season_year_id);
        }

        if ($request->filled('province_id')) {
            $query->where('host_province_id', $request->province_id);
        }

        if ($request->filled('region_id')) {
            $query->where('host_region_id', $request->region_id);
        }

        if ($request->filled('school_id')) {
            $query->where('host_school_id', $request->school_id);
        }

        return $query;
    }

    /**
     * Apply sport-specific filters.
     *
     * @param Builder $query
     * @param Request $request
     * @return Builder
     */
    public function applySportFilters(Builder $query, Request $request): Builder
    {
        if ($request->filled('sport_id')) {
            $query->where('sport_id', $request->sport_id);
        }

        if ($request->filled('sport_subcategory_id')) {
            $query->where('sport_subcategory_id', $request->sport_subcategory_id);
        }

        if ($request->filled('division_id')) {
            $query->where('division_id', $request->division_id);
        }

        return $query;
    }

    /**
     * Apply sorting to a query.
     *
     * @param Builder $query
     * @param string|null $sortBy
     * @param string $sortOrder
     * @param array $defaultSort
     * @return Builder
     */
    public function applySorting(Builder $query, ?string $sortBy, string $sortOrder = 'asc', array $defaultSort = []): Builder
    {
        if ($sortBy) {
            $query->orderBy($sortBy, $sortOrder);
        } elseif (!empty($defaultSort)) {
            foreach ($defaultSort as $column => $order) {
                $query->orderBy($column, $order);
            }
        }

        return $query;
    }

    /**
     * Apply pagination to a query.
     *
     * @param Builder $query
     * @param Request $request
     * @param int $defaultPerPage
     * @return \Illuminate\Contracts\Pagination\LengthAwarePaginator
     */
    public function applyPagination(Builder $query, Request $request, int $defaultPerPage = 15)
    {
        $perPage = (int) $request->input('per_page', $defaultPerPage);
        return $query->paginate($perPage);
    }
}
