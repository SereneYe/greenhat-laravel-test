<?php

namespace Modules\Course\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Modules\Base\Traits\CamelCasing;
use Modules\Employee\Models\Employee;
use Modules\Media\Models\FilamentMediaLibrary;

class Course extends Model
{
    use CamelCasing, SoftDeletes;

    protected $fillable = [
        'title',
        'slug',
        'description',
        'content',
        'instructor',
        'duration_hours',
        'price',
        'level',
        'cover_media_id',
    ];

    protected $casts = [
        'duration_hours' => 'integer',
        'price' => 'decimal:2',
    ];

    /**
     * The categories that belong to the course.
     */
    public function categories(): BelongsToMany
    {
        return $this->belongsToMany(CourseCategory::class, 'course_course_category');
    }

    /**
     * Get the cover image for the course.
     */
    public function coverMedia(): BelongsTo
    {
        return $this->belongsTo(FilamentMediaLibrary::class, 'cover_media_id');
    }

    /**
     * Get the enrollments for the course.
     */
    public function enrollments(): HasMany
    {
        return $this->hasMany(CourseEnrollment::class);
    }

    /**
     * The employees that are enrolled in the course.
     */
    public function employees(): BelongsToMany
    {
        return $this->belongsToMany(Employee::class, 'course_enrollments')
            ->withPivot('enrolled_at', 'cancelled_at')
            ->withTimestamps();
    }

    /**
     * Scope a query to filter courses by level.
     */
    public function scopeByLevel($query, $level)
    {
        return $query->where('level', $level);
    }

    /**
     * Scope a query to filter courses by categories.
     */
    public function scopeByCategories($query, $categoryIds)
    {
        return $query->whereHas('categories', function ($query) use ($categoryIds) {
            $query->whereIn('course_categories.id', (array) $categoryIds);
        });
    }

    /**
     * Scope a query to order courses by newest first.
     */
    public function scopeOrderByNewest($query)
    {
        return $query->orderBy('created_at', 'desc');
    }

    /**
     * Get the enrollment count for the course.
     */
    public function getEnrollmentCount(): int
    {
        return $this->enrollments()->whereNull('cancelled_at')->count();
    }
}
