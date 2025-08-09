<?php

namespace Modules\Course\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Modules\Base\Traits\CamelCasing;
use Modules\Course\Database\Factories\CourseCategoryFactory;

class CourseCategory extends Model
{
    use CamelCasing, SoftDeletes, HasFactory;

    protected $fillable = [
        'name',
        'slug',
        'description',
        'color',
        'is_active',
    ];

    protected $casts = [
        'is_active' => 'boolean',
    ];

    /**
     * The courses that belong to the category.
     */
    public function courses(): BelongsToMany
    {
        return $this->belongsToMany(Course::class, 'course_course_category');
    }

    /**
     * Scope a query to only include active categories.
     */
    public function scopeActive($query)
    {
        return $query->where('is_active', true);
    }

    /**
     * Scope a query to order categories by name.
     */
    public function scopeOrdered($query)
    {
        return $query->orderBy('name');
    }

    /**
     * Get the count of courses in this category.
     */
    public function getCoursesCount(): int
    {
        return $this->courses()->count();
    }

    /**
     * Get the count of active courses in this category.
     */
    public function getActiveCoursesCount(): int
    {
        return $this->courses()->whereNull('deleted_at')->count();
    }

    /**
     * Create a new factory instance for the model.
     */
    protected static function newFactory()
    {
        return CourseCategoryFactory::new();
    }
}
