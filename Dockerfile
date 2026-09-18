# Stage 1: build React
FROM node:20-alpine AS react-builder
WORKDIR /app/react
COPY react/package*.json ./
RUN npm install
COPY react/ ./
RUN npm run build

# Stage 2: PHP app
FROM php:8.3-apache

RUN apt-get update && apt-get install -y --no-install-recommends \
        libonig-dev \
    && docker-php-ext-install pdo_mysql fileinfo mbstring \
    && a2enmod rewrite \
    && rm -rf /var/lib/apt/lists/*

COPY docker/apache-vhost.conf /etc/apache2/sites-available/000-default.conf
COPY . /var/www/html/

# Timpa app.js dengan hasil build terbaru dari stage 1
COPY --from=react-builder /app/react/../public/assets/js/app.js /var/www/html/public/assets/js/app.js

RUN mkdir -p /var/www/html/public/uploads/bukti /var/www/html/storage/logs \
    && chown -R www-data:www-data /var/www/html/public/uploads /var/www/html/storage

EXPOSE 80