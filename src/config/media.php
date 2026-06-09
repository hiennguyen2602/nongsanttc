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

];
