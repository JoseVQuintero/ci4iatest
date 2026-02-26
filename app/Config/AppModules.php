<?php

namespace Config;

use CodeIgniter\Config\BaseConfig;

class AppModules extends BaseConfig
{
    /**
     * Module registry used for menu rendering and access control.
     *
     * @var array<string, array<string, string|int>>
     */
    public array $modules = [
        'dashboard' => [
            'label' => 'App.dashboard',
            'route' => 'dashboard',
            'icon' => 'fas fa-tachometer-alt',
            'sort' => 10,
        ],
        'products' => [
            'label' => 'App.products',
            'route' => 'products',
            'icon' => 'fas fa-box',
            'sort' => 20,
        ],
        'users' => [
            'label' => 'App.users',
            'route' => 'users',
            'icon' => 'fas fa-users',
            'sort' => 30,
        ],
        'roles' => [
            'label' => 'App.roles',
            'route' => 'roles',
            'icon' => 'fas fa-user-shield',
            'sort' => 40,
        ],
    ];
}
