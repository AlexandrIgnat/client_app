<?php

return [
    'entities' => [
        'pages' => true,
        'widgets' => true,
        'resources' => true,
        'custom_permissions' => false,
    ],
    'default_guard' => 'web',
    'super_admin' => [
        'enabled' => true,
        'name' => 'super_admin',
    ],
    'panel_provider' => \BezhanSalleh\FilamentShield\FilamentShieldProvider::class,
];
