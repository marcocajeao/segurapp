FROM php:8.3-fpm

# Instalación de extensiones necesarias
RUN apt-get update && apt-get install -y \
    git \
    zip \
    unzip \
    libpng-dev \
    libonig-dev \
    libxml2-dev \
    libzip-dev \
    && docker-php-ext-install pdo_mysql mbstring exif pcntl bcmath gd zip \
    && apt-get clean && rm -rf /var/lib/apt/lists/*

# Instalar Composer
COPY --from=composer:2 /usr/bin/composer /usr/bin/composer

WORKDIR /var/www/html

# Opcional: usuario no root (si querés)
# RUN useradd -G www-data,root -u 1000 -d /home/appuser appuser \
#     && mkdir -p /home/appuser/.composer \
#     && chown -R appuser:appuser /home/appuser
# USER appuser

CMD ["php-fpm"]
