<?php

namespace Tests\Unit\Course\Actions;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Pagination\LengthAwarePaginator;
use Modules\Course\Actions\CourseCategory\GetCourseCategoryList;
use Modules\Course\Data\CourseCategory\CourseCategoryFiltersData;
use Modules\Course\Models\CourseCategory;
use Tests\TestCase;

class GetCourseCategoryListTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that the action retrieves a paginated list of course categories.
     */
    public function test_retrieves_paginated_list_of_course_categories(): void
    {
        // Create some categories
        CourseCategory::create([
            'name' => 'Category A',
            'slug' => 'category-a',
            'is_active' => true,
        ]);

        CourseCategory::create([
            'name' => 'Category B',
            'slug' => 'category-b',
            'is_active' => true,
        ]);

        CourseCategory::create([
            'name' => 'Category C',
            'slug' => 'category-c',
            'is_active' => true,
        ]);

        $filters = CourseCategoryFiltersData::from([
            'per_page' => 2, // Limit to 2 per page to test pagination
        ]);

        $action = new GetCourseCategoryList();
        $result = $action->handle($filters);

        $this->assertInstanceOf(LengthAwarePaginator::class, $result);
        $this->assertEquals(3, $result->total()); // Total of 3 categories
        $this->assertEquals(2, $result->count()); // 2 per page
        $this->assertEquals(2, $result->perPage());
        $this->assertEquals(1, $result->currentPage());
        $this->assertEquals(2, $result->lastPage());
    }

    /**
     * Test that the action filters categories by active status.
     */
    public function test_filters_categories_by_active_status(): void
    {
        // Create active and inactive categories
        CourseCategory::create([
            'name' => 'Active Category 1',
            'slug' => 'active-category-1',
            'is_active' => true,
        ]);

        CourseCategory::create([
            'name' => 'Active Category 2',
            'slug' => 'active-category-2',
            'is_active' => true,
        ]);

        CourseCategory::create([
            'name' => 'Inactive Category',
            'slug' => 'inactive-category',
            'is_active' => false,
        ]);

        // Filter by active status
        $filters = CourseCategoryFiltersData::from([
            'is_active' => true,
        ]);

        $action = new GetCourseCategoryList();
        $result = $action->handle($filters);

        $this->assertEquals(2, $result->total()); // Only the 2 active categories

        // Check that all returned categories are active
        foreach ($result->items() as $category) {
            $this->assertTrue($category->is_active);
        }
    }

    /**
     * Test that the action filters categories by search term.
     */
    public function test_filters_categories_by_search_term(): void
    {
        // Create categories with different names
        CourseCategory::create([
            'name' => 'Technical Skills',
            'slug' => 'technical-skills',
            'description' => 'Technical skills description',
            'is_active' => true,
        ]);

        CourseCategory::create([
            'name' => 'Leadership',
            'slug' => 'leadership',
            'description' => 'Leadership skills are important',
            'is_active' => true,
        ]);

        CourseCategory::create([
            'name' => 'Communication',
            'slug' => 'communication',
            'description' => 'Communication technical aspects',
            'is_active' => true,
        ]);

        // Search for 'technical' in name or description
        $filters = CourseCategoryFiltersData::from([
            'search' => 'technical',
        ]);

        $action = new GetCourseCategoryList();
        $result = $action->handle($filters);

        $this->assertEquals(2, $result->total()); // Should find 2 categories with 'technical'

        // Check that all returned categories contain 'technical' in name or description
        $categoryNames = collect($result->items())->pluck('name')->toArray();
        $this->assertContains('Technical Skills', $categoryNames);
        $this->assertContains('Communication', $categoryNames);
        $this->assertNotContains('Leadership', $categoryNames);
    }

    /**
     * Test that the action sorts categories by name.
     */
    public function test_sorts_categories_by_name(): void
    {
        // Create categories with different names
        CourseCategory::create([
            'name' => 'Z Category',
            'slug' => 'z-category',
        ]);

        CourseCategory::create([
            'name' => 'A Category',
            'slug' => 'a-category',
        ]);

        CourseCategory::create([
            'name' => 'M Category',
            'slug' => 'm-category',
        ]);

        // Sort by name ascending
        $filters = CourseCategoryFiltersData::from([
            'sort_by' => 'name',
            'sort_direction' => 'asc',
        ]);

        $action = new GetCourseCategoryList();
        $result = $action->handle($filters);

        $categories = $result->items();
        $this->assertEquals('A Category', $categories[0]->name);
        $this->assertEquals('M Category', $categories[1]->name);
        $this->assertEquals('Z Category', $categories[2]->name);

        // Sort by name descending
        $filters = CourseCategoryFiltersData::from([
            'sort_by' => 'name',
            'sort_direction' => 'desc',
        ]);

        $result = $action->handle($filters);

        $categories = $result->items();
        $this->assertEquals('Z Category', $categories[0]->name);
        $this->assertEquals('M Category', $categories[1]->name);
        $this->assertEquals('A Category', $categories[2]->name);
    }

    /**
     * Test that the action sorts categories by created_at date.
     */
    public function test_sorts_categories_by_created_at(): void
    {
        // Create categories with different creation dates
        CourseCategory::create([
            'name' => 'Old Category',
            'slug' => 'old-category',
            'created_at' => now()->subDays(3),
        ]);

        CourseCategory::create([
            'name' => 'New Category',
            'slug' => 'new-category',
            'created_at' => now(),
        ]);

        CourseCategory::create([
            'name' => 'Middle Category',
            'slug' => 'middle-category',
            'created_at' => now()->subDay(),
        ]);

        // Sort by created_at ascending (oldest first)
        $filters = CourseCategoryFiltersData::from([
            'sort_by' => 'created_at',
            'sort_direction' => 'asc',
        ]);

        $action = new GetCourseCategoryList();
        $result = $action->handle($filters);

        $categories = $result->items();
        $this->assertEquals('Old Category', $categories[0]->name);
        $this->assertEquals('Middle Category', $categories[1]->name);
        $this->assertEquals('New Category', $categories[2]->name);

        // Sort by created_at descending (newest first)
        $filters = CourseCategoryFiltersData::from([
            'sort_by' => 'created_at',
            'sort_direction' => 'desc',
        ]);

        $result = $action->handle($filters);

        $categories = $result->items();
        $this->assertEquals('New Category', $categories[0]->name);
        $this->assertEquals('Middle Category', $categories[1]->name);
        $this->assertEquals('Old Category', $categories[2]->name);
    }

    /**
     * Test that the action handles empty results.
     */
    public function test_handles_empty_results(): void
    {
        // Don't create any categories

        $filters = CourseCategoryFiltersData::from([]);

        $action = new GetCourseCategoryList();
        $result = $action->handle($filters);

        $this->assertInstanceOf(LengthAwarePaginator::class, $result);
        $this->assertEquals(0, $result->total());
        $this->assertEmpty($result->items());
    }

    /**
     * Test that the action handles combined filters.
     */
    public function test_handles_combined_filters(): void
    {
        // Create various categories
        CourseCategory::create([
            'name' => 'Active Technical',
            'slug' => 'active-technical',
            'description' => 'Technical skills',
            'is_active' => true,
        ]);

        CourseCategory::create([
            'name' => 'Inactive Technical',
            'slug' => 'inactive-technical',
            'description' => 'More technical skills',
            'is_active' => false,
        ]);

        CourseCategory::create([
            'name' => 'Active Leadership',
            'slug' => 'active-leadership',
            'description' => 'Leadership skills',
            'is_active' => true,
        ]);

        // Apply combined filters: active status + search term
        $filters = CourseCategoryFiltersData::from([
            'is_active' => true,
            'search' => 'technical',
        ]);

        $action = new GetCourseCategoryList();
        $result = $action->handle($filters);

        $this->assertEquals(1, $result->total()); // Only 'Active Technical' matches both filters
        $this->assertEquals('Active Technical', $result->items()[0]->name);
    }

    /**
     * Test that the action respects per_page parameter.
     */
    public function test_respects_per_page_parameter(): void
    {
        // Create 10 categories
        for ($i = 1; $i <= 10; $i++) {
            CourseCategory::create([
                'name' => "Category {$i}",
                'slug' => "category-{$i}",
            ]);
        }

        // Test different per_page values
        $perPageValues = [5, 7, 10];

        foreach ($perPageValues as $perPage) {
            $filters = CourseCategoryFiltersData::from([
                'per_page' => $perPage,
            ]);

            $action = new GetCourseCategoryList();
            $result = $action->handle($filters);

            $this->assertEquals($perPage, $result->perPage());
            $this->assertEquals(min($perPage, 10), count($result->items()));
        }
    }
}
