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
            'label' => 'Khách hàng',
            'route' => 'admin.customers.index',
            'icon' => 'customers',
        ],
        [
            'label' => 'Liên hệ',
            'route' => 'admin.contact-messages.index',
            'icon' => 'news',
        ],
        [
            'label' => 'Bài viết',
            'route' => 'admin.posts.index',
            'icon' => 'news',
        ],
        [
            'label' => 'Khuyến mãi',
            'route' => 'admin.promotions.index',
            'icon' => 'promo',
        ],
        [
            'label' => 'Quản trị',
            'route' => 'admin.users.index',
            'icon' => 'users',
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
