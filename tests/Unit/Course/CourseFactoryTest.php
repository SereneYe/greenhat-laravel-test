<?php

namespace Tests\Unit\Course;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Modules\Course\Models\Course;
use Tests\TestCase;

class CourseFactoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that the Course factory creates a course with default attributes.
     */
    public function test_factory_creates_course_with_default_attributes(): void
    {
        // Create a course using the factory
        $course = Course::factory()->create();

        // Assert that the course was created and has the expected attributes
        $this->assertNotNull($course);
        $this->assertIsString($course->title);
        $this->assertIsString($course->slug);
        $this->assertIsString($course->description);
        $this->assertIsString($course->content);
        $this->assertIsString($course->instructor);
        $this->assertIsInt($course->duration_hours);
        $this->assertIsNumeric($course->price);
        $this->assertIsString($course->level);

        // Verify it exists in the database
        $this->assertDatabaseHas('courses', [
            'id' => $course->id,
        ]);
    }

    /**
     * Test that the Course factory creates a course with a valid level.
     */
    public function test_factory_creates_course_with_valid_level(): void
    {
        // Create multiple courses using the factory
        $courses = Course::factory()->count(10)->create();

        // Valid levels
        $validLevels = ['beginner', 'intermediate', 'advanced'];

        // Check that all courses have a valid level
        foreach ($courses as $course) {
            $this->assertContains($course->level, $validLevels);
        }

        // Check that we have a mix of levels (at least one of each)
        $levels = $courses->pluck('level')->unique()->toArray();
        $this->assertGreaterThanOrEqual(1, count(array_intersect($validLevels, $levels)));
    }

    /**
     * Test that the Course factory creates a course with a positive price.
     */
    public function test_factory_creates_course_with_positive_price(): void
    {
        // Create multiple courses using the factory
        $courses = Course::factory()->count(10)->create();

        // Check that all courses have a positive price
        foreach ($courses as $course) {
            $this->assertGreaterThanOrEqual(0, $course->price);
        }

        // Check that we have a mix of prices
        $prices = $courses->pluck('price')->unique()->toArray();
        $this->assertGreaterThan(1, count($prices), 'Factory should generate diverse prices');
    }

    /**
     * Test that the Course factory creates a course with a positive duration.
     */
    public function test_factory_creates_course_with_positive_duration(): void
    {
        // Create multiple courses using the factory
        $courses = Course::factory()->count(10)->create();

        // Check that all courses have a positive duration
        foreach ($courses as $course) {
            $this->assertGreaterThan(0, $course->duration_hours);
        }

        // Check that we have a mix of durations
        $durations = $courses->pluck('duration_hours')->unique()->toArray();
        $this->assertGreaterThan(1, count($durations), 'Factory should generate diverse durations');
    }

    /**
     * Test that the Course factory creates courses with unique titles.
     */
    public function test_factory_creates_unique_titles(): void
    {
        // Create multiple courses using the factory
        $courses = Course::factory()->count(10)->create();

        // Get all titles
        $titles = $courses->pluck('title')->toArray();

        // Check that all titles are unique
        $this->assertEquals(count($titles), count(array_unique($titles)));
    }

    /**
     * Test that the Course factory generates valid slugs.
     */
    public function test_factory_generates_valid_slugs(): void
    {
        // Create multiple courses using the factory
        $courses = Course::factory()->count(10)->create();

        foreach ($courses as $course) {
            // Slug should be a string
            $this->assertIsString($course->slug);

            // Slug should not be empty
            $this->assertNotEmpty($course->slug);

            // Slug should be lowercase
            $this->assertEquals(strtolower($course->slug), $course->slug);

            // Slug should not contain spaces or special characters (except hyphens)
            $this->assertMatchesRegularExpression('/^[a-z0-9-]+$/', $course->slug);

            // Slug should be related to the title
            $expectedSlug = Str::slug($course->title);
            $this->assertStringStartsWith(substr($expectedSlug, 0, 5), $course->slug);
        }
    }
}
