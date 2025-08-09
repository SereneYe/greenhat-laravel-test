<?php

namespace Modules\Course\Traits;

use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Modules\Course\Models\Course;
use Modules\Course\Models\CourseEnrollment;

trait HasCourseEnrollments
{
    /**
     * Get the course enrollments for the employee.
     */
    public function courseEnrollments(): HasMany
    {
        return $this->hasMany(CourseEnrollment::class, 'employee_id');
    }

    /**
     * Get the courses that the employee is enrolled in.
     */
    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'course_enrollments', 'employee_id', 'course_id')
            ->withPivot('enrolled_at', 'cancelled_at')
            ->withTimestamps();
    }

    /**
     * Enroll the employee in a course.
     */
    public function enrollInCourse(Course $course): CourseEnrollment
    {
        $enrollment = $this->courseEnrollments()->firstOrCreate(
            ['course_id' => $course->id],
            ['enrolled_at' => now()]
        );

        return $enrollment;
    }

    /**
     * Check if the employee is enrolled in a course.
     */
    public function isEnrolledIn(Course $course): bool
    {
        return $this->courseEnrollments()
            ->where('course_id', $course->id)
            ->whereNull('cancelled_at')
            ->exists();
    }

    /**
     * Get the total learning hours for the employee.
     */
    public function getTotalLearningHours(): int
    {
        return $this->courses()
            ->whereNull('course_enrollments.cancelled_at')
            ->sum('duration_hours');
    }
}
