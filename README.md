# Nông Sản TTC

Ứng dụng Laravel triển khai bằng Docker Compose. Các thành phần chạy tách riêng theo nhiệm vụ, dùng volume lưu dữ liệu và mạng nội bộ cho kết nối dịch vụ.

## Kiến trúc Compose

Compose khởi chạy **6 container**:

1. `web`: Nginx phục vụ HTTP và file tĩnh, chuyển PHP requests đến `app`.
2. `app`: PHP-FPM chạy Laravel.
3. `scheduler`: tiến trình Laravel scheduler.
4. `worker`: xử lý email và các queued jobs qua Redis.
5. `db`: MySQL 8.4.
6. `redis`: queue, cache và session; bật AOF và lưu dữ liệu trong volume.

Các container `web`, `app`, `scheduler`, `worker` dùng chung một image PHP/app hoặc web image được build từ cùng mã nguồn. MySQL và Redis không publish cổng ra máy chủ. Chỉ Nginx publish cổng `127.0.0.1:APP_PORT`, mặc định là `8080`, để reverse proxy trên máy chủ kết nối tới.

Tách tiến trình giúp có thể tăng số PHP app hoặc worker độc lập trên một máy chủ. Nginx phân giải địa chỉ service `app` động để theo kịp thay đổi container. Compose vẫn chỉ điều phối trên một máy chủ; multi-host/HA cần load balancer và dịch vụ dữ liệu dùng chung hoặc managed.

## Triển khai trên máy chủ riêng

Hướng dẫn này dành cho một VPS chạy riêng Nông Sản TTC, không dùng chung Docker network hay Compose stack với dự án khác. Theo cách triển khai của Manager Stock, Nginx và Laravel chạy trong Docker Compose; Nginx phục vụ HTTP/HTTPS trực tiếp ở cổng 80/443, còn Certbot trên VPS quản lý chứng chỉ Let's Encrypt. Ví dụ dùng Ubuntu 22.04/24.04 và thư mục `/var/www/nongsanttc`.

### 1. Chuẩn bị VPS và thư mục dự án

Trỏ DNS domain về IP VPS. Cài Docker Engine và Docker Compose plugin theo [hướng dẫn chính thức](https://docs.docker.com/engine/install/). Mở firewall cho SSH, HTTP và HTTPS; không mở cổng MySQL `3306` hoặc Redis `6379`.

Tạo thư mục và clone source lần đầu:

```sh
sudo mkdir -p /var/www/nongsanttc
sudo chown "$USER":"$USER" /var/www/nongsanttc
git clone https://github.com/hiennguyen2602/nongsanttc.git /var/www/nongsanttc
cd /var/www/nongsanttc
chmod +x dc-prod
```

Nếu repository riêng tư, thiết lập SSH deploy key trước khi clone. Khi source đã có trên VPS, các lần sau chỉ cần `cd /var/www/nongsanttc`.

### 2. Tạo và cấu hình production `.env`

```sh
./dc-prod init
nano .env
nano docker/app/nginx.production.conf
```

`init` chỉ tạo `.env` và `docker/app/nginx.production.conf` nếu file tương ứng chưa tồn tại, không ghi đè cấu hình hiện có. File Nginx production là cấu hình riêng trên VPS, được ignore bởi Git để `git pull` không ghi đè cấu hình domain. Cấu hình `.env` tối thiểu như sau:

```dotenv
APP_ENV=production
APP_DEBUG=false
APP_URL=https://ten-mien-cua-ban
APP_PORT=8080

DB_DATABASE=nongsanttc
DB_USERNAME=nongsanttc
DB_PASSWORD=mat-khau-database-rieng
DB_ROOT_PASSWORD=mat-khau-root-mysql-rieng
REDIS_PASSWORD=mat-khau-redis-rieng
```

Tạo mật khẩu riêng, dài và ngẫu nhiên (mỗi mật khẩu chạy lệnh một lần):

```sh
openssl rand -hex 32
```

Không dùng các giá trị mẫu trong `.env.example`, không commit `.env`, và giữ nguyên `APP_KEY` khi cập nhật hoặc khi ứng dụng đã có dữ liệu. Cấu hình SMTP nếu cần gửi email.

### 3. Tạo key và lấy chứng chỉ HTTPS

Tạo Laravel key, sau đó chép giá trị được in ra vào `APP_KEY=` trong `.env`:

```sh
./dc-prod build
./dc-prod run --rm --no-deps --entrypoint php app artisan key:generate --show
```

Chép key vào `APP_KEY=` trong `.env`. Không tạo key mới cho lần deploy tiếp theo.

Cài Certbot trên VPS và xin chứng chỉ trước khi chạy Nginx production lần đầu. Thay domain và email thật:

```sh
sudo apt update
sudo apt install -y certbot
sudo certbot certonly --standalone -d ten-mien-cua-ban --agree-tos -m email-cua-ban
```

Đảm bảo DNS đã trỏ về VPS, firewall cho phép cổng 80/443, và chưa có dịch vụ nào khác đang chiếm các cổng đó. Certbot standalone cần cổng 80 tạm thời để xác minh domain.

Trong `docker/app/nginx.production.conf`, thay `ten-mien-cua-ban` ở cả hai `server_name` và đường dẫn certificate bằng domain thật. Nếu dùng thêm `www`, thêm cả hostname đó vào `server_name` và cấp certificate cho cả hai tên.

### 4. Deploy lần đầu

Đảm bảo `.env` đã cấu hình production, `APP_KEY` đã có, và chứng chỉ cùng Nginx config đã khớp domain. Sau đó chạy:

```sh
./dc-prod deploy
```

Lệnh này cập nhật source bằng `git pull --ff-only`, build image, khởi động MySQL/Redis, chạy migration rồi đưa toàn bộ service lên. Nginx trong stack riêng publish cổng 80/443; MySQL và Redis không publish port ra host. Cấu hình Compose production nằm trong `compose.prod.yaml` và không phụ thuộc file cấu hình của Manager Stock.

Để Certbot tự gia hạn mà không tranh cổng 80 với Nginx, tạo hook dừng và khởi động lại web container. Hook post chạy sau lần renew, kể cả khi chưa đến hạn gia hạn:

```sh
sudo tee /etc/letsencrypt/renewal-hooks/pre/stop-nongsanttc-web >/dev/null <<'EOF'
#!/bin/sh
cd /var/www/nongsanttc
./dc-prod stop web
EOF

sudo tee /etc/letsencrypt/renewal-hooks/post/start-nongsanttc-web >/dev/null <<'EOF'
#!/bin/sh
cd /var/www/nongsanttc
./dc-prod start web
EOF

sudo chmod +x /etc/letsencrypt/renewal-hooks/pre/stop-nongsanttc-web \
  /etc/letsencrypt/renewal-hooks/post/start-nongsanttc-web
```

Kiểm tra Certbot timer đã được bật, rồi thử quy trình gia hạn:

```sh
sudo systemctl enable --now certbot.timer
sudo systemctl list-timers | grep certbot
sudo certbot renew --dry-run
```

Kiểm tra website và health endpoint sau deploy:

```sh
curl -I https://ten-mien-cua-ban
curl -I http://ten-mien-cua-ban
./dc-prod ps
```

### 5. Seed và lệnh quản trị

Chỉ trên database mới, nếu cần tạo dữ liệu khởi tạo:

```sh
./dc-prod seed
```

`DatabaseSeeder` tạo tài khoản quản trị mặc định; đổi mật khẩu ngay sau khi đăng nhập và không chạy seed lại trên database đang dùng vì `AdminSeeder` có thể đặt lại mật khẩu.

Các lệnh thường dùng:

```sh
./dc-prod ps
./dc-prod logs -f web app worker scheduler
./dc-prod exec app php artisan migrate:status
./dc-prod migrate
```

Laravel ghi log ra `stderr` trong Docker để Docker thu thập, không phụ thuộc quyền ghi vào `storage/logs`. Xem log bằng `./dc-prod logs -f app worker scheduler`.

Lệnh `migrate` chạy migration một lần với `--force`. Trước khi chuyển dữ liệu từ hệ thống cũ, backup và import database vào MySQL trên VPS; volume mới không tự chứa dữ liệu bên ngoài.

## Cập nhật và mở rộng

- Các lần cập nhật sau: backup dữ liệu trước, rồi dùng cùng một lệnh deploy:

  ```sh
  cd /var/www/nongsanttc
  ./dc-prod deploy
  ```

  Lệnh deploy luôn build image vì source Laravel được đóng gói trong image. Thiết kế migration tương thích với phiên bản đang chạy nếu cần triển khai không gián đoạn.
- Có thể tăng PHP app và worker trên cùng máy chủ khi tải tăng:

  ```sh
  ./dc-prod up -d --scale app=3 --scale worker=2
  ```

  Chỉ scale worker sau khi theo dõi tải database/Redis và thời gian xử lý job. Scheduler nên chạy một replica.
- `./dc-prod down` giữ dữ liệu. **Không dùng `./dc-prod down -v` trên production** vì lệnh đó xóa cả database, Redis, uploads và storage.
- Redis persistence hữu ích cho queued jobs, nhưng không thay backup. Sao lưu MySQL định kỳ và lưu bản sao ngoài máy chủ:

  ```sh
  ./dc-prod exec -T db sh -c 'exec mysqldump --single-transaction -u"$MYSQL_USER" -p"$MYSQL_PASSWORD" "$MYSQL_DATABASE"' > backup.sql
  ```

- `app-storage` và `app-uploads` là Docker volumes; đưa chúng vào kế hoạch backup. Khi cần chạy nhiều máy chủ, chuyển ảnh upload sang object storage tương thích S3, dùng database/Redis managed hoặc dịch vụ chia sẻ phù hợp, và bổ sung monitoring/restore drills. Docker Compose tự nó không cung cấp HA đa máy chủ.
