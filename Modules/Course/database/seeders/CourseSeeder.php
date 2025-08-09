<?php

namespace Modules\Course\Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Str;
use Modules\Course\Models\Course;
use Modules\Course\Models\CourseCategory;

class CourseSeeder extends Seeder
{
    public function run(): void
    {
        // Get existing categories
        $categories = CourseCategory::all();

        // Create predefined courses with specific attributes
        $courses = [
            // Beginner level courses
            [
                'title' => 'Introduction to Programming',
                'description' => 'A beginner-friendly course to learn programming fundamentals',
                'content' => 'This course covers basic programming concepts and syntax.',
                'instructor' => 'John Smith',
                'duration_hours' => 5,
                'price' => 49.99,
                'level' => 'beginner',
                'categories' => [1, 3], // Technical Skills, Communication Skills (assuming these IDs exist)
            ],
            [
                'title' => 'Communication Basics',
                'description' => 'Learn the fundamentals of effective communication',
                'content' => 'This course covers verbal and non-verbal communication techniques.',
                'instructor' => 'Sarah Johnson',
                'duration_hours' => 3,
                'price' => 29.99,
                'level' => 'beginner',
                'categories' => [3], // Communication Skills
            ],
            [
                'title' => 'Project Management Fundamentals',
                'description' => 'An introduction to project management principles',
                'content' => 'Learn the basics of project planning, execution, and delivery.',
                'instructor' => 'Michael Brown',
                'duration_hours' => 8,
                'price' => 79.99,
                'level' => 'beginner',
                'categories' => [4], // Project Management
            ],

            // Intermediate level courses
            [
                'title' => 'Advanced Web Development',
                'description' => 'Take your web development skills to the next level',
                'content' => 'This course covers advanced JavaScript, CSS, and HTML techniques.',
                'instructor' => 'Emily Chen',
                'duration_hours' => 15,
                'price' => 149.99,
                'level' => 'intermediate',
                'categories' => [1], // Technical Skills
            ],
            [
                'title' => 'Team Leadership',
                'description' => 'Develop essential skills for leading teams effectively',
                'content' => 'Learn how to motivate, delegate, and resolve conflicts within teams.',
                'instructor' => 'David Wilson',
                'duration_hours' => 12,
                'price' => 199.99,
                'level' => 'intermediate',
                'categories' => [2], // Leadership Development
            ],
            [
                'title' => 'Public Speaking Mastery',
                'description' => 'Improve your public speaking and presentation skills',
                'content' => 'This course covers advanced techniques for engaging audiences.',
                'instructor' => 'Lisa Rodriguez',
                'duration_hours' => 10,
                'price' => 129.99,
                'level' => 'intermediate',
                'categories' => [3], // Communication Skills
            ],

            // Advanced level courses
            [
                'title' => 'Enterprise Architecture',
                'description' => 'Master complex enterprise-level software architecture',
                'content' => 'This course covers advanced architectural patterns and practices.',
                'instructor' => 'Robert Zhang',
                'duration_hours' => 25,
                'price' => 299.99,
                'level' => 'advanced',
                'categories' => [1], // Technical Skills
            ],
            [
                'title' => 'Executive Leadership',
                'description' => 'Advanced leadership strategies for executives',
                'content' => 'Learn high-level leadership skills for organizational success.',
                'instructor' => 'Jennifer Adams',
                'duration_hours' => 20,
                'price' => 499.99,
                'level' => 'advanced',
                'categories' => [2, 5], // Leadership Development, Personal Development
            ],

            // Free course
            [
                'title' => 'Introduction to Time Management',
                'description' => 'Learn basic time management techniques',
                'content' => 'This free course covers essential time management strategies.',
                'instructor' => 'Thomas Lee',
                'duration_hours' => 2,
                'price' => 0.00,
                'level' => 'beginner',
                'categories' => [5], // Personal Development
            ],

            // Long duration course
            [
                'title' => 'Full Stack Development Bootcamp',
                'description' => 'Comprehensive training in full stack web development',
                'content' => 'This intensive bootcamp covers front-end and back-end technologies.',
                'instructor' => 'Alex Johnson',
                'duration_hours' => 80,
                'price' => 999.99,
                'level' => 'intermediate',
                'categories' => [1, 4], // Technical Skills, Project Management
            ],
        ];

        foreach ($courses as $courseData) {
            // Extract categories
            $categoryIds = $courseData['categories'] ?? [];
            unset($courseData['categories']);

            // Create or update the course
            $course = Course::updateOrCreate(
                ['title' => $courseData['title']],
                array_merge($courseData, [
                    'slug' => Str::slug($courseData['title'])
                ])
            );

            // 安全地附加分类（只附加存在的分类）
            if (!empty($categoryIds)) {
                $validCategoryIds = CourseCategory::whereIn('id', $categoryIds)->pluck('id')->toArray();
                if (!empty($validCategoryIds)) {
                    $course->categories()->sync($validCategoryIds);
                }
            }
        }

        // Create additional random courses using factory
        Course::factory()->count(20)->create()->each(function ($course) use ($categories) {
            // Attach 1-3 random categories to each course
            if ($categories->count() > 0) {
                $randomCategories = $categories->random(rand(1, min(3, $categories->count())))->pluck('id');
                $course->categories()->attach($randomCategories);
            }
        });
    }
}
