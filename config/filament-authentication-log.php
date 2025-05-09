<?php

use Modules\User\Models\User;

return [

    'resources' => [
        'AutenticationLogResource' => \Tapp\FilamentAuthenticationLog\Resources\AuthenticationLogResource::class,
    ],

    'authenticable-resources' => [
        User::class,
    ],

    'authenticatable' => [
        'field-to-display' => 'name',
    ],

    'navigation' => [
        'authentication-log' => [
            'register' => true,
            'sort' => 1,
            'icon' => 'heroicon-o-shield-check',
        ],
        'group' => 'Logs',
    ],

    'sort' => [
        'column' => 'login_at',
        'direction' => 'desc',
    ],
];
