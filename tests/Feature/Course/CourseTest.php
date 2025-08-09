<?php

namespace Tests\Feature\Course;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Modules\Course\Models\Course;
use Modules\Course\Models\CourseCategory;
use Modules\Media\Models\FilamentMediaLibrary;
use Tests\TestCase;

class CourseTest extends TestCase
{
    use RefreshDatabase;

    /**
     * createTestMedia method - empty implementation as Cover Media functionality has been removed.
     *
     * @param array $attributes Override default attributes
     * @return object Mock object that can be used in place of FilamentMediaLibrary
     */
    protected function createTestMedia(array $attributes = []): object
    {
        // Return a simple mock object instead of null to maintain compatibility
        return (object)['id' => null];
    }

    /**
     * Test creating a course with valid data.
     */
    public function test_can_create_course_with_valid_data(): void
    {
        $data = [
            'title' => 'Test Course',
            'description' => 'This is a test course',
            'content' => 'Course content goes here',
            'instructor' => 'John Doe',
            'duration_hours' => 10,
            'price' => 99.99,
            'level' => 'beginner',
            'categories' => [],
        ];

        $response = $this->postJson('/api/v1/courses', $data);

        $response->assertStatus(200)
            ->assertJsonStructure([
                'data' => [
                    'id',
                    'title',
                    'slug',
                    'description',
                    'content',
                    'instructor',
                    'durationHours',
                    'price',
                    'level',
                    'coverMediaId',
                    'createdAt',
                    'updatedAt',
                ]
            ])
            ->assertJson([
                'data' => [
                    'title' => 'Test Course',
                    'slug' => 'test-course',
                    'description' => 'This is a test course',
                    'content' => 'Course content goes here',
                    'instructor' => 'John Doe',
                    'durationHours' => 10,
                    'price' => 99.99,
                    'level' => 'beginner',
                ]
            ]);

        $this->assertDatabaseHas('courses', [
            'title' => 'Test Course',
            'slug' => 'test-course',
            'description' => 'This is a test course',
            'content' => 'Course content goes here',
            'instructor' => 'John Doe',
            'duration_hours' => 10,
            'price' => 99.99,
            'level' => 'beginner',
        ]);
    }

    /**
     * Test creating a course with categories.
     */
    public function test_can_create_course_with_categories(): void
    {
        // Create categories first
        $category1 = CourseCategory::factory()->create();
        $category2 = CourseCategory::factory()->create();

        $data = [
            'title' => 'Course With Categories',
            'description' => 'This course has categories',
            'content' => 'Course content',
            'instructor' => 'Jane Smith',
            'duration_hours' => 15,
            'price' => 149.99,
            'level' => 'intermediate',
            'categories' => [$category1->id, $category2->id],
        ];

        $response = $this->postJson('/api/v1/courses', $data);

        $response->assertStatus(200);

        $courseId = $response->json('data.id');

        // Check that the categories were attached
        $this->assertDatabaseHas('course_course_category', [
            'course_id' => $courseId,
            'course_category_id' => $category1->id,
        ]);

        $this->assertDatabaseHas('course_course_category', [
            'course_id' => $courseId,
            'course_category_id' => $category2->id,
        ]);
    }

    /**
     * Test creating a course without cover media.
     */
    public function test_can_create_course_without_cover_media(): void
    {
        $data = [
            'title' => 'Course Without Cover',
            'description' => 'This course has no cover image',
            'content' => 'Course content',
            'instructor' => 'Media Instructor',
            'duration_hours' => 8,
            'price' => 79.99,
            'level' => 'beginner',
            'categories' => [],
        ];

        $response = $this->postJson('/api/v1/courses', $data);

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'title' => 'Course Without Cover',
                ]
            ]);

        $this->assertDatabaseHas('courses', [
            'title' => 'Course Without Cover',
        ]);
    }

    /**
     * Test that a course cannot be created with a duplicate title.
     */
    public function test_cannot_create_course_with_duplicate_title(): void
    {
        // Create a course first
        Course::create([
            'title' => 'Existing Course',
            'slug' => 'existing-course',
            'duration_hours' => 10,
            'price' => 99.99,
            'level' => 'beginner',
        ]);

        // Try to create another with the same title
        $data = [
            'title' => 'Existing Course',
            'description' => 'This should fail',
            'duration_hours' => 5,
            'price' => 49.99,
            'level' => 'beginner',
            'categories' => [],
        ];

        $response = $this->postJson('/api/v1/courses', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title']);
    }

    /**
     * Test that a course cannot be created with invalid categories.
     */
    public function test_cannot_create_course_with_invalid_categories(): void
    {
        $data = [
            'title' => 'Invalid Categories Course',
            'description' => 'This should fail',
            'duration_hours' => 5,
            'price' => 49.99,
            'level' => 'beginner',
            'categories' => [999, 1000], // Non-existent category IDs
        ];

        $response = $this->postJson('/api/v1/courses', $data);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['categories.0', 'categories.1']);
    }

    /**
     * Test that a course can be created with additional fields.
     */
    public function test_can_create_course_with_additional_fields(): void
    {
        $data = [
            'title' => 'Course With Additional Fields',
            'description' => 'This course has additional fields',
            'content' => 'Detailed course content',
            'instructor' => 'Expert Instructor',
            'duration_hours' => 5,
            'price' => 49.99,
            'level' => 'beginner',
            'categories' => [],
            'extra_field' => 'This should be ignored'
        ];

        $response = $this->postJson('/api/v1/courses', $data);

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'title' => 'Course With Additional Fields',
                    'description' => 'This course has additional fields',
                ]
            ]);

        $this->assertDatabaseHas('courses', [
            'title' => 'Course With Additional Fields',
            'description' => 'This course has additional fields',
        ]);
    }

    /**
     * Test updating a course.
     */
    public function test_can_update_course(): void
    {
        $course = Course::create([
            'title' => 'Original Title',
            'slug' => 'original-title',
            'description' => 'Original description',
            'content' => 'Original content',
            'instructor' => 'Original Instructor',
            'duration_hours' => 10,
            'price' => 99.99,
            'level' => 'beginner',
        ]);

        $data = [
            'title' => 'Updated Title',
            'description' => 'Updated description',
            'content' => 'Updated content',
            'instructor' => 'Updated Instructor',
            'duration_hours' => 15,
            'price' => 149.99,
            'level' => 'intermediate',
            'categories' => [],
        ];

        $response = $this->putJson("/api/v1/courses/{$course->id}", $data);

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'title' => 'Updated Title',
                    'slug' => 'updated-title',
                    'description' => 'Updated description',
                    'content' => 'Updated content',
                    'instructor' => 'Updated Instructor',
                    'durationHours' => 15,
                    'price' => 149.99,
                    'level' => 'intermediate',
                ]
            ]);

        $this->assertDatabaseHas('courses', [
            'id' => $course->id,
            'title' => 'Updated Title',
            'slug' => 'updated-title',
            'description' => 'Updated description',
            'content' => 'Updated content',
            'instructor' => 'Updated Instructor',
            'duration_hours' => 15,
            'price' => 149.99,
            'level' => 'intermediate',
        ]);
    }

    /**
     * Test updating course categories.
     */
    public function test_can_update_course_categories(): void
    {
        // Create a course and categories
        $course = Course::create([
            'title' => 'Course for Category Update',
            'slug' => 'course-for-category-update',
            'duration_hours' => 10,
            'price' => 99.99,
            'level' => 'beginner',
        ]);

        $category1 = CourseCategory::factory()->create();
        $category2 = CourseCategory::factory()->create();
        $category3 = CourseCategory::factory()->create();

        // Initially attach category1
        $course->categories()->attach($category1->id);

        // Update to use category2 and category3 instead
        $data = [
            'title' => 'Course for Category Update',
            'duration_hours' => 10,
            'price' => 99.99,
            'level' => 'beginner',
            'categories' => [$category2->id, $category3->id],
        ];

        $response = $this->putJson("/api/v1/courses/{$course->id}", $data);

        $response->assertStatus(200);

        // Check that category1 is no longer attached
        $this->assertDatabaseMissing('course_course_category', [
            'course_id' => $course->id,
            'course_category_id' => $category1->id,
        ]);

        // Check that category2 and category3 are now attached
        $this->assertDatabaseHas('course_course_category', [
            'course_id' => $course->id,
            'course_category_id' => $category2->id,
        ]);

        $this->assertDatabaseHas('course_course_category', [
            'course_id' => $course->id,
            'course_category_id' => $category3->id,
        ]);
    }

    /**
     * Test updating course with partial data.
     */
    public function test_can_update_course_with_partial_data(): void
    {
        // Create a course
        $course = Course::create([
            'title' => 'Course for Partial Update',
            'slug' => 'course-for-partial-update',
            'description' => 'Original description',
            'duration_hours' => 10,
            'price' => 99.99,
            'level' => 'beginner',
        ]);

        // Update only the description
        $data = [
            'description' => 'Updated description',
        ];

        $response = $this->putJson("/api/v1/courses/{$course->id}", $data);

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'title' => 'Course for Partial Update',
                    'description' => 'Updated description',
                    'durationHours' => 10,
                    'price' => 99.99,
                    'level' => 'beginner',
                ]
            ]);

        $this->assertDatabaseHas('courses', [
            'id' => $course->id,
            'title' => 'Course for Partial Update',
            'description' => 'Updated description',
            'duration_hours' => 10,
            'price' => 99.99,
            'level' => 'beginner',
        ]);
    }

    /**
     * Test deleting a course.
     */
    public function test_can_delete_course(): void
    {
        $course = Course::create([
            'title' => 'Course to Delete',
            'slug' => 'course-to-delete',
            'duration_hours' => 10,
            'price' => 99.99,
            'level' => 'beginner',
        ]);

        $response = $this->deleteJson("/api/v1/courses/{$course->id}");

        $response->assertStatus(200)
            ->assertJson([
                'message' => 'Course deleted successfully',
            ]);

        // Since we're using soft deletes, the record should still exist but with a deleted_at timestamp
        $this->assertSoftDeleted('courses', [
            'id' => $course->id,
        ]);
    }

    /**
     * Test getting a course by ID.
     */
    public function test_can_get_course_by_id(): void
    {
        $course = Course::create([
            'title' => 'Test Course',
            'slug' => 'test-course',
            'description' => 'Test description',
            'content' => 'Test content',
            'instructor' => 'Test Instructor',
            'duration_hours' => 10,
            'price' => 99.99,
            'level' => 'beginner',
        ]);

        $response = $this->getJson("/api/v1/courses/{$course->id}");

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $course->id,
                    'title' => 'Test Course',
                    'slug' => 'test-course',
                    'description' => 'Test description',
                    'content' => 'Test content',
                    'instructor' => 'Test Instructor',
                    'durationHours' => 10,
                    'price' => 99.99,
                    'level' => 'beginner',
                ]
            ]);
    }

    /**
     * Test getting a course by slug.
     */
    public function test_can_get_course_by_slug(): void
    {
        $course = Course::create([
            'title' => 'Slug Test Course',
            'slug' => 'slug-test-course',
            'description' => 'Test description',
            'content' => 'Test content',
            'instructor' => 'Test Instructor',
            'duration_hours' => 10,
            'price' => 99.99,
            'level' => 'beginner',
        ]);

        $response = $this->getJson("/api/v1/courses/slug/slug-test-course");

        $response->assertStatus(200)
            ->assertJson([
                'data' => [
                    'id' => $course->id,
                    'title' => 'Slug Test Course',
                    'slug' => 'slug-test-course',
                ]
            ]);
    }

    /**
     * Test filtering courses by level.
     */
    public function test_can_filter_courses_by_level(): void
    {
        // Create courses with different levels
        Course::factory()->count(3)->create(['level' => 'beginner']);
        Course::factory()->count(2)->create(['level' => 'intermediate']);
        Course::factory()->count(1)->create(['level' => 'advanced']);

        $response = $this->getJson('/api/v1/courses?level=beginner');

        $response->assertStatus(200);

        // All returned courses should be beginner level
        foreach ($response->json('data') as $course) {
            $this->assertEquals('beginner', $course['level']);
        }
    }

    /**
     * Test filtering courses by categories.
     */
    public function test_can_filter_courses_by_categories(): void
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

        // Filter by category1
        $response = $this->getJson("/api/v1/courses?categories[]={$category1->id}");

        $response->assertStatus(200);

        // Should return course1 and course3
        $courseIds = collect($response->json('data'))->pluck('id')->toArray();
        $this->assertContains($course1->id, $courseIds);
        $this->assertContains($course3->id, $courseIds);
        $this->assertNotContains($course2->id, $courseIds);
    }

    /**
     * Test filtering courses by price range.
     */
    public function test_can_filter_courses_by_price_range(): void
    {
        // Create courses with different prices
        Course::factory()->create(['title' => 'Cheap Course', 'price' => 10.00]);
        Course::factory()->create(['title' => 'Mid-range Course', 'price' => 50.00]);
        Course::factory()->create(['title' => 'Expensive Course', 'price' => 100.00]);

        // Filter by min price
        $response = $this->getJson('/api/v1/courses?min_price=40');

        $response->assertStatus(200);

        // Should only return courses with price >= 40
        $titles = collect($response->json('data'))->pluck('title')->toArray();
        $this->assertContains('Mid-range Course', $titles);
        $this->assertContains('Expensive Course', $titles);
        $this->assertNotContains('Cheap Course', $titles);

        // Filter by max price
        $response = $this->getJson('/api/v1/courses?max_price=60');

        $response->assertStatus(200);

        // Should only return courses with price <= 60
        $titles = collect($response->json('data'))->pluck('title')->toArray();
        $this->assertContains('Cheap Course', $titles);
        $this->assertContains('Mid-range Course', $titles);
        $this->assertNotContains('Expensive Course', $titles);

        // Filter by price range
        $response = $this->getJson('/api/v1/courses?min_price=20&max_price=80');

        $response->assertStatus(200);

        // Should only return courses with 20 <= price <= 80
        $titles = collect($response->json('data'))->pluck('title')->toArray();
        $this->assertNotContains('Cheap Course', $titles);
        $this->assertContains('Mid-range Course', $titles);
        $this->assertNotContains('Expensive Course', $titles);
    }

    /**
     * Test filtering courses by duration.
     */
    public function test_can_filter_courses_by_duration(): void
    {
        // Create courses with different durations
        Course::factory()->create(['title' => 'Short Course', 'duration_hours' => 3]);
        Course::factory()->create(['title' => 'Medium Course', 'duration_hours' => 10]);
        Course::factory()->create(['title' => 'Long Course', 'duration_hours' => 30]);

        // Filter by min duration
        $response = $this->getJson('/api/v1/courses?min_duration=8');

        $response->assertStatus(200);

        // Should only return courses with duration >= 8
        $titles = collect($response->json('data'))->pluck('title')->toArray();
        $this->assertContains('Medium Course', $titles);
        $this->assertContains('Long Course', $titles);
        $this->assertNotContains('Short Course', $titles);

        // Filter by max duration
        $response = $this->getJson('/api/v1/courses?max_duration=15');

        $response->assertStatus(200);

        // Should only return courses with duration <= 15
        $titles = collect($response->json('data'))->pluck('title')->toArray();
        $this->assertContains('Short Course', $titles);
        $this->assertContains('Medium Course', $titles);
        $this->assertNotContains('Long Course', $titles);
    }

    /**
     * Test sorting courses by title.
     */
    public function test_can_sort_courses_by_title(): void
    {
        // Create courses with specific titles to test sorting
        Course::factory()->create(['title' => 'Z Course']);
        Course::factory()->create(['title' => 'A Course']);
        Course::factory()->create(['title' => 'M Course']);

        // Test ascending sort
        $response = $this->getJson('/api/v1/courses?sort_by=title&sort_direction=asc');
        $response->assertStatus(200);

        $courses = $response->json('data');
        $this->assertEquals('A Course', $courses[0]['title']);

        // Test descending sort
        $response = $this->getJson('/api/v1/courses?sort_by=title&sort_direction=desc');
        $response->assertStatus(200);

        $courses = $response->json('data');
        $this->assertEquals('Z Course', $courses[0]['title']);
    }

    /**
     * Test sorting courses by price.
     */
    public function test_can_sort_courses_by_price(): void
    {
        // Create courses with different prices
        Course::factory()->create(['title' => 'Expensive Course', 'price' => 100.00]);
        Course::factory()->create(['title' => 'Cheap Course', 'price' => 10.00]);
        Course::factory()->create(['title' => 'Mid-range Course', 'price' => 50.00]);

        // Test ascending sort
        $response = $this->getJson('/api/v1/courses?sort_by=price&sort_direction=asc');
        $response->assertStatus(200);

        $courses = $response->json('data');
        $this->assertEquals('Cheap Course', $courses[0]['title']);

        // Test descending sort
        $response = $this->getJson('/api/v1/courses?sort_by=price&sort_direction=desc');
        $response->assertStatus(200);

        $courses = $response->json('data');
        $this->assertEquals('Expensive Course', $courses[0]['title']);
    }

    /**
     * Test sorting courses by created date.
     */
    public function test_can_sort_courses_by_created_date(): void
    {
        // Create courses with different creation dates
        $oldCourse = Course::factory()->create(['title' => 'Old Course']);
        // Simulate a delay
        sleep(1);
        $newCourse = Course::factory()->create(['title' => 'New Course']);

        // Test ascending sort (oldest first)
        $response = $this->getJson('/api/v1/courses?sort_by=created_at&sort_direction=asc');
        $response->assertStatus(200);

        $courses = $response->json('data');
        $this->assertEquals('Old Course', $courses[0]['title']);

        // Test descending sort (newest first)
        $response = $this->getJson('/api/v1/courses?sort_by=created_at&sort_direction=desc');
        $response->assertStatus(200);

        $courses = $response->json('data');
        $this->assertEquals('New Course', $courses[0]['title']);
    }

    /**
     * Test that a 404 is returned for a non-existent course.
     */
    public function test_returns_404_for_non_existent_course(): void
    {
        $response = $this->getJson('/api/v1/courses/999');
        $response->assertStatus(404);
    }

    /**
     * Test that title is required when creating a course.
     */
    public function test_title_is_required(): void
    {
        $response = $this->postJson('/api/v1/courses', [
            'description' => 'Missing title',
            'duration_hours' => 10,
            'price' => 99.99,
            'level' => 'beginner',
            'categories' => [],
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['title']);
    }

    /**
     * Test that price is required and must be numeric.
     */
    public function test_price_is_required_and_numeric(): void
    {
        // Test missing price
        $response = $this->postJson('/api/v1/courses', [
            'title' => 'Price Test Course',
            'duration_hours' => 10,
            'level' => 'beginner',
            'categories' => [],
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['price']);

        // Test non-numeric price
        $response = $this->postJson('/api/v1/courses', [
            'title' => 'Price Test Course',
            'duration_hours' => 10,
            'price' => 'not-a-number',
            'level' => 'beginner',
            'categories' => [],
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['price']);
    }

    /**
     * Test that duration_hours is required and must be an integer.
     */
    public function test_duration_hours_is_required_and_integer(): void
    {
        // Test missing duration_hours
        $response = $this->postJson('/api/v1/courses', [
            'title' => 'Duration Test Course',
            'price' => 99.99,
            'level' => 'beginner',
            'categories' => [],
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['duration_hours']);

        // Test non-integer duration_hours
        $response = $this->postJson('/api/v1/courses', [
            'title' => 'Duration Test Course',
            'duration_hours' => 'not-an-integer',
            'price' => 99.99,
            'level' => 'beginner',
            'categories' => [],
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['duration_hours']);
    }

    /**
     * Test that level must be a valid enum value.
     */
    public function test_level_must_be_valid_enum_value(): void
    {
        $response = $this->postJson('/api/v1/courses', [
            'title' => 'Level Test Course',
            'duration_hours' => 10,
            'price' => 99.99,
            'level' => 'invalid-level',
            'categories' => [],
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['level']);
    }

    /**
     * Test that categories must be an array when provided.
     */
    public function test_categories_must_be_array(): void
    {
        // Test non-array categories
        $response = $this->postJson('/api/v1/courses', [
            'title' => 'Categories Test Course',
            'duration_hours' => 10,
            'price' => 99.99,
            'level' => 'beginner',
            'categories' => 'not-an-array',
        ]);

        $response->assertStatus(422)
            ->assertJsonValidationErrors(['categories']);
    }
}
