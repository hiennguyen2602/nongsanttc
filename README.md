# Nông Sản TTC

## Khởi chạy nhanh (giống manager-stock)

```bash
cp src/.env.example.docker src/.env   # lần đầu
cp docker-compose.override.yml.example docker-compose.override.yml

docker compose down --remove-orphans
docker compose up -d --build

# Build CSS/JS một lần — BẮT BUỘC cho tốc độ (không dùng Vite dev mặc định)
docker compose run --rm vite-build

docker compose exec app php artisan migrate --seed --force
```

Truy cập: http://localhost:8080

## Tại sao trước đó chậm?

| Nguyên nhân | Cách xử lý |
|-------------|-----------|
| `public/hot` + không có `public/build` | Browser chờ Vite `:5173` → **build assets** |
| Vite dev chạy mặc định + `npm install` mỗi lần | Vite chỉ bật với `--profile dev` |
| Redis cho session/cache | Đổi sang **database** như manager-stock |
| Redis + Vite + queue chạy cùng lúc | Chỉ `app + web + db` mặc định |

## Dev frontend (tự refresh — giống manager-stock)

`docker compose up -d` tự chạy container **vite**. Sửa CSS/JS/Blade → trình duyệt refresh.

Chỉ cần build tĩnh khi deploy production:

```bash
docker compose run --rm vite-build
```

## Sau khi `git checkout` / đổi branch (layout vỡ, CSS cũ)

**Blade/PHP** đổi theo branch ngay, nhưng **`public/build` và `public/hot` nằm trong `.gitignore`** — không đi theo branch. Trình duyệt vẫn có thể dùng CSS/JS cũ → giao diện lệch hoặc vỡ.

Chọn **một** trong hai cách:

### Cách 1 — Vite dev (HMR, tự refresh khi code)

```bash
docker compose up -d vite
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
