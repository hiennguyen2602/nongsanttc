# Triển khai Vibe Host

1. Tạo ứng dụng trên Vibe Host, chọn repository và nhánh cần triển khai. Để trống **Thư mục con** (thư mục gốc), chọn `Dockerfile` ở root và cổng `80`. Entrypoint sẽ dùng biến `PORT` do nền tảng cấp nếu nền tảng gán cổng khác.

2. Tạo MySQL trong bảng điều khiển Vibe Host. Tạo `APP_KEY` bằng lệnh `php artisan key:generate --show`, rồi khai báo các biến sau trong cấu hình ứng dụng:

    ```env
    APP_ENV=production
    APP_DEBUG=false
    APP_URL=https://ten-mien-cua-ban
    APP_KEY=base64:key-vua-tao
    DB_CONNECTION=mysql
    DB_HOST=host-do-vibe-host-cap
    DB_PORT=3306
    DB_DATABASE=ten-database
    DB_USERNAME=database-user
    DB_PASSWORD=database-password
    SESSION_DRIVER=file
    CACHE_STORE=file
    QUEUE_CONNECTION=sync
    ```

3. Bật xác minh 2 bước và tạo **App Password** trong tài khoản Google. Khai báo trên Vibe Host:

    ```env
    MAIL_MAILER=smtp
    MAIL_SCHEME=smtp
    MAIL_HOST=smtp.gmail.com
    MAIL_PORT=587
    MAIL_USERNAME=dia-chi-gmail@gmail.com
    MAIL_PASSWORD=google-app-password
    MAIL_FROM_ADDRESS=dia-chi-gmail@gmail.com
    MAIL_FROM_NAME=Nong San TTC
    ```

    Dùng App Password, không dùng mật khẩu Gmail; bỏ dấu cách khi nhập. `MAIL_FROM_ADDRESS` nên trùng với `MAIL_USERNAME`.

4. Lưu cấu hình và chọn **Đồng ý, đưa website lên mạng**. Sau khi container khởi động, mở Console của ứng dụng và chạy thủ công:

    ```sh
    php artisan migrate --force
    php artisan db:seed --force
    ```

    Chỉ chạy `db:seed` một lần trên database mới. Seeder tạo tài khoản admin với mật khẩu mặc định; đổi mật khẩu ngay sau khi đăng nhập và không chạy seed lại trên database đang sử dụng.

    Nếu muốn tự động migrate khi khởi động, khai báo `RUN_MIGRATIONS_ON_STARTUP=true` trong biến môi trường.

5. Bật lưu trữ lâu dài cho `storage` và `public/uploads` để giữ dữ liệu qua các lần triển khai.
