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
                ['label' => 'Danh sách sản phẩm', 'route' => 'admin.products.index'],
                ['label' => 'Danh mục', 'route' => 'admin.categories.index'],
            ],
        ],
        [
            'label' => 'Đơn hàng',
            'route' => 'admin.orders.index',
            'icon' => 'orders',
        ],
        [
            'label' => 'Người dùng',
            'route' => 'admin.users.index',
            'icon' => 'users',
        ],
        [
            'label' => 'Tin tức',
            'route' => 'admin.posts.index',
            'icon' => 'news',
        ],
        [
            'label' => 'Khuyến mãi',
            'route' => 'admin.promotions.index',
            'icon' => 'promo',
        ],
        [
            'label' => 'Banner',
            'route' => 'admin.banners.index',
            'icon' => 'banner',
        ],
        [
            'label' => 'Cài đặt',
            'route' => 'admin.settings.edit',
            'icon' => 'settings',
        ],
    ],
];
