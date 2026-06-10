<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Dung lượng tối đa cho mỗi ảnh upload (đơn vị: MB)
    |--------------------------------------------------------------------------
    |
    | Giá trị này điều khiển giới hạn nghiệp vụ khi upload ảnh sản phẩm, banner...
    | Lưu ý: phải nhỏ hơn upload_max_filesize của PHP (cấu hình trong
    | docker/php/uploads.ini) để người dùng nhận được thông báo lỗi thân thiện
    | thay vì lỗi hệ thống.
    |
    */

    'max_image_mb' => (float) env('MAX_IMAGE_UPLOAD_MB', 5),

    /** Ảnh đại diện bài viết: một file, max rộng (px). */
    'post_featured_max_width' => 600,

    /** Ảnh chèn trong editor: một file, max rộng (px). */
    'editor_max_width' => 1200,

    /** Grace period trước khi cron xóa ảnh editor mồ côi (giờ). */
    'editor_orphan_grace_hours' => (int) env('EDITOR_ORPHAN_GRACE_HOURS', 48),

    /** Banner CTA: giữ nguyên tỷ lệ, chỉ thu nhỏ nếu rộng hơn (px). */
    'banner_desktop_max_width' => 1200,
    'banner_mobile_max_width' => 768,

    /** Hero trang chủ: giữ nguyên tỷ lệ, chỉ thu nhỏ nếu rộng hơn (px). */
    'hero_desktop_max_width' => 1920,
    'hero_mobile_max_width' => 768,

];
