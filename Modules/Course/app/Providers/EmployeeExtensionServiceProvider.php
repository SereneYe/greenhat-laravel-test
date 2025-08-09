<?php

namespace Modules\Course\Providers;

use Illuminate\Support\ServiceProvider;
use Modules\Course\Traits\HasCourseEnrollments;
use Modules\Employee\Models\Employee;

class EmployeeExtensionServiceProvider extends ServiceProvider
{
    /**
     * Register the service provider.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // Extend the Employee model with course-related methods using mixins
        Employee::mixin(new class {
            public function courseEnrollments()
            {
                return function () {
                    return $this->hasMany(\Modules\Course\Models\CourseEnrollment::class, 'employee_id');
                };
            }

            public function courses()
            {
                return function () {
                    return $this->belongsToMany(\Modules\Course\Models\Course::class, 'course_enrollments', 'employee_id', 'course_id')
                        ->withPivot('enrolled_at', 'cancelled_at')
                        ->withTimestamps();
                };
            }

            public function enrollInCourse()
            {
                return function (\Modules\Course\Models\Course $course) {
                    $enrollment = $this->courseEnrollments()->firstOrCreate(
                        ['course_id' => $course->id],
                        ['enrolled_at' => now()]
                    );

                    return $enrollment;
                };
            }

            public function isEnrolledIn()
            {
                return function (\Modules\Course\Models\Course $course) {
                    return $this->courseEnrollments()
                        ->where('course_id', $course->id)
                        ->whereNull('cancelled_at')
                        ->exists();
                };
            }

            public function getTotalLearningHours()
            {
                return function () {
                    return $this->courses()
                        ->whereNull('course_enrollments.cancelled_at')
                        ->sum('duration_hours');
                };
            }
        });
    }
}
