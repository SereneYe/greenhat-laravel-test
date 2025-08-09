<?php

namespace Modules\Course\Database\Seeders;

use Illuminate\Database\Seeder;

class CourseDatabaseSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // Call the CourseCategorySeeder to seed course categories
        $this->call(CourseCategorySeeder::class);

        // Additional seeders for the Course module can be added here
        $this->call(CourseSeeder::class);
        // $this->call(CourseEnrollmentSeeder::class);
    }
}
