<?php

/**
 * Cấu hình tĩnh không quản lý qua admin Settings.
 * Thông tin cửa hàng (tên, liên hệ, mạng xã hội, banner…) lấy từ store_setting().
 * Khuyến mãi lấy từ bảng promotions.
 */
return [
    'commitments' => [
        ['icon' => 'cert', 'text' => 'Chứng nhận VSATTP'],
        ['icon' => 'leaf', 'text' => '100% không phẩm màu'],
        ['icon' => 'shield', 'text' => '100% không bảo quản'],
        ['icon' => 'star', 'text' => 'Nguồn gốc rõ ràng'],
        ['icon' => 'farm', 'text' => 'Nông sản sạch từ vùng trồng'],
        ['icon' => 'heart', 'text' => 'Cam kết chất lượng OCOP'],
    ],
];
