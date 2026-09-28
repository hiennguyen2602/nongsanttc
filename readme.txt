DEPLOY VIBE HOST

1. Tạo MySQL trong Vibe Host. Ghi lại Host, Port, Database, User và Password.

2. Tạo APP_KEY trên máy có Docker:
   docker build -t nongsanttc .
   docker run --rm nongsanttc php artisan key:generate --show

3. Trong Vibe Host, chọn GitHub repository và branch master. Chọn thư mục dự án/build context là root repository (không chọn src), Dockerfile là Dockerfile ở root, cổng HTTP 80. Đảm bảo branch master có các thay đổi deploy mới nhất.

4. Khai báo biến môi trường trong bảng điều khiển Vibe Host, không tạo hoặc commit file .env:
   APP_ENV=production
   APP_DEBUG=false
   APP_URL=https://ten-mien-cua-ban
   APP_KEY=base64:key-vua-tao
   DB_CONNECTION=mysql
   DB_HOST=host-duoc-cap
   DB_PORT=3306
   DB_DATABASE=database-duoc-cap
   DB_USERNAME=user-duoc-cap
   DB_PASSWORD=password-duoc-cap
   SESSION_DRIVER=database
   CACHE_STORE=database
   QUEUE_CONNECTION=sync
   LOG_LEVEL=warning
   AUTO_MIGRATE=true
   AUTO_SEED=true

5. Cấu hình gửi email bằng Gmail:
   - Bật Xác minh 2 bước cho tài khoản Google.
   - Tạo App Password tại https://myaccount.google.com/apppasswords.
   - Khai báo trong Vibe Host:
     MAIL_MAILER=smtp
     MAIL_SCHEME=null
     MAIL_HOST=smtp.gmail.com
     MAIL_PORT=587
     MAIL_USERNAME=dia-chi-gmail
     MAIL_PASSWORD=app-password-16-ky-tu
     MAIL_ENCRYPTION=tls
     MAIL_FROM_ADDRESS=dia-chi-gmail
     MAIL_FROM_NAME=Nong San TTC
   - Dùng App Password, không dùng mật khẩu đăng nhập Gmail. Bỏ dấu cách trong App Password.

6. Nhấn Đồng ý/Đưa website lên mạng. Chờ log báo migration và seed thành công.

7. Đặt AUTO_MIGRATE=false và AUTO_SEED=false, rồi deploy lại.

   Khi có migration mới: chạy php artisan migrate --force bằng console của Vibe Host. Nếu không có console, đặt AUTO_MIGRATE=true, deploy và kiểm tra log; sau khi migration thành công, đặt lại false rồi deploy lại. Giữ AUTO_SEED=false.

8. Mở https://ten-mien-cua-ban/admin/login, đăng nhập admin@nongsanttc.com / Aa123456!, đổi mật khẩu ngay và thử chức năng quên mật khẩu.

9. Bật persistent storage cho /var/www/html/storage và /var/www/html/public/uploads nếu Vibe Host hỗ trợ; kiểm tra trang chủ, admin, database, email và upload ảnh.