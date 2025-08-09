<?php

namespace Modules\Course\Actions\Course;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Pagination\LengthAwarePaginator;
use Lorisleiva\Actions\ActionRequest;
use Lorisleiva\Actions\Concerns\AsAction;
use Modules\Course\Data\Course\CourseFiltersData;
use Modules\Course\Models\Course;
use Modules\Course\Transformers\CourseTransformer;
use Spatie\Fractal\Fractal;

class GetCourseList
{
    use AsAction;

    /**
     * Handle the action.
     *
     * @param CourseFiltersData|array $filters
     * @return LengthAwarePaginator
     */
    public function handle($filters): LengthAwarePaginator
    {
        // Convert array to CourseFiltersData if needed
        if (is_array($filters)) {
            $filters = CourseFiltersData::from($filters);
        }

        $query = Course::query();

        // Always eager load categories for proper API responses
        $query->with(['categories']);

        // Apply additional relationship loading if specified
        if ($filters->has('with') && is_array($filters->with)) {
            $additionalRelations = array_diff($filters->with, ['categories']);
            if (!empty($additionalRelations)) {
                $query->with($additionalRelations);
            }
        }

        // Apply search filter
        if ($filters->has('search') && $filters->search !== null) {
            $search = $filters->search;
            $query->where(function (Builder $query) use ($search) {
                $query->where('title', 'like', "%{$search}%")
                    ->orWhere('description', 'like', "%{$search}%")
                    ->orWhere('instructor', 'like', "%{$search}%");
            });
        }

        // Apply level filter
        if ($filters->has('level') && $filters->level !== null) {
            $query->byLevel($filters->level);
        }

        // Apply categories filter
        if ($filters->has('categories') && !empty($filters->categories)) {
            $query->byCategories($filters->categories);
        }

        // Apply price range filters
        if ($filters->has('min_price') && $filters->min_price !== null) {
            $query->where('price', '>=', $filters->min_price);
        }

        if ($filters->has('max_price') && $filters->max_price !== null) {
            $query->where('price', '<=', $filters->max_price);
        }

        // Apply duration range filters
        if ($filters->has('min_duration') && $filters->min_duration !== null) {
            $query->where('duration_hours', '>=', $filters->min_duration);
        }

        if ($filters->has('max_duration') && $filters->max_duration !== null) {
            $query->where('duration_hours', '<=', $filters->max_duration);
        }

        // Cover media filter removed

        // Apply sorting
        $sortBy = $filters->getSortBy();
        $sortDirection = $filters->getSortDirection();
        $query->orderBy($sortBy, $sortDirection);

        // Return paginator
        return $query->paginate($filters->getPerPage());
    }

    /**
     * Handle the action as a controller.
     *
     * @param ActionRequest $request
     * @return Fractal
     */
    public function asController(ActionRequest $request): Fractal
    {
        $courses = $this->handle(
            CourseFiltersData::validateAndCreate($request->all())
        );

        return $this->jsonResponse($courses);
    }

    /**
     * Format the response.
     *
     * @param LengthAwarePaginator $courses
     * @return Fractal
     */
    public function jsonResponse(LengthAwarePaginator $courses): Fractal
    {
        return fractal()
            ->collection($courses->items(), new CourseTransformer())
            ->paginateWith(new \League\Fractal\Pagination\IlluminatePaginatorAdapter($courses));
    }
}
