
# Etapa 1: Build de assets con Node
FROM node:20-alpine AS assets
WORKDIR /app
COPY package*.json ./
RUN npm ci
COPY resources ./resources
COPY public ./public
COPY vite.config.js ./vite.config.js
RUN npm run build

# Etapa 2: Composer vendor
FROM composer:2 AS vendor
WORKDIR /app
COPY composer.json composer.lock ./
RUN composer install --no-dev --prefer-dist --no-interaction --optimize-autoloader --no-scripts

# Etapa 3: Imagen final PHP
FROM php:8.3-cli-alpine
WORKDIR /var/www/html

RUN apk add --no-cache \
    git \
    unzip \
    icu-dev \
    oniguruma-dev \
    libzip-dev \
    libpng-dev \
    libjpeg-turbo-dev \
    freetype-dev \
    libxml2-dev \
    && docker-php-ext-configure gd --with-freetype --with-jpeg \
    && docker-php-ext-install -j"$(nproc)" pdo_mysql mbstring bcmath exif gd intl zip pcntl

# Copiar composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

# Copiar vendor
COPY --from=vendor /app/vendor ./vendor
# Copiar todo el código fuente
COPY . .
# Copiar los assets generados
COPY --from=assets /app/public/build ./public/build

RUN mkdir -p storage/logs bootstrap/cache \
    && chown -R www-data:www-data storage bootstrap/cache

ENV APP_ENV=production
ENV APP_DEBUG=false

EXPOSE 10000

CMD ["sh", "-c", "php artisan config:clear && mkdir -p bootstrap/cache storage/app/public storage/framework/cache/data storage/framework/sessions storage/framework/views; php artisan storage:link || true; php artisan migrate --force || true; if [ \"${RUN_DEMO_SEED:-true}\" = \"true\" ]; then php artisan db:seed --force || true; elif [ \"${RUN_AUTH_SEED:-false}\" = \"true\" ]; then php artisan db:seed --class=Database\\Seeders\\AuthUsersSeeder --force || true; fi; php -S 0.0.0.0:${PORT:-10000} -t public public/index.php"]
