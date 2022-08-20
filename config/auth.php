<?php
return [
    'defaults' => [
        'guard' => env('AUTH_GUARD', 'api'),
        'guard' => 'api',
        'passwords' => 'users',
    ],

    'guards' => [
        'web' => [
            'driver' => 'session',
            'provider' => 'users',
        ],
        'api' => [
            'driver' => 'jwt',
            'provider' => 'users',
            'hash' => false,

        ],
        'user_api' => [
            'driver' => 'jwt',
            'provider' => 'users',

        ],
        
        'admin_api' => [
            'driver' => 'jwt',
            'provider' => 'users',
        ],
    ],
    
    'providers' => [
        'users' => [
            'driver' => 'eloquent',
            'model'  =>  App\Models\User::class,
        ]
    ],];