# ─── Stage 1: Node — compila assets ───────────────────────────────────────────
FROM node:22-alpine AS node-builder

WORKDIR /app

COPY package*.json ./
RUN npm ci --frozen-lockfile

COPY resources/ resources/
COPY vite.config.js tailwind.config.js postcss.config.js ./
COPY public/ public/

RUN npm run build


# ─── Stage 2: Composer — instala dependências PHP ─────────────────────────────
FROM composer:2.8 AS composer-builder

WORKDIR /app

COPY composer.json composer.lock ./
RUN composer install \
    --no-dev \
    --no-scripts \
    --no-autoloader \
    --prefer-dist \
    --optimize-autoloader

COPY . .
RUN composer dump-autoload --optimize --no-dev


# ─── Stage 3: Runtime — PHP-FPM + Nginx ───────────────────────────────────────
FROM php:8.4-fpm-alpine AS runtime

LABEL maintainer="Tallents RH"
LABEL org.opencontainers.image.title="Tallents RH"
LABEL org.opencontainers.image.description="Sistema de RH — Laravel 13"

# Dependências do sistema
RUN apk add --no-cache \
        nginx \
        supervisor \
        curl \
        libpng-dev \
        libjpeg-turbo-dev \
        freetype-dev \
        libwebp-dev \
        zip \
        libzip-dev \
        icu-dev \
        oniguruma-dev \
        mysql-client \
        shadow \
        sed \
    && rm -rf /var/cache/apk/*

# Extensões PHP
RUN docker-php-ext-configure gd \
        --with-freetype \
        --with-jpeg \
        --with-webp \
    && docker-php-ext-install -j$(nproc) \
        pdo_mysql \
        mbstring \
        exif \
        pcntl \
        bcmath \
        gd \
        zip \
        intl \
        opcache

# Extensão Redis (phpredis) — necessária para CACHE/SESSION/QUEUE via Redis
RUN apk add --no-cache --virtual .build-deps $PHPIZE_DEPS \
    && pecl install redis \
    && docker-php-ext-enable redis \
    && apk del .build-deps \
    && rm -rf /tmp/pear

# Configuração do PHP
COPY docker/php/php.ini     /usr/local/etc/php/conf.d/app.ini
COPY docker/php/opcache.ini /usr/local/etc/php/conf.d/opcache.ini
COPY docker/php/php-fpm.conf /usr/local/etc/php-fpm.d/www.conf

# Configuração do Nginx
COPY docker/nginx/nginx.conf   /etc/nginx/nginx.conf
COPY docker/nginx/default.conf /etc/nginx/http.d/default.conf

# Configuração do Supervisor
RUN mkdir -p /etc/supervisor/conf.d /var/log/supervisor
COPY docker/supervisor/supervisord.conf /etc/supervisord.conf

# Entrypoint
COPY docker/entrypoint.sh /entrypoint.sh
RUN chmod +x /entrypoint.sh

WORKDIR /var/www/html

# Copia o projeto
COPY --chown=www-data:www-data . .
COPY --chown=www-data:www-data --from=composer-builder /app/vendor ./vendor
COPY --chown=www-data:www-data --from=node-builder /app/public/build ./public/build

# Garante .env.example disponível para o entrypoint usar como fallback
RUN cp .env.example .env.example.bak

# Diretórios e permissões
RUN mkdir -p \
        storage/framework/{sessions,views,cache} \
        storage/logs \
        storage/app/public \
        bootstrap/cache \
        /var/lib/php/sessions \
    && chmod -R 775 storage bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache /var/lib/php/sessions

EXPOSE 80

HEALTHCHECK --interval=30s --timeout=10s --start-period=90s --retries=5 \
    CMD curl -sf http://localhost/up || exit 1

ENTRYPOINT ["/entrypoint.sh"]
CMD ["/usr/bin/supervisord", "-c", "/etc/supervisord.conf"]
