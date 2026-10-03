FROM php:8.3-fpm-trixie AS php-base

ENV TZ=Asia/Ho_Chi_Minh

RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        git \
        libfreetype6-dev \
        libjpeg62-turbo-dev \
        libonig-dev \
        libpng-dev \
        libwebp-dev \
        libzip-dev \
        unzip \
    && docker-php-ext-configure gd --with-jpeg --with-freetype --with-webp \
    && docker-php-ext-install pdo_mysql zip mbstring bcmath pcntl gd \
    && pecl install redis-6.2.0 \
    && docker-php-ext-enable redis \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

FROM node:22-alpine3.22 AS frontend-build

WORKDIR /app
COPY package.json package-lock.json ./
RUN npm ci
COPY ./ ./
RUN npm run build

FROM php-base AS composer-build

WORKDIR /var/www/html
COPY ./ ./
RUN mkdir -p bootstrap/cache storage/framework/cache storage/framework/sessions storage/framework/views storage/logs \
    && composer install --no-dev --no-interaction --prefer-dist --optimize-autoloader

FROM php-base AS runtime

WORKDIR /var/www/html
COPY ./ ./
COPY --from=composer-build /var/www/html/vendor ./vendor
COPY --from=frontend-build /app/public/build ./public/build
COPY docker/php/start.sh /usr/local/bin/app-start
COPY docker/php/opcache.ini /usr/local/etc/php/conf.d/99-opcache-custom.ini
COPY docker/php/uploads.ini /usr/local/etc/php/conf.d/99-uploads-custom.ini

RUN sed -i 's/\r$//' /usr/local/bin/app-start \
    && chmod +x /usr/local/bin/app-start \
    && mkdir -p storage/app/public storage/app/private \
        storage/framework/cache storage/framework/sessions \
        storage/framework/views storage/logs bootstrap/cache \
        public/uploads/settings public/uploads/editor public/uploads/products \
    && chown -R www-data:www-data storage bootstrap/cache public/uploads

EXPOSE 9000

ENTRYPOINT ["/usr/local/bin/app-start"]
CMD ["php-fpm", "-F"]

FROM nginx:stable-alpine AS web

COPY docker/app/nginx.conf /etc/nginx/conf.d/default.conf
COPY --from=runtime /var/www/html/public /var/www/html/public

RUN mkdir -p /var/www/html/storage/app/public \
    && chown -R nginx:nginx /var/www/html/public

EXPOSE 8080