<?php

namespace Modules\Course\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Base\Traits\CamelCasing;
use Modules\Employee\Models\Employee;

class CourseEnrollment extends Model
{
    use CamelCasing;

    protected $fillable = [
        'employee_id',
        'course_id',
        'enrolled_at',
        'cancelled_at',
    ];

    protected $casts = [
        'enrolled_at' => 'datetime',
        'cancelled_at' => 'datetime',
    ];

    /**
     * Get the employee that owns the enrollment.
     */
    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }

    /**
     * Get the course that owns the enrollment.
     */
    public function course(): BelongsTo
    {
        return $this->belongsTo(Course::class);
    }

    /**
     * Cancel the enrollment.
     */
    public function cancel(): self
    {
        $this->cancelled_at = now();
        $this->save();

        return $this;
    }
}
