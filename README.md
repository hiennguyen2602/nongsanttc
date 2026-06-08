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

## Admin

http://localhost:8080/admin/login — `admin@nongsanttc.local` / `password`
