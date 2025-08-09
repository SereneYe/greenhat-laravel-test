<?php

namespace Modules\Course\Database\Seeders;

use Illuminate\Database\Seeder;
use Modules\Course\Models\CourseCategory;
use Illuminate\Support\Str;

class CourseCategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            [
                'name' => 'Technical Skills',
                'description' => 'Programming, development, and technical competencies',
                'color' => '#3b82f6',
                'is_active' => true,
            ],
            [
                'name' => 'Leadership Development',
                'description' => 'Management and leadership training programs',
                'color' => '#ef4444',
                'is_active' => true,
            ],
            [
                'name' => 'Communication Skills',
                'description' => 'Effective communication and presentation skills',
                'color' => '#10b981',
                'is_active' => true,
            ],
            [
                'name' => 'Project Management',
                'description' => 'Project planning, execution, and delivery methodologies',
                'color' => '#f59e0b',
                'is_active' => true,
            ],
            [
                'name' => 'Personal Development',
                'description' => 'Self-improvement and personal growth courses',
                'color' => '#8b5cf6',
                'is_active' => true,
            ],
        ];

        foreach ($categories as $categoryData) {
            CourseCategory::updateOrCreate(
                ['name' => $categoryData['name']],
                array_merge($categoryData, [
                    'slug' => Str::slug($categoryData['name'])
                ])
            );
        }

        // Create additional test data
        CourseCategory::factory()->count(10)->create();
    }
}
