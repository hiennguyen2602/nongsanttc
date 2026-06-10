#!/bin/sh
set -e

cd /var/www/html

npm install

if [ "${VITE_MODE:-watch}" = "dev" ]; then
    echo "[vite] Dev server (HMR) — lần compile Tailwind đầu có thể chậm trên Windows."
    exec npm run dev
fi

rm -f public/hot

echo "[vite] Building static assets (fast first load)..."
npm run build

echo "[vite] Watching — sửa CSS/JS/Blade rồi F5 trình duyệt."
exec npm run watch