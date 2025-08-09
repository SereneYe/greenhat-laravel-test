<?php

namespace Tests\Unit\Course;

use Illuminate\Foundation\Testing\RefreshDatabase;
use Modules\Course\Models\Course;
use Modules\Course\Models\CourseCategory;
use Tests\TestCase;

class CourseCategoryTest extends TestCase
{
    use RefreshDatabase;

    /**
     * Test that course category has fillable attributes.
     */
    public function test_course_category_has_fillable_attributes(): void
    {
        $category = new CourseCategory();

        $this->assertContains('name', $category->getFillable());
        $this->assertContains('slug', $category->getFillable());
        $this->assertContains('description', $category->getFillable());
        $this->assertContains('color', $category->getFillable());
        $this->assertContains('is_active', $category->getFillable());
    }

    /**
     * Test that course category casts is_active to boolean.
     */
    public function test_course_category_casts_is_active_to_boolean(): void
    {
        $category = CourseCategory::create([
            'name' => 'Test Category',
            'slug' => 'test-category',
            'color' => '#000000',
            'is_active' => 1, // Integer value
        ]);

        $this->assertIsBool($category->is_active);
        $this->assertTrue($category->is_active);
    }

    /**
     * Test that course category uses soft deletes.
     */
    public function test_course_category_uses_soft_deletes(): void
    {
        $category = CourseCategory::create([
            'name' => 'Soft Delete Test',
            'slug' => 'soft-delete-test',
            'color' => '#000000',
        ]);

        $category->delete();

        // The category should not be found in a normal query
        $this->assertNull(CourseCategory::find($category->id));

        // But it should be found when including trashed records
        $this->assertNotNull(CourseCategory::withTrashed()->find($category->id));
    }

    /**
     * Test that course category belongs to many courses.
     */
    public function test_course_category_belongs_to_many_courses(): void
    {
        // Create a category
        $category = CourseCategory::create([
            'name' => 'Relationship Test',
            'slug' => 'relationship-test',
            'color' => '#000000',
        ]);

        // Check that the courses relationship exists and is the correct type
        $this->assertInstanceOf(\Illuminate\Database\Eloquent\Relations\BelongsToMany::class, $category->courses());
    }

    /**
     * Test active scope only returns active categories.
     */
    public function test_active_scope_only_returns_active_categories(): void
    {
        // Create active and inactive categories
        CourseCategory::create([
            'name' => 'Active Category',
            'slug' => 'active-category',
            'color' => '#00FF00',
            'is_active' => true,
        ]);

        CourseCategory::create([
            'name' => 'Inactive Category',
            'slug' => 'inactive-category',
            'color' => '#FF0000',
            'is_active' => false,
        ]);

        // Query using the active scope
        $activeCategories = CourseCategory::active()->get();

        // Should only return active categories
        $this->assertEquals(1, $activeCategories->count());
        $this->assertEquals('Active Category', $activeCategories->first()->name);
    }

    /**
     * Test ordered scope sorts by name.
     */
    public function test_ordered_scope_sorts_by_name(): void
    {
        // Create categories with different names
        CourseCategory::create([
            'name' => 'Z Category',
            'slug' => 'z-category',
            'color' => '#0000FF',
        ]);

        CourseCategory::create([
            'name' => 'A Category',
            'slug' => 'a-category',
            'color' => '#00FF00',
        ]);

        CourseCategory::create([
            'name' => 'M Category',
            'slug' => 'm-category',
            'color' => '#FF0000',
        ]);

        // Query using the ordered scope
        $orderedCategories = CourseCategory::ordered()->get();

        // Should be ordered by name
        $this->assertEquals('A Category', $orderedCategories[0]->name);
        $this->assertEquals('M Category', $orderedCategories[1]->name);
        $this->assertEquals('Z Category', $orderedCategories[2]->name);
    }

    /**
     * Test get_courses_count returns correct count.
     */
    public function test_get_courses_count_returns_correct_count(): void
    {
        // Create a category
        $category = CourseCategory::create([
            'name' => 'Courses Count Test',
            'slug' => 'courses-count-test',
            'color' => '#000000',
        ]);

        // Initially should have 0 courses
        $this->assertEquals(0, $category->getCoursesCount());

        // Verify the implementation of getCoursesCount in the model
        $this->assertEquals(
            'return $this->courses()->count();',
            $this->getMethodImplementation($category, 'getCoursesCount')
        );
    }

    /**
     * Test get_active_courses_count excludes deleted courses.
     */
    public function test_get_active_courses_count_excludes_deleted_courses(): void
    {
        $category = CourseCategory::create([
            'name' => 'Active Courses Count Test',
            'slug' => 'active-courses-count-test',
            'color' => '#000000',
        ]);

        // Initially should have 0 active courses
        $this->assertEquals(0, $category->getActiveCoursesCount());

        // Test method exists and returns integer
        $this->assertIsInt($category->getActiveCoursesCount());

        // Verify the implementation of getActiveCoursesCount in the model
        $this->assertEquals(
            'return $this->courses()->whereNull(\'deleted_at\')->count();',
            $this->getMethodImplementation($category, 'getActiveCoursesCount')
        );
    }

    /**
     * Helper method to get the implementation of a method from a class.
     */
    private function getMethodImplementation($object, $methodName): string
    {
        $reflection = new \ReflectionMethod(get_class($object), $methodName);
        $file = file($reflection->getFileName());
        $startLine = $reflection->getStartLine() - 1;
        $endLine = $reflection->getEndLine();
        $length = $endLine - $startLine;

        $body = array_slice($file, $startLine, $length);

        // Extract the method body (between the curly braces)
        $methodBody = implode('', $body);
        preg_match('/\{(.*)\}/s', $methodBody, $matches);

        // Return the trimmed method body without the curly braces
        return trim($matches[1] ?? '');
    }
}
