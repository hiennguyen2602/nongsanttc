#!/bin/sh
# Build Vite assets một lần — CSS/JS serve từ nginx, không cần container vite (nhanh hơn nhiều)
set -e
cd "$(dirname "$0")/.."
docker compose run --rm --no-deps vite-build
echo "Done. Xóa public/hot nếu có — trang dùng public/build/"
