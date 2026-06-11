# Nông Sản TTC

## Khởi chạy nhanh

```bash
cp src/.env.example.docker src/.env   # lần đầu
cp docker-compose.override.yml.example docker-compose.override.yml

docker compose down --remove-orphans
docker compose up -d --build

docker compose exec app php artisan migrate --seed --force
```

Truy cập: http://localhost:8080

## Tại sao lần đầu vào chậm?

**Nguyên nhân chính đã xác định:** khi có file `public/hot`, Laravel bắt browser tải CSS/JS từ **Vite dev** (`:5173`). Trên Windows + Docker, lần compile Tailwind đầu mất **~60 giây** — trong lúc đó trang trắng hoặc không có style.

**Cách xử lý ổn định (mặc định):** container `vite` chạy `build + watch` — **không tạo `public/hot`**, trang dùng file trong `public/build/` (nginx, ~200ms). Sửa CSS/JS → vite tự build lại → **F5 trình duyệt**.

| Triệu chứng | Nguyên nhân | Xử lý |
|-------------|-------------|--------|
| Trang trắng / không CSS ~1 phút | `public/hot` + Vite dev compile | Mặc định đã dùng build+watch; xóa hot: `rm src/public/hot` |
| Ảnh/CSS thiếu chậm từng file | nginx gọi Laravel (đã sửa) | `vite-build` hoặc để vite watch build |
| `docker compose up` lỗi unhealthy | healthcheck thiếu lệnh | Đã sửa `php-fpm -t` |
| Trang chủ nhiều ảnh (~1.5MB) | Lần đầu browser tải ảnh | Bình thường; lần 2 cache nhanh |

**Quy trình hàng ngày:**

```bash
docker compose up -d
# Đợi vite build xong (~30–40s lần đầu sau up) rồi mở http://localhost:8080
```

**Muốn HMR tức thì:** trong `docker-compose.override.yml` thêm `VITE_MODE: dev` vào service `vite`, rồi `docker compose up -d --force-recreate vite`.

## Dev frontend

- **Mặc định:** `build + watch` — nhanh, sửa code → F5
- **Tùy chọn HMR:** `VITE_MODE=dev` (xem trên)

Build một lần thủ công:

```bash
docker compose run --rm vite-build
```

## Sau khi `git checkout` / đổi branch (layout vỡ, CSS cũ)

**Blade/PHP** đổi theo branch ngay, nhưng **`public/build` và `public/hot` nằm trong `.gitignore`** — không đi theo branch. Trình duyệt vẫn có thể dùng CSS/JS cũ → giao diện lệch hoặc vỡ.

Chọn **một** trong hai cách:

### Cách 1 — Vite dev (HMR, tự refresh khi code)

```bash
docker compose restart vite          # tạo lại public/hot
docker compose exec app php artisan view:clear
```

Kiểm tra: file `src/public/hot` phải tồn tại (nội dung dạng `http://127.0.0.1:5173`). Hard refresh trình duyệt (`Ctrl+F5`).

### Cách 2 — Build tĩnh (nhanh, ổn định — khuyên dùng sau đổi branch)

```bash
docker compose run --rm vite-build     # build mới + xóa public/hot
docker compose exec app php artisan view:clear
```

Nếu vẫn lạ: tắt vite dev để tránh nhầm mode:

```bash
docker compose stop vite
```

### Cache Laravel (Docker volume)

View/config cache nằm volume `app_bootstrap_cache`, có thể giữ code cũ:

```bash
docker compose exec app php artisan optimize:clear
```

**Lưu ý:** Chạy `vite-build` trong lúc container `vite` đang chạy sẽ xóa `public/hot` — cần `docker compose restart vite` hoặc dùng một mode (dev **hoặc** build), không trộn lẫn.

## Admin

http://localhost:8080/admin/login — `admin@nongsanttc.local` / `password`

## Production (VPS)

Quy mô **vài chục–vài trăm truy cập/ngày**: `CACHE_STORE=database` (hoặc `file`) và `SESSION_DRIVER=database` (hoặc `file`) **đủ dùng**. Chưa cần Redis trừ khi nhiều container PHP cùng session hoặc traffic tăng rõ.

### `.env` production

| Biến | Giá trị |
|------|---------|
| `APP_ENV` | `production` |
| `APP_DEBUG` | `false` |
| `APP_URL` | `https://domain-cua-ban` |
| `LOG_LEVEL` | `warning` hoặc `error` |
| `SESSION_DRIVER` | `database` (khuyên dùng) hoặc `file` |
| `CACHE_STORE` | `database` hoặc `file` |
| `QUEUE_CONNECTION` | `database` (đủ nếu queue nhẹ) |

### Script `dc-prod` (VPS)

File `dc-prod` có quyền thực thi (`755`) trong git — sau `git pull` chạy trực tiếp `./dc-prod ...`. Nếu vẫn `Permission denied`: `chmod +x dc-prod`.

**Không** chạy `npm install` trực tiếp trên VPS (dễ sửa `package-lock.json` và chặn `git pull`). Build frontend qua `./dc-prod vite-build`.

### Checklist deploy

```bash
# 1. Code + dependency
composer install --no-dev --optimize-autoloader
npm ci && npm run build          # hoặc: docker compose run --rm vite-build

# 2. App key / migrate (lần đầu hoặc có migration mới)
php artisan key:generate --force   # chỉ lần đầu
php artisan migrate --force

# 3. Cache Laravel (chạy sau khi .env đã đúng)
php artisan config:cache
php artisan route:cache
php artisan view:cache
php artisan event:cache            # Laravel 11+
```

Docker:

```bash
docker compose exec app composer install --no-dev --optimize-autoloader
docker compose run --rm vite-build
docker compose exec app php artisan migrate --force
docker compose exec app php artisan config:cache
docker compose exec app php artisan route:cache
docker compose exec app php artisan view:cache
docker compose exec app php artisan event:cache
```

### Sau khi đổi `.env`, route, hoặc Blade

```bash
php artisan optimize:clear
# sửa xong rồi cache lại (config/route/view/event)
```

**Không** bật `APP_DEBUG=true` trên production. **Không** commit `.env`.
