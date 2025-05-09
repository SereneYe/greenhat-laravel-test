<?php

namespace Modules\Employee\Models;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Modules\Base\Traits\CamelCasing;

class EmployeeFeedback extends Model
{
    protected $guarded = [];

    protected $table = 'employee_feedback';

    use CamelCasing;

    public function employee(): BelongsTo
    {
        return $this->belongsTo(Employee::class);
    }
}
