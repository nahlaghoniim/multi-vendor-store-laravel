<?php

return [

    [
        'icon'   => 'nav-icon fas fa-tachometer-alt',
        'route'  => 'dashboard.index',
        'title'  => 'Dashboard',
        'active' => 'dashboard.index',
    ],

    [
        'icon'    => 'fas fa-tags nav-icon',
        'route'   => 'dashboard.categories.index',
        'title'   => 'Categories',
        'active'  => 'dashboard.categories.*',
        'ability' => 'categories.view',
    ],

    [
        'icon'    => 'fas fa-box nav-icon',
        'route'   => 'dashboard.products.index',
        'title'   => 'Products',
        'active'  => 'dashboard.products.*',
        'ability' => 'products.view',
    ],

    [
        'icon'    => 'fas fa-shopping-cart nav-icon',
        'route'   => 'dashboard.orders.index',
        'title'   => 'Orders',
        'active'  => 'dashboard.orders.*',
        'ability' => 'orders.view',
    ],

    [
        'icon'    => 'fas fa-shield-alt nav-icon',
        'route'   => 'dashboard.roles.index',
        'title'   => 'Roles',
        'active'  => 'dashboard.roles.*',
        'ability' => 'roles.view',
    ],

    [
        'icon'    => 'fas fa-users nav-icon',
        'route'   => 'dashboard.users.index',
        'title'   => 'Users',
        'active'  => 'dashboard.users.*',
        'ability' => 'users.view',
    ],

    [
        'icon'    => 'fas fa-user-shield nav-icon',
        'route'   => 'dashboard.admins.index',
        'title'   => 'Admins',
        'active'  => 'dashboard.admins.*',
        'ability' => 'admins.view',
    ],

];
