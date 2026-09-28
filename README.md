# Nông Sản TTC

## Một cách triển khai

Repo chỉ dùng Dockerfile ở thư mục gốc. Dockerfile đóng gói Laravel, Composer dependencies và Vite assets, rồi chạy Nginx cùng PHP-FPM trong một container. Dùng cùng Dockerfile/image và cùng bộ tên biến môi trường ở máy local, VPS hoặc Vibe Host; không có cấu hình Compose hay image riêng theo môi trường.

MySQL không chạy trong container ứng dụng. Local dùng MySQL của XAMPP; môi trường khác kết nối tới MySQL bên ngoài tương ứng. Cả ba cùng dùng các biến `DB_HOST`, `DB_PORT`, `DB_DATABASE`, `DB_USERNAME` và `DB_PASSWORD`; chỉ giá trị host/credentials phụ thuộc nơi đặt MySQL, Dockerfile và image không thay đổi.

## Chạy bằng Docker

Đảm bảo MySQL trong XAMPP đang chạy. Tạo file môi trường nếu chưa có, rồi cấu hình `DB_HOST=host.docker.internal`, `DB_PORT=3306` và thông tin database/user/password có quyền truy cập database trong XAMPP. Không dùng `DB_HOST=db` vì local không chạy MySQL bằng Compose.

Trong PowerShell:

```powershell
if (-not (Test-Path src/.env)) {
	Copy-Item src/.env.example src/.env
}
docker build -t nongsanttc .
docker run --rm nongsanttc php artisan key:generate --show
```

Lưu key vừa tạo vào `APP_KEY` trong `src/.env`. Khởi động ứng dụng và tạo bảng lần đầu:

```powershell
docker rm -f nongsanttc-local 2>$null
docker run -d --name nongsanttc-local -p 8080:80 --env-file src/.env -v nongsanttc-storage:/var/www/html/storage -v nongsanttc-uploads:/var/www/html/public/uploads nongsanttc
docker exec nongsanttc-local php artisan migrate --seed --force
```

Mở http://localhost:8080. Database XAMPP và thông tin kết nối hiện đã được chuẩn bị trên máy này. Sau khi sửa mã nguồn, build lại image rồi tạo lại container. File upload và dữ liệu Laravel được giữ trong Docker volumes.

Thông tin đăng nhập admin sau khi seed: `admin@nongsanttc.local` / `password`.

## Vibe Host hoặc máy chủ khác

Chọn Dockerfile ở thư mục gốc, build context là thư mục gốc repository và cung cấp các biến môi trường cùng tên như trong `src/.env.example`. Trên Vibe Host, tạo MySQL trong bảng điều khiển rồi điền thông tin DB được cấp. Chạy `php artisan migrate --force` qua console/tác vụ deploy sau khi ứng dụng kết nối được database.

Không commit `.env` hoặc credentials. Cấu hình persistent storage cho `storage` và `public/uploads` nếu nền tảng hỗ trợ; nếu không, dùng object storage cho tệp cần giữ qua các lần deploy. `QUEUE_CONNECTION=sync` giúp tác vụ queue chạy trong request, không cần container worker riêng.

## Phát triển

Quy ước migration (gộp vào bảng gốc, squash trước production): [docs/development.md](docs/development.md).
