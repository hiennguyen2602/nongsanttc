<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Nhãn hiển thị dùng chung (admin + storefront nếu cần)
    |--------------------------------------------------------------------------
    |
    | Sửa text tại đây — model gọi qua visibilityLabels(), publishStatusLabels(), ...
    |
    */

    /** is_active: sản phẩm, banner, khuyến mãi */
    'visibility' => [
        'active' => 'Hiển thị',
        'inactive' => 'Ẩn',
    ],

    /** is_published: bài viết */
    'publish' => [
        'published' => 'Đã xuất bản',
        'draft' => 'Nháp',
    ],

    /** status: tài khoản quản trị (status 1/0) */
    'account' => [
        1 => 'Hoạt động',
        0 => 'Khóa',
    ],

    /** position: banner */
    'banner_position' => [
        'home_cta' => 'Trang chủ — khối banner CTA',
    ],

    /** status: đơn hàng */
    'order_status' => [
        'pending' => 'Chờ xử lý',
        'confirmed' => 'Đã xác nhận',
        'shipping' => 'Đang giao',
        'completed' => 'Hoàn thành',
        'cancelled' => 'Đã hủy',
    ],

];
