# ==============================================================================
# Multi-Stage Dockerfile for Movie Ticket Booking API (Laravel 11 / PHP 8.3)
# Target 1: dev      -> Used by docker-compose for local development
# Target 2: render   -> Standalone production image for Render web service
# ==============================================================================

# ------------------------------------------------------------------------------
# Stage 1: Base PHP 8.3 with required PostgreSQL and Laravel extensions
# ------------------------------------------------------------------------------
FROM php:8.3-fpm-alpine AS base

# Install system dependencies
RUN apk add --no-cache \
    curl \
    git \
    libpq-dev \
    libzip-dev \
    icu-dev \
    oniguruma-dev \
    netcat-openbsd \
    bash

# Install PHP extensions
RUN docker-php-ext-configure pgsql -with-pgsql=/usr/include \
    && docker-php-ext-install -j$(nproc) \
        pdo \
        pdo_pgsql \
        pgsql \
        zip \
        bcmath \
        intl \
        opcache

# Install Composer
COPY --from=composer:2.7 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www

# ------------------------------------------------------------------------------
# Stage 2: Local Development
# ------------------------------------------------------------------------------
FROM base AS dev

COPY docker/entrypoint.sh /usr/local/bin/entrypoint.sh
RUN chmod +x /usr/local/bin/entrypoint.sh

EXPOSE 9000

ENTRYPOINT ["/usr/local/bin/entrypoint.sh"]

# ------------------------------------------------------------------------------
# Stage 3: Dependencies Builder for Production
# ------------------------------------------------------------------------------
FROM base AS vendor

WORKDIR /var/www

COPY composer.json composer.lock* ./
RUN composer install \
    --no-dev \
    --no-interaction \
    --prefer-dist \
    --no-scripts \
    --no-autoloader

COPY . .

RUN composer dump-autoload --optimize --no-dev

# ------------------------------------------------------------------------------
# Stage 4: Production Image for Render (Nginx + PHP-FPM Single Container)
# ------------------------------------------------------------------------------
FROM base AS render

# Install Nginx and envsubst
RUN apk add --no-cache nginx gettext

# Configure PHP production opcache
RUN { \
        echo 'opcache.memory_consumption=128'; \
        echo 'opcache.interned_strings_buffer=8'; \
        echo 'opcache.max_accelerated_files=10000'; \
        echo 'opcache.revalidate_freq=0'; \
        echo 'opcache.validate_timestamps=0'; \
        echo 'opcache.enable_cli=1'; \
    } > /usr/local/etc/php/conf.d/opcache-recommended.ini

# Copy application source code and optimized vendor directory
WORKDIR /var/www
COPY --from=vendor /var/www /var/www

# Set proper ownership and permissions for web server
RUN chown -R www-data:www-data /var/www/storage /var/www/bootstrap/cache \
    && chmod -R 775 /var/www/storage /var/www/bootstrap/cache

# Copy Nginx template and Render entrypoint
COPY docker/render-nginx.conf.template /etc/nginx/templates/default.conf.template
COPY docker/render-entrypoint.sh /usr/local/bin/render-entrypoint.sh
RUN chmod +x /usr/local/bin/render-entrypoint.sh

# Default port for Render
ENV PORT=10000
EXPOSE 10000

ENTRYPOINT ["/usr/local/bin/render-entrypoint.sh"]

