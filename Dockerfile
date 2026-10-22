# syntax=docker/dockerfile:1.6
#
# Multi-stage build for gpm-login-global-private-server.
#
# - Stage 1 (node-builder)     : compile Vue 3 SPA (public/build/*)
# - Stage 2 (composer-builder) : install PHP deps (vendor/)
# - Stage 3 (runtime)          : slim PHP/Apache image serving the app
#
# Both builder stages run on the native BUILDPLATFORM to avoid slow QEMU
# emulation on multi-arch builds. Their artifacts (JS/CSS + PHP autoload)
# are architecture-independent, so only the final runtime stage is built
# per TARGETPLATFORM by buildx.
#
# Build & publish multi-arch:
#   docker buildx build --platform linux/amd64,linux/arm64 \
#       -t ngochoaitn/gpm-login-global-private-server:latest --push .
#

# -----------------------------------------------------------------------------
# Stage 1 — Vue SPA bundle
# -----------------------------------------------------------------------------
FROM --platform=$BUILDPLATFORM node:20-alpine AS node-builder

WORKDIR /app

# Install FE deps first (cache-friendly)
COPY package.json package-lock.json* ./
RUN if [ -f package-lock.json ]; then \
        npm ci --no-audit --no-fund; \
    else \
        npm install --no-audit --no-fund; \
    fi

# Copy only what Vite needs
COPY vite.config.js tailwind.config.js postcss.config.js ./
COPY resources ./resources
COPY public ./public

RUN npm run build


# -----------------------------------------------------------------------------
# Stage 2 — PHP dependencies (composer)
#
# Pinned to PHP 8.2 (security-supported, Laravel 9 compatible). The 8.2.x tag
# always resolves to the latest patch release at build time, so a fresh
# `--no-cache` build picks up the PHP2Shell fix (>= 8.2.35, CVE-2026-109618 /
# CVE-2026-109619). It must stay <= 8.4 because composer.lock pins packages
# that require php <=8.4 (nette/schema, nette/utils); the official composer:*
# image ships PHP 8.5+ and refuses to resolve. See SECURITY_PATCH_PHP.vi.md.
# -----------------------------------------------------------------------------
FROM --platform=$BUILDPLATFORM php:8.2-cli-alpine AS composer-builder

RUN apk add --no-cache git unzip libzip-dev \
    && docker-php-ext-install zip
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /app

# Prime the package cache with composer metadata only (better layer caching)
COPY composer.json composer.lock ./
RUN composer install \
        --no-dev \
        --no-scripts \
        --no-autoloader \
        --prefer-dist \
        --no-interaction \
        --no-progress

# Copy the rest of the Laravel source and finalise the autoloader
COPY . .
RUN composer dump-autoload --no-dev --optimize --classmap-authoritative


# -----------------------------------------------------------------------------
# Stage 3 — Runtime (per-target PHP/Apache)
#
# php:8.2-apache (mod_php) — the 8.2.x tag resolves to the newest patch at
# build time. Rebuild with `--no-cache` and re-push to ship the PHP2Shell fix
# (>= 8.2.35) to every user running the published image. See
# SECURITY_PATCH_PHP.vi.md.
# -----------------------------------------------------------------------------
FROM php:8.2-apache AS runtime

# PHP native extensions + build tools (cleaned up in the same layer)
RUN apt-get update \
    && apt-get install -y --no-install-recommends \
        libpng-dev \
        libjpeg-dev \
        libfreetype6-dev \
        libzip-dev \
        zip \
        unzip \
        curl \
        git \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" gd pdo pdo_mysql exif fileinfo zip \
    && apt-get clean \
    && rm -rf /var/lib/apt/lists/*

WORKDIR /var/www/html

# Application code + composer-installed vendor/
COPY --from=composer-builder /app /var/www/html

# Overwrite/ensure public/build is the freshly compiled SPA bundle
COPY --from=node-builder /app/public/build /var/www/html/public/build

# Runtime config (Apache vhost, PHP ini, entrypoint)
COPY docker/apache/000-default.conf /etc/apache2/sites-available/000-default.conf
COPY docker/php/php_large_file_upload.ini /usr/local/etc/php/conf.d/php_large_file_upload.ini
COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh

RUN a2enmod rewrite \
    && chmod +x /usr/local/bin/entrypoint.sh \
    && mkdir -p \
        storage/app/public/profiles \
        storage/framework/cache \
        storage/framework/sessions \
        storage/framework/testing \
        storage/framework/views \
        storage/logs \
        bootstrap/cache \
    && chown -R www-data:www-data /var/www/html \
    && chmod -R 775 /var/www/html/storage /var/www/html/bootstrap/cache

EXPOSE 80

HEALTHCHECK --interval=30s --timeout=10s --start-period=40s --retries=3 \
    CMD curl -fsS http://localhost/ >/dev/null || exit 1

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]
CMD ["apache2-foreground"]
