<?php

return [
    'name' => 'Nông Sản TTC',
    'menu' => [
        [
            'label' => 'Dashboard',
            'route' => 'admin.dashboard',
            'icon' => 'dashboard',
        ],
        [
            'label' => 'Sản phẩm',
            'route' => null,
            'icon' => 'products',
            'children' => [
                ['label' => 'Danh sách sản phẩm', 'route' => null],
                ['label' => 'Danh mục', 'route' => null],
            ],
        ],
        [
            'label' => 'Đơn hàng',
            'route' => null,
            'icon' => 'orders',
        ],
        [
            'label' => 'Khách hàng',
            'route' => null,
            'icon' => 'users',
        ],
        [
            'label' => 'Tin tức',
            'route' => null,
            'icon' => 'news',
        ],
        [
            'label' => 'Khuyến mãi',
            'route' => null,
            'icon' => 'promo',
        ],
        [
            'label' => 'Banner',
            'route' => null,
            'icon' => 'banner',
        ],
        [
            'label' => 'Cài đặt',
            'route' => null,
            'icon' => 'settings',
        ],
    ],
];
