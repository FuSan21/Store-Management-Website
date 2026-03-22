# Laravel on Vercel's container runtime, served by FrankenPHP (PHP 8.4 + Caddy).
# The image ships a current libpq, which sends TLS SNI as Prisma Postgres requires.

# --- Front-end assets ---------------------------------------------------------
FROM node:22-alpine AS assets
WORKDIR /app

COPY package.json package-lock.json ./
RUN npm ci --no-audit --no-fund

COPY vite.config.js ./
COPY resources ./resources
COPY app/Enums ./app/Enums
# Livewire's ESM build is published here and imported by resources/js/app.js.
COPY public/vendor ./public/vendor
RUN npm run build

# --- PHP base -----------------------------------------------------------------
FROM dunglas/frankenphp:1-php8.4-alpine AS base

RUN install-php-extensions pdo_pgsql intl zip opcache pcntl \
    && cp "$PHP_INI_DIR/php.ini-production" "$PHP_INI_DIR/php.ini"

COPY docker/php.ini "$PHP_INI_DIR/conf.d/zz-app.ini"

WORKDIR /app

# --- Composer dependencies ----------------------------------------------------
FROM base AS vendor

COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --no-progress --prefer-dist --no-scripts --no-autoloader

COPY . .
RUN composer dump-autoload --no-dev --optimize --classmap-authoritative \
    && php artisan view:cache

# --- Runtime ------------------------------------------------------------------
FROM base AS runtime

ENV APP_ENV=production \
    APP_DEBUG=false \
    APP_TIMEZONE=Asia/Dhaka \
    DB_CONNECTION=pgsql \
    CACHE_STORE=database \
    SESSION_DRIVER=cookie \
    QUEUE_CONNECTION=sync \
    LOG_CHANNEL=stderr \
    LOG_LEVEL=warning \
    PORT=80

COPY --from=vendor /app /app
COPY --from=assets /app/public/build /app/public/build
COPY docker/Caddyfile /etc/caddy/Caddyfile

RUN mkdir -p storage/framework/cache/data storage/framework/sessions storage/framework/views storage/logs bootstrap/cache

CMD ["frankenphp", "run", "--config", "/etc/caddy/Caddyfile"]
