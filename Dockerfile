# syntax=docker/dockerfile:1

# ---- Stage 1: PHP dependencies (no dev) ------------------------------------
FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --no-interaction --no-progress --no-scripts \
    --prefer-dist --optimize-autoloader --ignore-platform-reqs
COPY . .
RUN composer dump-autoload --no-dev --optimize --no-scripts

# ---- Stage 2: runtime (nginx + php-fpm, non-root, port 8080) ---------------
FROM serversideup/php:8.3-fpm-nginx AS production

ENV PHP_OPCACHE_ENABLE=1 \
    AUTORUN_ENABLED=true \
    AUTORUN_LARAVEL_STORAGE_LINK=true \
    AUTORUN_LARAVEL_MIGRATION=true \
    AUTORUN_LARAVEL_MIGRATION_ISOLATION=true \
    AUTORUN_LARAVEL_CONFIG_CACHE=true \
    AUTORUN_LARAVEL_ROUTE_CACHE=true \
    AUTORUN_LARAVEL_VIEW_CACHE=true \
    AUTORUN_LARAVEL_EVENT_CACHE=true

# Production PHP settings (expose_php=Off, OPcache)
COPY --chown=root:root docker/php/php.ini /usr/local/etc/php/conf.d/99-wijhan.ini

WORKDIR /var/www/html
COPY --chown=www-data:www-data --from=vendor /app /var/www/html

USER www-data
# Publish Filament assets (public/js|css|fonts/filament are gitignored)
RUN php artisan package:discover --ansi \
    && php artisan filament:assets --ansi

EXPOSE 8080
HEALTHCHECK --interval=30s --timeout=5s --start-period=40s --retries=3 \
    CMD curl -fs http://localhost:8080/up || exit 1
