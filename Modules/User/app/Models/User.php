<?php

namespace Modules\User\Models;

use Filament\Models\Contracts\FilamentUser;
use Filament\Panel;
use Illuminate\Database\Eloquent\Casts\Attribute;
use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Illuminate\Support\Facades\Hash;
use Laravel\Sanctum\HasApiTokens;
use Modules\Base\Traits\CamelCasing;
use Modules\Employee\Models\Employee;
use Rappasoft\LaravelAuthenticationLog\Traits\AuthenticationLoggable;
use Silber\Bouncer\Database\HasRolesAndAbilities;
use Stephenjude\FilamentTwoFactorAuthentication\TwoFactorAuthenticatable;

class User extends Authenticatable implements FilamentUser
{
    use AuthenticationLoggable,
        CamelCasing,
        HasApiTokens,
        HasRolesAndAbilities,
        Notifiable,
        SoftDeletes,
        TwoFactorAuthenticatable;

    protected $guarded = [];

    protected $hidden = [
        'password', 'remember_token', 'google2fa_secret',
    ];

    public function canAccessPanel(Panel $panel): bool
    {
        return true;
    }

    public function changePassword(string $password): self
    {
        $this->update(['password' => Hash::make($password)]);

        return $this;
    }

    protected function timezone(): Attribute
    {
        return Attribute::make(
            get: fn (?string $value) => $value ?: config('app.timezone'),
        );
    }

    public function employee(): HasOne
    {
        return $this->hasOne(Employee::class);
    }
}
