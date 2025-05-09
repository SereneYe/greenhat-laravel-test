<?php

namespace Modules\Employee\Models;

use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\MorphMany;
use Modules\Base\Traits\CamelCasing;
use Modules\User\Models\User;

class Employee extends Model
{
    use CamelCasing;

    protected $guarded = [];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class);
    }

    public function authenticationLogs(): MorphMany
    {
        return $this->user->authentications();
    }

    public static function getRoleOptions(): array
    {
        return [
            'Family Support Leader' => 'Family Support Leader',
        ];
    }

    public function username(): Attribute
    {
        return Attribute::make(
            get: fn ($value) => $this->user->name,
        );
    }
}
