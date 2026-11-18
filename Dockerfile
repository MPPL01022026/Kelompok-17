# syntax=docker/dockerfile:1.7

# Stage 1: Install PHP production dependencies
FROM composer:2 AS vendor
WORKDIR /app

COPY app/composer.json app/composer.lock ./
RUN composer install \
    --no-dev \
    --no-interaction \
    --no-plugins \
    --no-scripts \
    --prefer-dist \
    --optimize-autoloader

# Stage 2: Build Vite & Tailwind CSS v4 frontend bundle
FROM node:22-bookworm-slim AS frontend
WORKDIR /app

COPY app/package.json app/package-lock.json app/vite.config.js ./
RUN npm ci

COPY app/resources ./resources
COPY app/public ./public
COPY --from=vendor /app/vendor ./vendor

RUN npm run build

# Stage 3: Production PHP 8.4-FPM + Nginx runtime
FROM php:8.4-fpm-alpine AS runtime
WORKDIR /var/www/html

ENV APP_ENV=production \
    APP_DEBUG=false \
    LOG_CHANNEL=stack \
    TZ=Asia/Jakarta

RUN apk add --no-cache \
        nginx \
        curl \
        tzdata \
        libpng \
        libpng-dev \
        libwebp \
        libwebp-dev \
        libjpeg-turbo \
        libjpeg-turbo-dev \
        freetype \
        freetype-dev \
        libzip \
        libzip-dev \
        icu-libs \
        icu-dev \
        oniguruma \
        oniguruma-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg --with-webp \
    && docker-php-ext-install -j"$(nproc)" \
        pdo_mysql \
        mbstring \
        bcmath \
        intl \
        zip \
        gd \
        opcache \
    && apk del --no-network \
        libpng-dev \
        libwebp-dev \
        libjpeg-turbo-dev \
        freetype-dev \
        libzip-dev \
        icu-dev \
        oniguruma-dev \
    && rm -rf /var/cache/apk/* /tmp/*

# Copy PHP & Nginx production configuration
COPY docker/php/php-prod.ini /usr/local/etc/php/conf.d/99-ayamo-prod.ini
COPY docker/nginx/default.conf /etc/nginx/http.d/default.conf
RUN sed -i 's/^user .*/user www-data;/' /etc/nginx/nginx.conf \
    && mkdir -p /run/nginx /var/log/nginx /var/lib/nginx/tmp \
    && chown -R www-data:www-data /var/lib/nginx

# Copy application source, production vendor packages, compiled Vite bundle, and entrypoint
COPY --chown=www-data:www-data app/ /var/www/html/
COPY --from=vendor --chown=www-data:www-data /app/vendor ./vendor
COPY --from=frontend --chown=www-data:www-data /app/public/build ./public/build
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh

# Prepare writable directories, package discovery, and entrypoint permissions
RUN rm -f bootstrap/cache/*.php resources/views/dashboard.blade.php \
    && mkdir -p \
        storage/app/public/products \
        storage/app/public/payment \
        storage/framework/cache/data \
        storage/framework/sessions \
        storage/framework/views \
        storage/logs \
        bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache \
    && php artisan package:discover --ansi \
    && sed -i 's/\r$//' /usr/local/bin/entrypoint.sh \
    && chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 8000

HEALTHCHECK --interval=30s --timeout=5s --start-period=20s --retries=3 \
    CMD curl -fsS http://127.0.0.1:8000/up || exit 1

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]

