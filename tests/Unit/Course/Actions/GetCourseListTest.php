<?php

namespace Tests\Unit\Course\Actions;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Course\Actions\Course\GetCourseList;
use Modules\Course\Data\Course\CourseFiltersData;
use Modules\Course\Models\Course;
use Modules\Course\Models\CourseCategory;
use Tests\TestCase;

class GetCourseListTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that the action returns paginated courses.
     */
    public function test_returns_paginated_courses(): void
    {
        // Create 15 courses
        Course::factory()->count(15)->create();

        $action = new GetCourseList();
        $result = $action->handle(CourseFiltersData::from([]));

        // Default pagination should be 10 items per page
        $this->assertInstanceOf(\Illuminate\Pagination\LengthAwarePaginator::class, $result);
        $this->assertEquals(10, count($result->items()));
        $this->assertEquals(15, $result->total());
        $this->assertEquals(2, $result->lastPage());
    }

    /**
     * Test that the action filters by level.
     */
    public function test_filters_by_level(): void
    {
        // Create courses with different levels
        Course::factory()->count(3)->create(['level' => 'beginner']);
        Course::factory()->count(2)->create(['level' => 'intermediate']);
        Course::factory()->count(1)->create(['level' => 'advanced']);

        $action = new GetCourseList();

        // Filter by beginner level
        $result = $action->handle(['level' => 'beginner']);
        $this->assertInstanceOf(\Illuminate\Pagination\LengthAwarePaginator::class, $result);
        $this->assertEquals(3, $result->total());
        foreach ($result->items() as $course) {
            $this->assertEquals('beginner', $course->level);
        }

        // Filter by intermediate level
        $result = $action->handle(['level' => 'intermediate']);
        $this->assertInstanceOf(\Illuminate\Pagination\LengthAwarePaginator::class, $result);
        $this->assertEquals(2, $result->total());
        foreach ($result->items() as $course) {
            $this->assertEquals('intermediate', $course->level);
        }

        // Filter by advanced level
        $result = $action->handle(['level' => 'advanced']);
        $this->assertInstanceOf(\Illuminate\Pagination\LengthAwarePaginator::class, $result);
        $this->assertEquals(1, $result->total());
        foreach ($result->items() as $course) {
            $this->assertEquals('advanced', $course->level);
        }
    }

    /**
     * Test that the action filters by categories.
     */
    public function test_filters_by_categories(): void
    {
        // Create categories
        $category1 = CourseCategory::factory()->create();
        $category2 = CourseCategory::factory()->create();

        // Create courses and attach to categories
        $course1 = Course::factory()->create();
        $course1->categories()->attach($category1->id);

        $course2 = Course::factory()->create();
        $course2->categories()->attach($category2->id);

        $course3 = Course::factory()->create();
        $course3->categories()->attach([$category1->id, $category2->id]);

        $course4 = Course::factory()->create();
        // No categories attached to course4

        $action = new GetCourseList();

        // Filter by category1
        $result = $action->handle(['categories' => [$category1->id]]);
        $this->assertInstanceOf(\Illuminate\Pagination\LengthAwarePaginator::class, $result);
        $this->assertEquals(2, $result->total());
        $courseIds = collect($result->items())->pluck('id')->toArray();
        $this->assertContains($course1->id, $courseIds);
        $this->assertContains($course3->id, $courseIds);
        $this->assertNotContains($course2->id, $courseIds);
        $this->assertNotContains($course4->id, $courseIds);

        // Filter by category2
        $result = $action->handle(['categories' => [$category2->id]]);
        $this->assertInstanceOf(\Illuminate\Pagination\LengthAwarePaginator::class, $result);
        $this->assertEquals(2, $result->total());
        $courseIds = collect($result->items())->pluck('id')->toArray();
        $this->assertContains($course2->id, $courseIds);
        $this->assertContains($course3->id, $courseIds);
        $this->assertNotContains($course1->id, $courseIds);
        $this->assertNotContains($course4->id, $courseIds);

        // Filter by both categories
        $result = $action->handle(['categories' => [$category1->id, $category2->id]]);
        $this->assertInstanceOf(\Illuminate\Pagination\LengthAwarePaginator::class, $result);
        $this->assertEquals(3, $result->total());
        $courseIds = collect($result->items())->pluck('id')->toArray();
        $this->assertContains($course1->id, $courseIds);
        $this->assertContains($course2->id, $courseIds);
        $this->assertContains($course3->id, $courseIds);
        $this->assertNotContains($course4->id, $courseIds);
    }

    /**
     * Test that the action filters by price range.
     */
    public function test_filters_by_price_range(): void
    {
        // Create courses with different prices
        Course::factory()->create(['title' => 'Cheap Course', 'price' => 10.00]);
        Course::factory()->create(['title' => 'Mid-range Course', 'price' => 50.00]);
        Course::factory()->create(['title' => 'Expensive Course', 'price' => 100.00]);

        $action = new GetCourseList();

        // Filter by min price
        $result = $action->handle(['min_price' => 40]);
        $this->assertInstanceOf(\Illuminate\Pagination\LengthAwarePaginator::class, $result);
        $this->assertEquals(2, $result->total());
        $titles = collect($result->items())->pluck('title')->toArray();
        $this->assertContains('Mid-range Course', $titles);
        $this->assertContains('Expensive Course', $titles);
        $this->assertNotContains('Cheap Course', $titles);

        // Filter by max price
        $result = $action->handle(['max_price' => 60]);
        $this->assertInstanceOf(\Illuminate\Pagination\LengthAwarePaginator::class, $result);
        $this->assertEquals(2, $result->total());
        $titles = collect($result->items())->pluck('title')->toArray();
        $this->assertContains('Cheap Course', $titles);
        $this->assertContains('Mid-range Course', $titles);
        $this->assertNotContains('Expensive Course', $titles);

        // Filter by price range
        $result = $action->handle(['min_price' => 20, 'max_price' => 80]);
        $this->assertInstanceOf(\Illuminate\Pagination\LengthAwarePaginator::class, $result);
        $this->assertEquals(1, $result->total());
        $titles = collect($result->items())->pluck('title')->toArray();
        $this->assertNotContains('Cheap Course', $titles);
        $this->assertContains('Mid-range Course', $titles);
        $this->assertNotContains('Expensive Course', $titles);
    }

    /**
     * Test that the action filters by duration range.
     */
    public function test_filters_by_duration_range(): void
    {
        // Create courses with different durations
        Course::factory()->create(['title' => 'Short Course', 'duration_hours' => 3]);
        Course::factory()->create(['title' => 'Medium Course', 'duration_hours' => 10]);
        Course::factory()->create(['title' => 'Long Course', 'duration_hours' => 30]);

        $action = new GetCourseList();

        // Filter by min duration
        $result = $action->handle(['min_duration' => 8]);
        $this->assertInstanceOf(\Illuminate\Pagination\LengthAwarePaginator::class, $result);
        $this->assertEquals(2, $result->total());
        $titles = collect($result->items())->pluck('title')->toArray();
        $this->assertContains('Medium Course', $titles);
        $this->assertContains('Long Course', $titles);
        $this->assertNotContains('Short Course', $titles);

        // Filter by max duration
        $result = $action->handle(['max_duration' => 15]);
        $this->assertInstanceOf(\Illuminate\Pagination\LengthAwarePaginator::class, $result);
        $this->assertEquals(2, $result->total());
        $titles = collect($result->items())->pluck('title')->toArray();
        $this->assertContains('Short Course', $titles);
        $this->assertContains('Medium Course', $titles);
        $this->assertNotContains('Long Course', $titles);

        // Filter by duration range
        $result = $action->handle(['min_duration' => 5, 'max_duration' => 20]);
        $this->assertInstanceOf(\Illuminate\Pagination\LengthAwarePaginator::class, $result);
        $this->assertEquals(1, $result->total());
        $titles = collect($result->items())->pluck('title')->toArray();
        $this->assertNotContains('Short Course', $titles);
        $this->assertContains('Medium Course', $titles);
        $this->assertNotContains('Long Course', $titles);
    }

    /**
     * Test that the action sorts by title.
     */
    public function test_sorts_by_title(): void
    {
        // Create courses with specific titles to test sorting
        Course::factory()->create(['title' => 'Z Course']);
        Course::factory()->create(['title' => 'A Course']);
        Course::factory()->create(['title' => 'M Course']);

        $action = new GetCourseList();

        // Sort ascending
        $result = $action->handle(['sort_by' => 'title', 'sort_direction' => 'asc']);
        $titles = $result->pluck('title')->toArray();
        $this->assertEquals('A Course', $titles[0]);
        $this->assertEquals('M Course', $titles[1]);
        $this->assertEquals('Z Course', $titles[2]);

        // Sort descending
        $result = $action->handle(['sort_by' => 'title', 'sort_direction' => 'desc']);
        $titles = $result->pluck('title')->toArray();
        $this->assertEquals('Z Course', $titles[0]);
        $this->assertEquals('M Course', $titles[1]);
        $this->assertEquals('A Course', $titles[2]);
    }

    /**
     * Test that the action sorts by price.
     */
    public function test_sorts_by_price(): void
    {
        // Create courses with different prices
        Course::factory()->create(['title' => 'Expensive Course', 'price' => 100.00]);
        Course::factory()->create(['title' => 'Cheap Course', 'price' => 10.00]);
        Course::factory()->create(['title' => 'Mid-range Course', 'price' => 50.00]);

        $action = new GetCourseList();

        // Sort ascending
        $result = $action->handle(['sort_by' => 'price', 'sort_direction' => 'asc']);
        $titles = $result->pluck('title')->toArray();
        $this->assertEquals('Cheap Course', $titles[0]);
        $this->assertEquals('Mid-range Course', $titles[1]);
        $this->assertEquals('Expensive Course', $titles[2]);

        // Sort descending
        $result = $action->handle(['sort_by' => 'price', 'sort_direction' => 'desc']);
        $titles = $result->pluck('title')->toArray();
        $this->assertEquals('Expensive Course', $titles[0]);
        $this->assertEquals('Mid-range Course', $titles[1]);
        $this->assertEquals('Cheap Course', $titles[2]);
    }

    /**
     * Test that the action sorts by created date.
     */
    public function test_sorts_by_created_date(): void
    {
        // Create courses with different creation dates
        $oldCourse = Course::factory()->create(['title' => 'Old Course']);
        // Simulate a delay
        sleep(1);
        $newCourse = Course::factory()->create(['title' => 'New Course']);

        $action = new GetCourseList();

        // Sort ascending (oldest first)
        $result = $action->handle(['sort_by' => 'created_at', 'sort_direction' => 'asc']);
        $titles = $result->pluck('title')->toArray();
        $this->assertEquals('Old Course', $titles[0]);
        $this->assertEquals('New Course', $titles[1]);

        // Sort descending (newest first)
        $result = $action->handle(['sort_by' => 'created_at', 'sort_direction' => 'desc']);
        $titles = $result->pluck('title')->toArray();
        $this->assertEquals('New Course', $titles[0]);
        $this->assertEquals('Old Course', $titles[1]);
    }

    /**
     * Test that the action returns a paginator.
     */
    public function test_returns_paginator(): void
    {
        // Create a course
        Course::factory()->create();

        $action = new GetCourseList();
        $result = $action->handle([]);

        $this->assertInstanceOf(\Illuminate\Pagination\LengthAwarePaginator::class, $result);
    }

    /**
     * Test that the action includes categories when requested.
     */
    public function test_includes_categories_when_requested(): void
    {
        // Create a course
        Course::factory()->create();

        $action = new GetCourseList();
        $result = $action->handle(['with' => ['categories']]);

        foreach ($result as $course) {
            $this->assertTrue($course->relationLoaded('categories'));
        }
    }

    /**
     * Test that the action can search by title.
     */
    public function test_can_search_by_title(): void
    {
        // Create courses with specific titles
        Course::factory()->create(['title' => 'PHP Programming Course']);
        Course::factory()->create(['title' => 'JavaScript Basics']);
        Course::factory()->create(['title' => 'Advanced PHP Techniques']);

        $action = new GetCourseList();

        // Search for PHP courses
        $result = $action->handle(['search' => 'PHP']);
        $this->assertInstanceOf(\Illuminate\Pagination\LengthAwarePaginator::class, $result);
        $this->assertEquals(2, $result->total());
        $titles = collect($result->items())->pluck('title')->toArray();
        $this->assertContains('PHP Programming Course', $titles);
        $this->assertContains('Advanced PHP Techniques', $titles);
        $this->assertNotContains('JavaScript Basics', $titles);
    }

    /**
     * Test that the action can search by description.
     */
    public function test_can_search_by_description(): void
    {
        // Create courses with specific descriptions
        Course::factory()->create([
            'title' => 'Course 1',
            'description' => 'Learn about database design'
        ]);
        Course::factory()->create([
            'title' => 'Course 2',
            'description' => 'Web development fundamentals'
        ]);
        Course::factory()->create([
            'title' => 'Course 3',
            'description' => 'Advanced database techniques'
        ]);

        $action = new GetCourseList();

        // Search for database courses
        $result = $action->handle(['search' => 'database']);
        $this->assertInstanceOf(\Illuminate\Pagination\LengthAwarePaginator::class, $result);
        $this->assertEquals(2, $result->total());
        $titles = collect($result->items())->pluck('title')->toArray();
        $this->assertContains('Course 1', $titles);
        $this->assertContains('Course 3', $titles);
        $this->assertNotContains('Course 2', $titles);
    }
}
