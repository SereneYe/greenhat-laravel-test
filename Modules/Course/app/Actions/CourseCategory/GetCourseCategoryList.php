<?php

namespace Modules\Course\Actions\CourseCategory;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Modules\Course\Data\CourseCategory\CourseCategoryFiltersData;
use Modules\Course\Models\CourseCategory;
use Modules\Course\Transformers\CourseCategoryTransformer;
use Spatie\Fractal\Fractal;

class GetCourseCategoryList
{
    use AsAction;

    /**
     * Handle the action.
     *
     * @param CourseCategoryFiltersData $filters
     * @return LengthAwarePaginator
     */
    public function handle(CourseCategoryFiltersData $filters): LengthAwarePaginator
    {
        $query = CourseCategory::query();

        // Apply search filter
        if ($filters->has('search')) {
            $query->where(function (Builder $query) use ($filters) {
                $query->where('name', 'like', "%{$filters->search}%")
                    ->orWhere('description', 'like', "%{$filters->search}%");
            });
        }

        // Apply active status filter
        if ($filters->has('is_active') && $filters->is_active !== null) {
            $query->where('is_active', $filters->is_active);
        }

        // Apply sorting
        $sortBy = $filters->has('sort_by') ? $filters->sort_by : 'name';
        $sortDirection = $filters->has('sort_direction') ? $filters->sort_direction : 'asc';

        if ($sortBy === 'courses_count') {
            // Sort by courses count requires a subquery
            $query->withCount('courses')
                ->orderBy('courses_count', $sortDirection);
        } else {
            $query->orderBy($sortBy, $sortDirection);
        }

        // Apply pagination
        $perPage = $filters->has('per_page') ? $filters->per_page : 15;

        return $query->paginate($perPage);
    }

    /**
     * Handle the action as a controller.
     *
     * @param ActionRequest $request
     * @return LengthAwarePaginator
     */
    public function asController(ActionRequest $request): LengthAwarePaginator
    {
        return $this->handle(
            CourseCategoryFiltersData::validateAndCreate($request->all())
        );
    }

    /**
     * Format the response.
     *
     * @param LengthAwarePaginator $paginator
     * @return Fractal
     */
    public function jsonResponse(LengthAwarePaginator $paginator): Fractal
    {
        return fractal()
            ->collection($paginator->items(), new CourseCategoryTransformer())
            ->paginateWith(new \League\Fractal\Pagination\IlluminatePaginatorAdapter($paginator));
    }
}
