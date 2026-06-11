import { defineConfig } from 'vite';
import laravel from 'laravel-vite-plugin';
import tailwindcss from '@tailwindcss/vite';

export default defineConfig({
    plugins: [
        laravel({
            input: [
                'resources/css/app.css',
                'resources/css/admin.css',
                'resources/scss/pagination.scss',
                'resources/js/store.js',
                'resources/js/admin.js',
            ],
            refresh: true,
        }),
        tailwindcss(),
    ],
    server: {
        host: '0.0.0.0',
        port: 5173,
        strictPort: true,
        watch: {
            ignored: [
                '**/storage/**',
                '**/bootstrap/cache/**',
                '**/vendor/**',
                '**/public/build/**',
                '**/public/hot',
            ],
            usePolling: process.env.CHOKIDAR_USEPOLLING === 'true',
            interval: Number(process.env.CHOKIDAR_INTERVAL) || undefined,
        },
        hmr: {
            host: '127.0.0.1',
            port: 5173,
        },
    },
});
