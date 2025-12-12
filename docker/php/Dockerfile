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

# Crear usuario con UID/GID 1000 para evitar problemas de permisos
RUN groupadd -g 1000 appuser && \
    useradd -u 1000 -g 1000 -m -s /bin/bash appuser && \
    usermod -aG www-data appuser && \
    mkdir -p /home/appuser/.composer && \
    chown -R appuser:appuser /home/appuser

WORKDIR /var/www/html

CMD ["php-fpm"]
