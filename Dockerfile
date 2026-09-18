FROM php:8.3-apache

# Ekstensi PHP yang dibutuhkan DuitGW
RUN apt-get update && apt-get install -y --no-install-recommends \
        libonig-dev \
    && docker-php-ext-install pdo_mysql fileinfo mbstring \
    && a2enmod rewrite \
    && rm -rf /var/lib/apt/lists/*

# Arahkan document root Apache ke folder public/
COPY docker/apache-vhost.conf /etc/apache2/sites-available/000-default.conf

# Salin seluruh proyek (config/, app/, database/, public/ tetap satu struktur
# supaya path relatif __DIR__ di public/index.php dan config/config.php tetap benar)
COPY . /var/www/html/

# Siapkan folder yang perlu ditulis oleh PHP
RUN mkdir -p /var/www/html/public/uploads/bukti /var/www/html/storage/logs \
    && chown -R www-data:www-data /var/www/html/public/uploads /var/www/html/storage

EXPOSE 80
